<?php
require_once '../../config/db.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT username, is_active, created_at, updated_at FROM users WHERE is_active = 1");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        "message" => "Xpens user list",
        "date" => $data
    ]);
}else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}