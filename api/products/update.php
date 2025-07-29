<?php 
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT') {

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input, id is required and must be numeric']);
        exit;
    }

    $id = trim($_GET['id']);

    // Fetch existing product
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id_product = :id AND is_active = 1");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['message' => 'Product not found']);
        exit;
    }

    $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

    // Prepare update fields: use existing values as fallback
    $product_name = isset($data['product_name']) ? trim($data['product_name']) : $existingProduct['product_name'];

    // Validate partial inputs
    if (empty($product_name) || !is_string($product_name)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input: product_name must not be empty and must be a string']);
        exit;
    }

    // Check if another product with same name and purchase_id exists (excluding current)
    $existingProductCheck = $pdo->prepare("SELECT * FROM products WHERE product_name = :product_name AND is_active = 1 AND id_product != :id");
    $existingProductCheck->bindParam(':product_name', $product_name);
    $existingProductCheck->bindParam(':id', $id);
    $existingProductCheck->execute();

    if ($existingProductCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['message' => 'This product already exists']);
        exit;
    }

    // Perform update with merged data
    $updateStmt = $pdo->prepare("UPDATE products SET product_name = :product_name WHERE id_product = :id");
    $updateStmt->bindParam(':product_name', $product_name);
    $updateStmt->bindParam(':id', $id);

    if ($updateStmt->execute()) {
        http_response_code(200);
        echo json_encode([  
            'message' => 'Product updated successfully',
            'data' => [
                'id_product' => $id,
                'product_name' => $product_name,
                'is_active' => 1,
                'created_at' => $existingProduct['created_at'],
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
