<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $_SESSION['user']['id'];

        // Validate required fields
        $number = $data['number'] ?? null;
        $unit = trim($data['unit']) ?? null;
        $unit_price = $data['unit_price'] ?? null;
        $list_id = $data['list_id'] ?? null;
        $product_id = $data['product_id'] ?? null;
        $product_name = trim($data['product_name']) ?? null;
        $list_name = isset($data['list_name']) && trim($data['list_name']) !== '' ? trim($data['list_name']) : null;

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
        if ($product_name === null || !is_string($product_name) || empty($product_name)) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid or missing "product_name". Must be a non-empty string.']);
            exit;
        }

        // ----------- List operations -----------

        // Normalize list name if both are missing
        if (empty($list_name) && empty($list_id)) {
            $list_name = 'Unlisted purchases';
            $list_description = 'All Unlisted purchases are listed here';
        }

        // 1. Check if the list with the given list_name exists and is active
        $existingListName = $pdo->prepare("
            SELECT * FROM lists 
            WHERE list_name = :list_name AND is_active = 1 AND user_id = :user_id
        ");
        $existingListName->bindParam(':list_name', $list_name);
        $existingListName->bindParam(':user_id', $user_id);
        $existingListName->execute();

        if ($existingListName->rowCount() > 0) {
            // Use the existing list's ID
            $list_id = $existingListName->fetch(PDO::FETCH_ASSOC)['id_list'];

        } else {
            // Create new list (with optional description for unlisted)
            $createListQuery = empty($list_description)
                ? "INSERT INTO lists (list_name, user_id) VALUES (:list_name, :user_id)"
                : "INSERT INTO lists (list_name, user_id, description) VALUES (:list_name, :user_id, :list_description)";

            $createList = $pdo->prepare($createListQuery);
            $createList->bindParam(':list_name', $list_name);
            $createList->bindParam(':user_id', $user_id);
            if (!empty($list_description)) {
                $createList->bindParam(':list_description', $list_description);
            }
            $createList->execute();

            // Get the new list ID
            $list_id = $pdo->lastInsertId();
        }
        
        // ----------- Product operations -----------
        // Check if the product with the same name exists and is active
        $existingProductName = $pdo->prepare("
            SELECT * FROM products 
            WHERE product_name = :product_name AND is_active = 1
        ");
        $existingProductName->bindParam(':product_name', $product_name);
        $existingProductName->execute();

        if ($existingProductName->rowCount() > 0) {
            // Product exists with the name, use its ID
            $product_id = $existingProductName->fetch(PDO::FETCH_ASSOC)['id_product'];

        } else {
            // Product name not found, create new product
            $createProduct = $pdo->prepare("
                INSERT INTO products (product_name) 
                VALUES (:product_name)
            ");
            $createProduct->bindParam(':product_name', $product_name);
            $createProduct->execute();

            // Assign the new product ID
            $product_id = $pdo->lastInsertId();
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
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
