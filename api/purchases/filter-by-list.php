<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET["list_id"])) {
  $id_list = trim($_GET['list_id']);

  // Check if the list exist
  $existingListCheck = $pdo->prepare("SELECT * FROM lists WHERE id_list = :list_id AND is_active = 1");
  $existingListCheck->bindParam(':list_id', $id_list);
  $existingListCheck->execute();

  if ($existingListCheck->rowCount() === 0) {
      http_response_code(404);
      echo json_encode(['message' => 'The specified list is not found']);
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

} else {
  http_response_code(405);
  echo json_encode(["message" => "Method not allowed"]);
}