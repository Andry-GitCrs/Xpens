<?php
require_once '../../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

  if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['message' => 'User not authenticated']);
    exit;
  }
  
  $user = $_SESSION['user'];
  session_unset();
  session_destroy();
  echo json_encode([
      'message' =>  $user['username'] . ' logged out successfully',
  ]);

} else {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
}

