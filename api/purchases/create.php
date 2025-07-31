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
        // ----------- If no list_id and list_name is provided, create a new list -----------
        if ($list_id === null || $list_name === null) {
            $list_name = 'Unlisted purchases';
            $list_description = 'All Unlisted purchases are listed here';
            // Check if user already has a unlisted list list
            $unlistedListCheck = $pdo->prepare("SELECT * FROM lists WHERE list_name = :list_name AND is_active = 1 AND user_id = :user_id");
            $unlistedListCheck->bindParam(':list_name', $list_name);
            $unlistedListCheck->bindParam(':user_id', $user_id);
            $unlistedListCheck->execute();

            if ($unlistedListCheck->rowCount() === 0) {
                $createList = $pdo->prepare("INSERT INTO lists (list_name, user_id, description) VALUES (:list_name, :user_id, :list_description)");
                $createList->bindParam(':list_name', $list_name);
                $createList->bindParam(':user_id', $user_id);
                $createList->bindParam(':list_description', $list_description);
                $createList->execute();
                // Get new list id and assign to $list_id
                $list_id = $pdo->lastInsertId();
            } else {
                $list_id = $unlistedListCheck->fetch(PDO::FETCH_ASSOC)['id_list'];
            }
        }

        // Check if the list with the given list_id exist
        $existingListCheck = $pdo->prepare("SELECT * FROM lists WHERE id_list = :list_id AND is_active = 1 AND user_id = :user_id");
        $existingListCheck->bindParam(':list_id', $list_id);
        $existingListCheck->bindParam(':user_id', $user_id);
        $existingListCheck->execute();

        if ($existingListCheck->rowCount() === 0) {
            // Check if the list with the given list_name exist
            $existingListName = $pdo->prepare("SELECT * FROM lists WHERE list_name = :list_name AND is_active = 1 AND user_id = :user_id");
            $existingListName->bindParam(':list_name', $list_name);
            $existingListName->bindParam(':user_id', $user_id);
            $existingListName->execute();

            if ($existingListName->rowCount() === 0) {
                $createList = $pdo->prepare("INSERT INTO lists (list_name, user_id) VALUES (:list_name, :user_id)");
                $createList->bindParam(':list_name', $list_name);
                $createList->bindParam(':user_id', $user_id);
                $createList->execute();
                // Get new list id and assign to $list_id
                $list_id = $pdo->lastInsertId();
            } else { // Get the existing list id and assign to $list_id to insure that the given list_name is prioritized
                $list_id = $existingListName->fetch(PDO::FETCH_ASSOC)['id_list'];        
            }
        }
        
        // ----------- Product operations -----------
        // Check if the product with the given product_id exist
        $existingProductCheck = $pdo->prepare("SELECT * FROM products WHERE id_product = :product_id AND is_active = 1");
        $existingProductCheck->bindParam(':product_id', $product_id);
        $existingProductCheck->execute();

        if ($existingProductCheck->rowCount() === 0) {
            // Check if the product with the given product_name exist
            $existingProductName = $pdo->prepare("SELECT * FROM products WHERE product_name = :product_name AND is_active = 1");
            $existingProductName->bindParam(':product_name', $product_name);
            $existingProductName->execute();

            if ($existingProductName->rowCount() === 0) {
                $createProduct = $pdo->prepare("INSERT INTO products (product_name) VALUES (:product_name)");
                $createProduct->bindParam(':product_name', $product_name);
                $createProduct->execute();
                // Get new product id and assign to $product_id
                $product_id = $pdo->lastInsertId();
            }
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
            echo json_encode(['message' => 'Purchase already exists in this list, update it instead']);
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
