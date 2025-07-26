<?php
require_once '../../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    // Validate required fields
    $username = trim($data['username']) ?? null;
    $password = $data['password'] ?? null;

    if(empty($username) || !is_string($username)) {
      http_response_code(400);
      echo json_encode(['message' => 'Invalid or missing "username". Must be a non-empty string.']);
      exit;
    }
    if(empty($password) || !is_string($password)) {
      http_response_code(400);
      echo json_encode(['message' => 'Invalid or missing "password". Must be a non-empty string.']);
      exit;
    }

    // Check if the user with the username already exist
    $existingUsernameCheck = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $existingUsernameCheck->bindParam(':username', $username);
    $existingUsernameCheck->execute();

    if ($existingUsernameCheck->rowCount() === 0) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
        exit;
    }

    $user = $existingUsernameCheck->fetch(PDO::FETCH_ASSOC);

    if ($password !== $user['password']) {
      http_response_code(401);
      echo json_encode(['message' => 'Wrong password']);
      exit;
    }

    http_response_code(200);
    echo json_encode([
      'message' => 'User connected successfully',
      'data' => $user,
      'token' => ''
    ]);

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}