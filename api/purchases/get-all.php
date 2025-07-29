<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $user_id = $_SESSION['user']['id'];
  $stmt = $pdo->prepare("
    SELECT 
      purchases.id_purchase,
      purchases.description,
      purchases.purchase_date,
      purchases.number,
      purchases.unit_price,
      purchases.unit,
      purchases.total_price,
      purchases.created_at,
      purchases.updated_at,
      products.product_name,
      lists.list_name
    FROM purchases 
      JOIN products ON purchases.product_id = products.id_product
      JOIN lists ON purchases.list_id = lists.id_list 
      JOIN users ON lists.user_id = users.id_user 
    WHERE 
      purchases.is_active = 1 AND
      lists.is_active = 1 AND
      lists.user_id = :user_id
  ");
  $stmt->bindParam(':user_id', $user_id);
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($data);

}
else {
  http_response_code(405);
  echo json_encode(["message" => "Method not allowed"]);
  
}