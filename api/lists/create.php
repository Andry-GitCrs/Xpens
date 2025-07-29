<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);
    // Validate required fields
    $list_name = trim($data['list_name']) ?? null;
    $user_id = $_SESSION['user']['id'] ?? null;

    if(empty($list_name) || !is_string($list_name)) {
      http_response_code(400);
      echo json_encode(['message' => 'Invalid or missing "list_name". Must be a non-empty string.']);
      exit;
    }

    // Check if the user exist
    $existingUserCheck = $pdo->prepare("SELECT * FROM users WHERE id_user = :user_id AND is_active = 1");
    $existingUserCheck->bindParam(':user_id', $user_id);
    $existingUserCheck->execute();

    if ($existingUserCheck->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['message' => 'User with the specified id does not exist']);
        exit;
    }

    // Check if the list already exist
    $existingListCheck = $pdo->prepare("SELECT * FROM lists WHERE id_list = :list_id AND is_active = 1 AND user_id = :user_id");
    $existingListCheck->bindParam(':list_id', $list_id);
    $existingListCheck->bindParam(':user_id', $user_id);
    $existingListCheck->execute();

    if ($existingListCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['message' => 'List with the specified name already exist']);
        exit;
    }
    
    $description = isset($data['description']) ? trim($data['description']) : 'No description';

    // Insert into lists
    $stmt = $pdo->prepare("
        INSERT INTO lists 
          (list_name, description, user_id) 
        VALUES 
          (:list_name, :description, :user_id)
    ");

    $stmt->bindParam(':list_name', $list_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        $id = $pdo->lastInsertId();

        // Fetch inserted row to return (including generated total_price, timestamps)
        $fetchStmt = $pdo->prepare("SELECT * FROM lists WHERE id_list = :id");
        $fetchStmt->bindParam(':id', $id);
        $fetchStmt->execute();
        $list = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        echo json_encode([
            'message' => 'List created successfully',
            'data' => $list
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to create list']);
    }


} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}