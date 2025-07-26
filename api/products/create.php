<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['product_name']) && isset($data['price']) && isset($data['purchase_id'])) {
        // Validate input
        $data['product_name'] = trim($data['product_name']);
        $data['price'] = trim($data['price']);
        $data['purchase_id'] = trim($data['purchase_id']);

        if (empty($data['product_name']) || !is_numeric($data['price']) || !is_numeric($data['purchase_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input, product_name must be a string, price and purchase_id must be numeric']);
            exit;
        }

        $existingProductCheck = $pdo->prepare("SELECT * FROM products WHERE product_name = :product_name AND stat = 1 AND purchase_id = :purchase_id");
        // Check if product already exists
        $existingProductCheck->bindParam(':product_name', $data['product_name']);
        $existingProductCheck->bindParam(':purchase_id', $data['purchase_id']);
        $existingProductCheck->execute();
        if ($existingProductCheck->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'Product already exists in this purchase, update it instead']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO products (product_name, price, purchase_id) VALUES (:product_name, :price, :purchase_id)");
        $stmt->bindParam(':product_name', $data['product_name']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':purchase_id', $data['purchase_id']);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
              'message' => 'Product created successfully',
              'data' => [
                'id' => $pdo->lastInsertId(),
                'product_name' => $data['product_name'],
                'price' => $data['price'],
                'purchase_id' => $data['purchase_id']
              ]
            ]);

        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create product']);
        }

    } else {
        http_response_code(400);
        echo json_encode([
          'error' => 'Invalid input, product_name, price, and purchase_id are required'
        ]);

    }

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}