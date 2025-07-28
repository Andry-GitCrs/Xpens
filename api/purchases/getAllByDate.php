<?php
function getAllByDate($date, $user_id, $pdo) {
  // Step 1: Check if $date is a string
  if (!is_string($date)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input. Date must be a string.']);
    exit;
  }

  // Step 2: Try to parse the date
  $parsedDate = DateTime::createFromFormat('d-m-Y', $date);
  $errors = DateTime::getLastErrors();

  // Step 3: Check for errors or warnings during parsing
  if (!$parsedDate || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid date format or value. Expected format: dd-mm-yyyy.']);
    exit;
  }

  // Step 4: Convert to Y-m-d format for SQL
  $sqlDate = $parsedDate->format('Y-m-d');

  // Step 5: Execute query
  $stmt = $pdo->prepare("
    SELECT purchases.* FROM purchases
      JOIN lists ON purchases.list_id = lists.id_list
      JOIN users ON lists.user_id = users.id_user
    WHERE 
      DATE(purchases.purchase_date) = :date AND
      purchases.is_active = 1 AND
      lists.user_id = :user_id
  ");
  $stmt->bindParam(':user_id', $user_id);
  $stmt->bindParam(':date', $sqlDate);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
