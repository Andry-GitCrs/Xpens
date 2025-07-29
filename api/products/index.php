<?php
$method = $_SERVER['REQUEST_METHOD'];
require_once '../../helper/auth/index.php';
header('Content-Type: application/json');

if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['message' => 'User not authenticated']);
    exit;
}

$get_all = $method === 'GET';
$create = $method === 'POST';
$update = $method === 'PUT';
$delete = $method === 'DELETE';

if ($get_all) {
  include 'get-all.php';

} else if ($create) {
  include 'create.php';

} else if ($update) {
  include 'update.php';

} else if ($delete) {
  include 'delete.php';

} else {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
  
}