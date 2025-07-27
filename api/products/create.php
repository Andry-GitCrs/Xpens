<?php
require_once '../../config/db.php';
require_once '../../helper/auth/index.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['message' => 'User not authenticated']);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['product_name'])) {
        // Validate input
        $data['product_name'] = trim($data['product_name']);

        if (empty($data['product_name'] ) || !is_string($data['product_name'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid input, product_name must be a string']);
            exit;
        }

        $existingProductCheck = $pdo->prepare("SELECT * FROM products WHERE product_name = :product_name AND is_active = 1");
        // Check if product already exists
        $existingProductCheck->bindParam(':product_name', $data['product_name']);
        $existingProductCheck->execute();
        if ($existingProductCheck->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(['message' => 'Product already exists']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO products (product_name) VALUES (:product_name)");
        $stmt->bindParam(':product_name', $data['product_name']);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
              'message' => 'Product created successfully',
              'data' => [
                'id' => $pdo->lastInsertId(),
                'product_name' => $data['product_name'],
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'updated_at' => null,
              ]
            ]);

        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create product']);
        }

    } else {
        http_response_code(400);
        echo json_encode([
          'message' => 'Invalid input, product_name is required'
        ]);

    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}