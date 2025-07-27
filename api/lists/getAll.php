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
    $stmt = $pdo->prepare("SELECT * FROM lists WHERE is_active = 1 AND user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}