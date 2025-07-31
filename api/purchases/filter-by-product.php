<?php
require_once '../../config/db.php';
require_once '../../helper/purchases/get-all-by-product.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['product_id'])) {
  $product_id = trim($_GET['product_id']);

  if (empty($product_id) || !is_numeric($product_id)) {
    http_response_code(400);
    echo json_encode(['message' => 'product_id must be numeric']);
    exit;
  }

  $data = getAllByProduct($product_id, $_SESSION['user']['id'], $pdo);
  echo json_encode($data);

} else {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
}