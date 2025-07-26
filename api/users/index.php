<?php
$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
if ($method === 'GET') {
  require_once 'getAll.php';
}
else {
  http_response_code(405);
  echo json_encode(array("message" => "Method not allowed"));
}