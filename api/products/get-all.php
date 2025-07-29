<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("
        SELECT
            products.*,
            COUNT(purchases.id_purchase) AS purchase_nbr,
            COALESCE(SUM(purchases.total_price), 0) AS total_expense
        FROM products
            LEFT JOIN purchases ON products.id_product = purchases.product_id
        WHERE products.is_active = 1
        GROUP BY products.id_product
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
