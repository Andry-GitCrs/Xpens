<?php
require_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['start_date'], $_GET['end_date'], $_GET['get_total_expense'])) {

  if ($_GET['get_total_expense'] !== "1") {
    http_response_code(400);
    echo json_encode(['message' => 'get_total_expense must be 1, if you want to get the total expense of a product']);
    exit;
  }

  $user_id = $_SESSION['user']['id'];
  $start_date = $_GET['start_date'];
  $end_date = $_GET['end_date'];

  $parsedStart = DateTime::createFromFormat('d-m-Y', $start_date);
  $parsedEnd = DateTime::createFromFormat('d-m-Y', $end_date);

  // Vérification du format
  if (!$parsedStart || !$parsedEnd) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid date format. Expected dd-mm-yyyy.']);
    exit;
  }

  // Vérification logique
  if ($parsedStart > $parsedEnd) {
    http_response_code(400);
    echo json_encode(['message' => 'Start date cannot be after end date.']);
    exit;
  }

  $sqlStart = $parsedStart->format('Y-m-d');
  $sqlEnd = $parsedEnd->format('Y-m-d');

  $stmt = $pdo->prepare("
    SELECT 
      SUM(purchases.total_price) AS total_expense
    FROM purchases
      JOIN products ON purchases.product_id = products.id_product
      JOIN lists ON purchases.list_id = lists.id_list
      JOIN users ON lists.user_id = users.id_user
    WHERE 
      DATE(purchases.purchase_date) BETWEEN :start AND :end AND
      purchases.is_active = 1 AND
      lists.user_id = :user_id
  ");

  $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindParam(':start', $sqlStart);
  $stmt->bindParam(':end', $sqlEnd);
  $stmt->execute();

  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  echo json_encode([
    'start_date' => $start_date,
    'end_date' => $end_date,
    'total_expense' => $data['total_expense'] ?? 0
  ]);
} else {
  http_response_code(405);
  echo json_encode(["message" => "Method not allowed"]);
}
