<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
