<?php
require_once '../../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    // Validate required fields
    $username = trim($data['username']) ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if(empty($username) || !is_string($username)) {
      http_response_code(400);
      echo json_encode(['message' => 'Invalid or missing "username". Must be a non-empty string.']);
      exit;
    }
    if(empty($email) || !is_string($email)) {
      http_response_code(400);
      echo json_encode(['message' => 'Invalid or missing "email". Must be a non-empty string.']);
      exit;
    }
    if(empty($password) || !is_string($password)) {
      http_response_code(400);
      echo json_encode(['message' => 'Invalid or missing "password". Must be a non-empty string.']);
      exit;
    }

    // Check if the user with the username already exist
    $existingUsernameCheck = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $existingUsernameCheck->bindParam(':username', $username);
    $existingUsernameCheck->execute();

    if ($existingUsernameCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['message' => 'User with this username already exist']);
        exit;
    }

    // Check if the user with the username already exist
    $existingEmailCheck = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $existingEmailCheck->bindParam(':email', $email);
    $existingEmailCheck->execute();

    if ($existingEmailCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['message' => 'User with this email already exist']);
        exit;
    }

    // Hash user password
    // $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users
    $stmt = $pdo->prepare("
        INSERT INTO users 
          (username, email, password) 
        VALUES 
          (:username, :email, :password)
    ");

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        $id = $pdo->lastInsertId();

        // Fetch inserted row to return (including generated total_price, timestamps)
        $fetchStmt = $pdo->prepare("SELECT * FROM users WHERE id_user = :id");
        $fetchStmt->bindParam(':id', $id);
        $fetchStmt->execute();
        $user = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        echo json_encode([
            'message' => 'User registration successfull',
            'data' => $user
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to create user']);
    }


} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}