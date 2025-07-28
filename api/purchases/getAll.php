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

  if (isset($_GET['product_id']) && isset($_GET['get_product_total_price'])) {

    if ($_GET['product_id'] == "" || $_GET['get_product_total_price'] == "") {
      http_response_code(400);
      echo json_encode(['message' => 'Missing product_id or get_product_total_price']);
      exit;
    } elseif (!is_numeric($_GET['get_product_total_price'])) {
      http_response_code(400);
      echo json_encode(['message' => 'get_product_total_price must be numeric']);
      exit;
    } elseif (!is_numeric($_GET['product_id'])) {
      http_response_code(400);
      echo json_encode(['message' => 'product_id must be numeric']);
      exit;
    }

    if ($_GET['get_product_total_price'] === "1") {
      require_once 'getTotalPrice.php';
      $product_id = trim($_GET['product_id']);
      $data = getTotalPrice($product_id, $pdo);
      echo json_encode($data);
      exit;
    }else {
      http_response_code(400);
      echo json_encode(['message' => 'get_product_total_price must be 1, if you want to get the total price of a product']);
      exit;
    }

  } elseif (isset($_GET['product_id'])) {
    require_once 'getAllByProduct.php';
    $product_id = trim($_GET['product_id']);
    $user_id = $_SESSION['user']['id'];
    $data = getAllByProduct($product_id, $user_id, $pdo);
    echo json_encode($data);
    exit;
  } elseif (isset($_GET['get_product_total_price'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing product_id']);
    exit;
  }

  if (isset($_GET['purchase_date']) && is_string($_GET['purchase_date']) && trim($_GET['purchase_date']) != "" && isset($_GET['get_total'])) {
    if (!is_numeric($_GET['get_total'])) {
      http_response_code(400);
      echo json_encode(['message' => 'get_total must be numeric']);
      exit;
    } elseif ($_GET['get_total'] === "1") {
      require_once 'getTotalByDate.php';
      $date =$_GET['purchase_date'];
      $user_id = $_SESSION['user']['id'];
      $data = getTotalByDate($date, $user_id, $pdo);
      http_response_code(200);
      echo json_encode($data);
      exit;
    } else {
      http_response_code(400);
      echo json_encode(['message' => 'get_total must be 1, if you want to get the total price of a product by date']);
      exit;
    }
    exit;
  } elseif (isset($_GET['purchase_date']) && (!is_string($_GET['purchase_date']) || trim($_GET['purchase_date']) === "")) {
    http_response_code(400);
    echo json_encode(['message' => 'purchase_date must be a string']);
    exit;
  }

  if (isset($_GET['list_id']) && is_numeric($_GET['list_id'])) {
    $id_list = trim($_GET['list_id']);
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