<?php
require_once '../../helper/auth/index.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

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
  require_once 'get-all.php';

} elseif ($create) {
  require_once 'create.php';

} elseif ($update) {
  require_once 'update.php';

} elseif ($delete) {
  require_once 'delete.php';

} else {
  http_response_code(405);
  echo json_encode(["message" => "Method not allowed"]);
}