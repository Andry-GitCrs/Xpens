<?php
require_once '../../config/db.php';
require_once '../../helper/auth/index.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['message' => 'User not authenticated']);
        exit;
    }

    $user_id = $_SESSION['user']['id'];

    $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
