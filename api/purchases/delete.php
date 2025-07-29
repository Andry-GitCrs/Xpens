<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'DELETE') {

  if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = trim($_GET['id']);
    $user_id = $_SESSION['user']['id'];

    // Check if purchase exists
    $stmt = $pdo->prepare("
      SELECT * FROM purchases 
        JOIN lists ON purchases.list_id = lists.id_list
        JOIN users ON lists.user_id = users.id_user
      WHERE 
        purchases.id_purchase = :id AND
        purchases.is_active = 1 AND
        users.id_user = :user_id
    ");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
      http_response_code(404);
      echo json_encode(['message' => 'Purchase not found']);
      exit;

    }

    $stmt = $pdo->prepare("UPDATE purchases SET is_active = 0 WHERE id_purchase = :id");
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
      http_response_code(200);
      echo json_encode(['message' => 'Purchase deleted successfully']);

    } else {
      http_response_code(500);
      echo json_encode(['message' => 'Failed to delete purchase']);

    }
  } else {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input, id is required and must be numeric']);

  }
} else {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
}