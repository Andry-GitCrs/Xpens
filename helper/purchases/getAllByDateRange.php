<?php
function getAllByDateRange($start_date, $end_date, $user_id, $pdo) {
  // Validate start date
  $start = DateTime::createFromFormat('d-m-Y', $start_date);
  $start_errors = DateTime::getLastErrors();
  if (!$start || $start_errors['warning_count'] > 0 || $start_errors['error_count'] > 0) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid start date. Format must be dd-mm-yyyy.']);
    exit;
  }

  // Validate end date
  $end = DateTime::createFromFormat('d-m-Y', $end_date);
  $end_errors = DateTime::getLastErrors();
  if (!$end || $end_errors['warning_count'] > 0 || $end_errors['error_count'] > 0) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid end date. Format must be dd-mm-yyyy.']);
    exit;
  }

  // Convert to SQL format
  $sqlStart = $start->format('Y-m-d');
  $sqlEnd = $end->format('Y-m-d');

  // Query with date range
  $stmt = $pdo->prepare("
    SELECT 
      purchases.*
    FROM purchases
      JOIN lists ON purchases.list_id = lists.id_list
      JOIN users ON lists.user_id = users.id_user
    WHERE 
      DATE(purchases.purchase_date) BETWEEN :start AND :end AND
      purchases.is_active = 1 AND
      lists.user_id = :user_id
  ");
  $stmt->bindParam(':user_id', $user_id);
  $stmt->bindParam(':start', $sqlStart);
  $stmt->bindParam(':end', $sqlEnd);
  $stmt->execute();

  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
