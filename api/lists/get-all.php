<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    $user_id = $_SESSION['user']['id'] ?? null;
    $stmt = $pdo->prepare("
        SELECT 
            lists.*, 
            COUNT(purchases.id_purchase) AS purchase_nbr,
            SUM(purchases.total_price) AS total_expense
        FROM lists
            LEFT JOIN purchases ON lists.id_list = purchases.list_id
            WHERE lists.is_active = 1 AND lists.user_id = :user_id
        GROUP BY lists.id_list
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}