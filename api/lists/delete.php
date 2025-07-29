<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'DELETE') {

  if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = trim($_GET['id']);
    $user_id = $_SESSION['user']['id'];

    // Check if list exists
    $stmt = $pdo->prepare("SELECT * FROM lists WHERE id_list = :id AND is_active = 1 AND user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
      http_response_code(404);
      echo json_encode(['message' => 'List not found']);
      exit;

    }

    $stmt = $pdo->prepare("UPDATE lists SET is_active = 0 WHERE id_list = :id");
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
      http_response_code(200);
      echo json_encode(['message' => 'List deleted successfully']);

    } else {
      http_response_code(500);
      echo json_encode(['message' => 'Failed to delete list']);

    }
  } else {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input, id is required and must be numeric']);

  }
} else {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
}