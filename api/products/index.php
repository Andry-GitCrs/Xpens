<?php
$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

if ($method === 'GET') {
  include 'getAll.php';

} else if ($method === 'POST') {
  include 'create.php';

} else if ($method === 'PUT') {
  include 'update.php';

} else if ($method === 'DELETE') {
  include 'delete.php';

} else {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  
}