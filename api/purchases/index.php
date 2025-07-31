<?php
// --------- Purchase controller ----------
require_once '../../helper/auth/index.php';
$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
# All these routes needs authentication
if (!isAuthenticated()) {
  http_response_code(401);
  echo json_encode(['message' => 'User not authenticated']);
  exit;
}
$get_all = $method === 'GET';
$filter_by_list = $method === 'GET' && isset($_GET["list_id"]);
$filter_by_date = $method === 'GET' && isset($_GET["purchase_date"]);
$filter_by_date_range = $method === 'GET' && isset($_GET['start_date'], $_GET['end_date']);
$get_total_expense = $method === 'GET' && isset($_GET['get_total_expense']);
$get_total_expense_by_product = $method === 'GET' && isset($_GET['product_id']) && isset($_GET['get_total_expense']);
$get_total_expense_by_date = $method === 'GET' && isset($_GET['purchase_date']) && isset($_GET['get_total_expense']);
$get_total_expense_by_date_range = $method === 'GET' && isset($_GET['start_date']) && isset($_GET['end_date']) && isset($_GET['get_total_expense']);
$create = $method === 'POST';
$update = $method === 'PUT';
$delete = $method === 'DELETE';

# /api/purchases  --GET
if (
  $get_all &&
  !$filter_by_date &&
  !$filter_by_list &&
  !$get_total_expense_by_product &&
  !$get_total_expense_by_date &&
  !$get_total_expense_by_date_range &&
  !$get_total_expense &&
  !$filter_by_date_range
) { 
  require_once 'get-all.php';

} elseif ($filter_by_list) { # /api/purchases?list_id=X
  require_once 'filter-by-list.php';

} elseif ($filter_by_date && !$get_total_expense_by_date && !$get_total_expense_by_date_range && !$get_total_expense && !$filter_by_date_range) { # /api/purchases?purchase_date=dd-mm-yyyy
  require_once 'filter-by-date.php';

}  elseif ($filter_by_date_range && !$get_total_expense_by_date && !$get_total_expense_by_date_range && !$get_total_expense) { # /api/purchases?start_date=dd-mm-yyyy&end_date=dd-mm-yyyy
  require_once 'filter-by-date-range.php';

} elseif ($get_total_expense_by_product && !$get_total_expense) { # /api/purchases?product_id=X&get_total_expense=1
  require_once 'get-total-expense-by-product.php';

} elseif ($get_total_expense_by_date && !$get_total_expense_by_date_range && !$get_total_expense && !$filter_by_date_range) { # /api/purchases?purchase_date=dd-mm-yyyy&get_total_expense=1
  require_once 'get-total-expense-by-date.php';

} elseif($get_total_expense_by_date_range && !$get_total_expense) { # /api/purchases?start_date=dd-mm-yyyy&end_date=dd-mm-yyyy&get_total_expense=1
  require_once 'get-total-expense-by-date-range.php';

} elseif ($get_total_expense) { # /api/purchases?get_total_expense=1  --GET
  require_once 'get-total-expense.php';

} elseif ($create) { # /api/purchases  --POST
  require_once 'create.php';

} elseif ($update) { # /api/purchases?id=X  --PUT
  require_once 'update.php';

} elseif ($delete) { # /api/purchases?id=X  --DELETE
  require_once 'delete.php';

} else {
  http_response_code(405);
  echo json_encode(["message" => "Method not allowed"]);
}