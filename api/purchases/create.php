<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user']['id'];

    // Validate required fields
    $number = $data['number'] ?? null;
    $unit = $data['unit'] ?? null;
    $unit_price = $data['unit_price'] ?? null;
    $list_id = $data['list_id'] ?? null;
    $product_id = $data['product_id'] ?? null;

    if ($number === null || !is_numeric($number) || $number <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid or missing "number". Must be a positive number.']);
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

    // Optional fields
    $description = isset($data['description']) && !empty($data['description']) ? trim($data['description']) : 'No description';
    $purchase_date = isset($data['purchase_date']) ? trim($data['purchase_date']) : date('d-m-Y H:i');


    if ($purchase_date !== null ) {
        if (!is_string($purchase_date)) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid "purchase_date". Must be a string.']);
            exit;
        }

        $date = DateTime::createFromFormat('d-m-Y H:i', $purchase_date);
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

    // Check if purchase already exists
    $existingPurchaseCheck = $pdo->prepare("SELECT * FROM purchases WHERE list_id = :list_id AND product_id = :product_id AND is_active = 1");
    $existingPurchaseCheck->bindParam(':list_id', $list_id); 
    $existingPurchaseCheck->bindParam(':product_id', $product_id);
    $existingPurchaseCheck->execute();
    if ($existingPurchaseCheck->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['message' => 'Purchase already exists, update it instead']);
        exit;
    }

    // Insert into purchases
    $stmt = $pdo->prepare("
        INSERT INTO purchases 
          (description, number, unit, unit_price, list_id, product_id, purchase_date) 
        VALUES 
          (:description, :number, :unit, :unit_price, :list_id, :product_id, :purchase_date)
    ");

    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':number', $number);
    $stmt->bindParam(':unit', $unit);
    $stmt->bindParam(':unit_price', $unit_price);
    $stmt->bindParam(':list_id', $list_id);
    $stmt->bindParam(':product_id', $product_id);

    if ($purchase_date !== null) {
        $stmt->bindParam(':purchase_date', $sqlFormattedDate);
    }

    if ($stmt->execute()) {
        $id = $pdo->lastInsertId();

        // Fetch inserted row to return (including generated total_price, timestamps)
        $fetchStmt = $pdo->prepare("SELECT * FROM purchases WHERE id_purchase = :id");
        $fetchStmt->bindParam(':id', $id);
        $fetchStmt->execute();
        $purchase = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        echo json_encode([
            'message' => 'Purchase created successfully',
            'data' => $purchase
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to create purchase']);
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
