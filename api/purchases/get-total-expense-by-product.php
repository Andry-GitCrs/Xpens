<?php
require_once '../../config/db.php';
require_once '../../helper/purchases/get-total-expense-by-product.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['product_id']) && isset($_GET['get_total_expense'])) {
  $product_id = trim($_GET['product_id']);
  $get_total_expense = trim($_GET['get_total_expense']);

  if ($product_id == "" || $get_total_expense == "") {
    http_response_code(400);
    echo json_encode(['message' => 'Missing product_id or get_total_expense']);
    exit;
  } elseif (!is_numeric($get_total_expense)) {
    http_response_code(400);
    echo json_encode(['message' => 'get_total_expense must be numeric']);
    exit;
  } elseif (!is_numeric($product_id)) {
    http_response_code(400);
    echo json_encode(['message' => 'product_id must be numeric']);
    exit;
  }

  if ($get_total_expense === "1") {
    $data = get_total_expense_by_product($product_id, $pdo);
    echo json_encode($data);
    exit;
  } else {
    http_response_code(400);
    echo json_encode(['message' => 'get_total_expense must be 1, if you want to get the total price of a product']);
    exit;
  }

} else {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
}