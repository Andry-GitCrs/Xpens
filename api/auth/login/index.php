<?php
require_once '../../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

    if(isset($_SESSION['user'])) {
      http_response_code(401);
      echo json_encode([
        'message' => 'User ' . $_SESSION['user']['username'] . ' is already logged in. Please log out first or enter.'
      ]);
      exit;
    }

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

    // Check if the user with the username exist
    $existingUsernameCheck = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $existingUsernameCheck->bindParam(':username', $username);
    $existingUsernameCheck->execute();

    if ($existingUsernameCheck->rowCount() === 0) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials, user not registered']);
        exit;
    }

    $user = $existingUsernameCheck->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($password, $user['password'])) {
      http_response_code(401);
      echo json_encode(['message' => 'Wrong password']);
      exit;
    }

    // Store user data in session
    $_SESSION['user'] = [
        'id' => $user['id_user'],
        'username' => $user['username'],
        'email' => $user['email'],
        'created_at' => $user['created_at'],
        'updated_at' => $user['updated_at']
    ];

    http_response_code(200);
    echo json_encode([
      'message' => 'User ' . $user['username'] . ' connected successfully',
    ]);

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}