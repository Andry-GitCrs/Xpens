<?php
function getTotalByDate($date, $user_id, $pdo) {
  // Convert input from dd-mm-yyyy to Y-m-d (MySQL compatible)
  $parsedDate = DateTime::createFromFormat('d-m-Y', $date);
  if (!$parsedDate) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid date format. Expected dd-mm-yyyy.']);
    exit;
  }
  $sqlDate = $parsedDate->format('Y-m-d');

  $stmt = $pdo->prepare("
    SELECT 
      SUM(purchases.total_price) AS total_price
    FROM purchases
      JOIN products ON purchases.product_id = products.id_product
      JOIN lists ON purchases.list_id = lists.id_list
      JOIN users ON lists.user_id = users.id_user
    WHERE 
      DATE(purchases.purchase_date) = :date AND
      purchases.is_active = 1 AND
      lists.user_id = :user_id
  ");
  $stmt->bindParam(':user_id', $user_id);
  $stmt->bindParam(':date', $sqlDate);
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return [
    'date' => $date,
    'total_price' => $data[0]['total_price'] ? $data[0]['total_price'] : 0
  ];
}
