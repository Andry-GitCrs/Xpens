<?php
require_once '../../config/db.php';
require_once '../../helper/auth/index.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {

  if (!isAuthenticated()) {
      http_response_code(401);
      echo json_encode(['message' => 'User not authenticated']);
      exit;
  }

  if (isset($_GET['list_id']) && is_numeric($_GET['list_id'])) {
    $id_list = trim($_GET['list_id']);
    $user_id = $_SESSION['user']['id'];

    // Check if purchase exists
    $stmt = $pdo->prepare("
      SELECT 
        purchases.id_purchase,
        purchases.description,
        purchases.purchase_date,
        purchases.number,
        purchases.unit_price,
        purchases.unit,
        purchases.total_price,
        products.product_name,
        purchases.created_at,
        purchases.updated_at,
        lists.list_name
      FROM purchases 
        JOIN products ON purchases.product_id = products.id_product
        JOIN lists ON purchases.list_id = lists.id_list
        JOIN users ON lists.user_id = users.id_user
      WHERE 
        purchases.is_active = 1 AND
        users.id_user = :user_id AND
        lists.id_list = :id_list
    ");
    $stmt->bindParam(':id_list', $id_list);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
    exit;
  }

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
      lists.user_id = :user_id
  ");
  $stmt->bindParam(':user_id', $user_id);
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($data);

}
else {
  http_response_code(405);
  echo json_encode(array("message" => "Method not allowed"));
  
}