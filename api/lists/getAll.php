<?php
require_once '../../config/db.php';
include '../../helper/auth/index.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['message' => 'User not authenticated']);
        exit;
    }

    $user_id = $_SESSION['user']['id'] ?? null;
    $stmt = $pdo->prepare("
        SELECT 
            lists.*, 
            COUNT(purchases.id_purchase) AS purchase_nbr
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