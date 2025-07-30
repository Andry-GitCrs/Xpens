<?php
require_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['get_total_expense'])) {

  if ($_GET['get_total_expense'] !== "1") {
    http_response_code(400);
    echo json_encode(['message' => 'get_total_expense must be 1, if you want to get the total expense']);
    exit;
  }

  $user_id = $_SESSION['user']['id'];
  // Fetch total expense
  $stmtTotal = $pdo->prepare("
    SELECT 
      SUM(purchases.total_price) AS total_expense
    FROM purchases
      JOIN products ON purchases.product_id = products.id_product
      JOIN lists ON purchases.list_id = lists.id_list
      JOIN users ON lists.user_id = users.id_user
    WHERE 
      purchases.is_active = 1 AND
      lists.is_active = 1 AND
      lists.user_id = :user_id
  ");
  $stmtTotal->bindParam(':user_id', $user_id);
  $stmtTotal->execute();
  $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);
  echo json_encode([
    "user" => $_SESSION['user'],
    "total_expense" => $total['total_expense'] ? $total['total_expense'] : 0
  ]);
  
} else {
  http_response_code(405);
  echo json_encode(["message" => "Method not allowed"]);
}
