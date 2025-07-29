<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'DELETE') {

  if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = trim($_GET['id']);

    // Check if product exists
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id_product = :id AND is_active = 1");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
      http_response_code(404);
      echo json_encode(['message' => 'Product not found']);
      exit;

    }

    $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id_product = :id");
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
      http_response_code(200);
      echo json_encode(['message' => 'Product deleted successfully']);

    } else {
      http_response_code(500);
      echo json_encode(['message' => 'Failed to delete product']);

    }
  } else {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input, id is required and must be numeric']);

  }
} else {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
}