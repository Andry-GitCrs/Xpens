<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input, param id is required and must be numeric']);
        exit;
    }

    $id = trim($_GET['id']);

    // Fetch existing purchase
    $stmt = $pdo->prepare("SELECT * FROM purchases WHERE id_purchase = :id AND is_active = 1");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['message' => 'Purchase not found']);
        exit;
    }

    $existingPurchase = $stmt->fetch(PDO::FETCH_ASSOC);

    $description = isset($data['description']) ? trim($data['description']) : $existingPurchase['description'];
    $number = isset($data['number']) ? trim($data['number']) : $existingPurchase['number'];
    $unit = isset($data['unit']) ? trim($data['unit']) : $existingPurchase['unit'];
    $unit_price = isset($data['unit_price']) ? trim($data['unit_price']) : $existingPurchase['unit_price'];
    $product_id = isset($data['product_id']) ? trim($data['product_id']) : $existingPurchase['product_id'];
    $list_id = isset($data['list_id']) ? trim($data['list_id']) : $existingPurchase['list_id'];
    $is_active = isset($data['is_active']) ? trim($data['is_active']) : $existingPurchase['is_active'];
    $purchase_date = isset($data['purchase_date']) ? trim($data['purchase_date']) : $existingPurchase['purchase_date'];
    if ($purchase_date !== null ) {
        if (!is_string($purchase_date)) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid "purchase_date". Must be a string.']);
            exit;
        }

        $date = DateTime::createFromFormat('d-m-Y H:i:s', $purchase_date);
        $errors = DateTime::getLastErrors();

        if (!$date || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid "purchase_date". Must be in format dd-mm-yyyy HH:ii and valid.']);
            exit;
        }

        // Convert to SQL format
        $sqlFormattedDate = $date->format('Y-m-d H:i:s');
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid or missing "purchase_date". Must be in format dd-mm-yyyy HH:ii and valid.']);
        exit;
    }

    // Validate partial inputs
    if ($number === null || !is_numeric($number) || $number <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid or missing "number". Must be a positive number.']);
        exit;
    }
    if (empty($description) || !is_string($description)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid or missing "description". Must be a non-empty string.']);
        exit;
    }
    if (empty($unit) || !is_string($unit)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid or missing "unit". Must be a non-empty string.']);
        exit;
    }
    if ($unit_price === null || !is_numeric($unit_price) || $unit_price < 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid or missing "unit_price". Must be a non-negative number.']);
        exit;
    }
    if ($list_id === null || !is_numeric($list_id) || $list_id < 0 ) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid or missing "list_id". Must be a non-negative number.']);
      exit;
    }
    if ($product_id === null || !is_numeric($product_id) || $product_id < 0 ) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid or missing "product_id". Must be a non-negative number.']);
      exit;
    }

    // Check if the list exist
    $existingListCheck = $pdo->prepare("SELECT * FROM lists WHERE id_list = :list_id AND is_active = 1");
    $existingListCheck->bindParam(':list_id', $list_id);
    $existingListCheck->execute();

    if ($existingListCheck->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['message' => 'The specified list is not found']);
        exit;
    }
    
    // Check if the product exist
    $existingProductCheck = $pdo->prepare("SELECT * FROM products WHERE id_product = :product_id AND is_active = 1");
    $existingProductCheck->bindParam(':product_id', $product_id);
    $existingProductCheck->execute();

    if ($existingProductCheck->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['message' => 'The specified product is not found']);
        exit;
    }
    // Check if purchase already exists
    $existingPurchaseCheck = $pdo->prepare("SELECT * FROM purchases WHERE list_id = :list_id AND product_id = :product_id AND is_active = 1 AND id_purchase != :id");
    $existingPurchaseCheck->bindParam(':list_id', $list_id); 
    $existingPurchaseCheck->bindParam(':product_id', $product_id);
    $existingPurchaseCheck->bindParam(':id', $purchase_id);
    $existingPurchaseCheck->execute();
    if ($existingPurchaseCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['message' => 'Purchase already exists, update it instead']);
        exit;
    }

    // Perform update with merged data
    $updateStmt = $pdo->prepare("UPDATE purchases SET description = :description, number = :number, unit = :unit, unit_price = :unit_price, list_id = :list_id, product_id = :product_id, purchase_date = :purchase_date WHERE id_purchase = :id");
    $updateStmt->bindParam(':description', $description);
    $updateStmt->bindParam(':number', $number);
    $updateStmt->bindParam(':unit', $unit);
    $updateStmt->bindParam(':unit_price', $unit_price);
    $updateStmt->bindParam(':purchase_date', $sqlFormattedDate);
    $updateStmt->bindParam(':list_id', $list_id);
    $updateStmt->bindParam(':product_id', $product_id);    
    $updateStmt->bindParam(':id', $id);

    if ($updateStmt->execute()) {

        $updated_at = date('Y-m-d H:i:s');

        // Fetch updated row to return (including generated total_price, timestamps)
        $fetchStmt = $pdo->prepare("SELECT * FROM purchases WHERE id_purchase = :id");
        $fetchStmt->bindParam(':id', $id);
        $fetchStmt->execute();
        $purchase = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([  
            'message' => 'Purchase updated successfully',
            'data' => [
                'id_purchase' => $id,
                'description' => $description,
                'number' => $number,
                'unit' => $unit,
                'unit_price' => $unit_price,
                'total_price' => $purchase['total_price'],
                'purchase_date' => $purchase_date,
                'created_at' => $purchase['created_at'],
                'updated_at' => $updated_at,
                'list_id' => $list_id,
                'product_id' => $product_id,
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to update purchase']);
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
