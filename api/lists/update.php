<?php 
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT') {

    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user']['id'] ?? null;
    
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input, id is required and must be numeric']);
        exit;
    }

    $id = trim($_GET['id']);

    // Fetch existing list
    $stmt = $pdo->prepare("SELECT * FROM lists WHERE id_list = :id AND is_active = 1 AND user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['message' => 'List not found']);
        exit;
    }

    $existingList = $stmt->fetch(PDO::FETCH_ASSOC);

    // Prepare update fields: use existing values as fallback
    $list_name = isset($data['list_name']) ? trim($data['list_name']) : $existingList['list_name'];
    $description = isset($data['description']) ? trim($data['description']) : $existingList['description'];

    // Validate partial inputs
    if (empty($list_name) || !is_string($list_name)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input: list_name must not be empty and must be a string']);
        exit;
    }
    if (empty($description) || !is_string($description)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input: description must not be empty and must be a string']);
        exit;
    }

    // Perform update with merged data
    $updateStmt = $pdo->prepare("UPDATE lists SET list_name = :list_name, description = :description WHERE id_list = :id");
    $updateStmt->bindParam(':list_name', $list_name);
    $updateStmt->bindParam(':description', $description);
    $updateStmt->bindParam(':id', $id);

    if ($updateStmt->execute()) {
        http_response_code(200);
        echo json_encode([  
            'message' => 'List updated successfully',
            'data' => [
                'id_list' => $id,
                'list_name' => $list_name,
                'description' => $description,
                'is_active' => 1,
                'user_id'=> $existingList['user_id'],
                'created_at' => $existingList['created_at'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to update product']);
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
