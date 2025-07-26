<?php
$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
if ($method === 'GET') {
  require_once 'getAll.php';
} elseif ($method === 'POST') {
  require_once 'create.php';
} elseif ($method === 'PUT') {
  require_once 'update.php';
} elseif ($method === 'DELETE') {
  require_once 'delete.php';
}
else {
  http_response_code(405);
  echo json_encode(array("message" => "Method not allowed"));
}