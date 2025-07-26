<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM products WHERE stat = 1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
