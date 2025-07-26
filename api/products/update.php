<?php 
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input, id is required and must be numeric']);
        exit;
    }

    $id = trim($_GET['id']);

    // Fetch existing product
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id_product = :id AND stat = 1");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

    // Prepare update fields: use existing values as fallback
    $product_name = isset($data['product_name']) ? trim($data['product_name']) : $existingProduct['product_name'];
    $price = isset($data['price']) ? trim($data['price']) : $existingProduct['price'];
    $purchase_id = isset($data['purchase_id']) ? trim($data['purchase_id']) : $existingProduct['purchase_id'];

    // Validate partial inputs
    if (empty($product_name) || !is_numeric($price) || !is_numeric($purchase_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input: product_name must not be empty, price and purchase_id must be numeric']);
        exit;
    }

    // Check if another product with same name and purchase_id exists (excluding current)
    $existingProductCheck = $pdo->prepare("SELECT * FROM products WHERE product_name = :product_name AND stat = 1 AND purchase_id = :purchase_id AND id_product != :id");
    $existingProductCheck->bindParam(':product_name', $product_name);
    $existingProductCheck->bindParam(':purchase_id', $purchase_id);
    $existingProductCheck->bindParam(':id', $id);
    $existingProductCheck->execute();

    if ($existingProductCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Product already exists in this purchase, update it instead']);
        exit;
    }

    // Perform update with merged data
    $updateStmt = $pdo->prepare("UPDATE products SET product_name = :product_name, price = :price, purchase_id = :purchase_id WHERE id_product = :id");
    $updateStmt->bindParam(':product_name', $product_name);
    $updateStmt->bindParam(':price', $price);
    $updateStmt->bindParam(':purchase_id', $purchase_id);
    $updateStmt->bindParam(':id', $id);

    if ($updateStmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'message' => 'Product updated successfully',
            'data' => [
                'id_product' => $id,
                'product_name' => $product_name,
                'price' => $price,
                'purchase_id' => $purchase_id
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update product']);
    }

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
