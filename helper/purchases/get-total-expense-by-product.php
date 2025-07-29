<?php
function get_total_expense_by_product($product_id, $pdo) {

  $productCheck = $pdo->prepare("SELECT * FROM products WHERE id_product = :product_id AND is_active = 1");
  $productCheck->bindParam(':product_id', $product_id);
  $productCheck->execute();
  if ($productCheck->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['message' => 'Product not found']);
    exit;
  }

  $stmt = $pdo->prepare("
    SELECT SUM(purchases.total_price) AS total_price
    FROM purchases 
      JOIN products ON purchases.product_id = products.id_product
      JOIN lists ON purchases.list_id = lists.id_list
      JOIN users ON lists.user_id = users.id_user
    WHERE 
      purchases.is_active = 1 AND
      users.id_user = :user_id AND
      purchases.product_id = :product_id
  ");
  $stmt->bindParam(':user_id', $_SESSION['user']['id']);
  $stmt->bindParam(':product_id', $product_id);
  $stmt->execute();
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  return [
    'product_name' => $productCheck->fetch(PDO::FETCH_ASSOC)['product_name'],
    'total_expense' => $data['total_price'] ? $data['total_price'] : 0
  ];
}