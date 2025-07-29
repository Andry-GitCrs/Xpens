<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['purchase_date'])) {
  $date = $_GET['purchase_date'];
  $user_id = $_SESSION['user']['id'];
  if (trim($date) === "") {
    http_response_code(400);
    echo json_encode(['message' => 'param purchase_date cannot be empty']);
    exit;
  } if (!is_string($date)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input. Date must be a string.']);
    exit;
  }

  $parsedDate = DateTime::createFromFormat('d-m-Y', $date);
  $errors = DateTime::getLastErrors();

  if (!$parsedDate || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid date format or value. Expected format: dd-mm-yyyy.']);
    exit;
  }

  // Convert to Y-m-d format for SQL
  $sqlDate = $parsedDate->format('Y-m-d');

  $stmt = $pdo->prepare("
    SELECT purchases.* FROM purchases
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
  echo json_encode([
    "date" => $date,
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
  ]);

} else {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
}