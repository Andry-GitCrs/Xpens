<?php
require_once '../../../config/db.php';
require_once '../../../helper/auth/index.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if (!isAuthenticated()) {
  http_response_code(401);
  echo json_encode(['message' => 'User not authenticated']);
  exit;
}

if ($method === 'GET') {
    $user_id = $_SESSION['user']['id'];
    // 1. Daily average expense
    $stmt = $pdo->prepare("
        SELECT AVG(daily_sum) AS daily_avg_expense FROM (
          SELECT DATE(purchase_date) as day, SUM(total_price) AS daily_sum
          FROM purchases pu
          JOIN lists l ON pu.list_id = l.id_list
          WHERE l.user_id = :user_id
          GROUP BY day
        ) AS daily_totals
    ");
    $stmt->execute(['user_id' => $user_id]);
    $dailyAvg = $stmt->fetchColumn();

    // 2. Monthly average expense
    $stmt = $pdo->prepare("
        SELECT AVG(monthly_sum) AS monthly_avg_expense FROM (
          SELECT DATE_FORMAT(purchase_date, '%Y-%m') AS month, SUM(total_price) AS monthly_sum
          FROM purchases pu
          JOIN lists l ON pu.list_id = l.id_list
          WHERE l.user_id = :user_id
          GROUP BY month
        ) AS monthly_totals
    ");
    $stmt->execute(['user_id' => $user_id]);
    $monthlyAvg = $stmt->fetchColumn();

    // 3. Top 5 lists by total expense
    $stmt = $pdo->prepare("
        SELECT l.*, SUM(pu.total_price) AS total_expense
        FROM purchases pu
        JOIN lists l ON pu.list_id = l.id_list
        WHERE l.user_id = :user_id and l.is_active = 1
        GROUP BY l.id_list, l.list_name
        ORDER BY total_expense DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $user_id]);
    $topLists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Preferred purchased product
    $stmt = $pdo->prepare("
        SELECT p.*, pu.purchase_date, SUM(pu.number) AS total_quantity, SUM(pu.total_price) AS total_expense
        FROM purchases pu
        JOIN lists l ON pu.list_id = l.id_list
        JOIN products p ON pu.product_id = p.id_product
        WHERE l.user_id = :user_id AND p.is_active = 1
        GROUP BY p.id_product, p.product_name
        ORDER BY total_quantity DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $user_id]);
    $preferredProduct = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'user' => $_SESSION['user'],
        'daily_avg_expense' => round(floatval($dailyAvg), 2),
        'monthly_avg_expense' => round(floatval($monthlyAvg), 2),
        'top_lists' => $topLists,
        'preferred_product' => $preferredProduct ?: null
    ]);

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}