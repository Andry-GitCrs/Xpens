<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $user_id = $_SESSION['user']['id'] ?? null;

        $stmt = $pdo->prepare("
            SELECT 
                lists.*, 
                COUNT(purchases.id_purchase) AS purchase_nbr,
                SUM(purchases.total_price) AS total_expense
            FROM lists
                LEFT JOIN purchases ON lists.id_list = purchases.list_id
            WHERE 
                lists.is_active = 1 AND
                lists.user_id = :user_id
            GROUP BY lists.id_list
            ORDER BY lists.updated_at DESC
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($lists as &$list) {
            $list_id = $list['id_list'];
            $purchasesStmt = $pdo->prepare("
                SELECT purchases.*, purchases.description as purchase_description, lists.*, products.product_name AS product_name FROM purchases 
                    JOIN products ON purchases.product_id = products.id_product
                    JOIN lists ON purchases.list_id = lists.id_list
                    JOIN users ON lists.user_id = users.id_user
                WHERE 
                    purchases.list_id = :list_id AND
                    users.id_user = :user_id 
                ORDER BY purchases.created_at DESC");
            $purchasesStmt->bindParam(':user_id', $user_id);
            $purchasesStmt->bindParam(':list_id', $list_id);
            $purchasesStmt->execute();
            $list['purchases'] = $purchasesStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($lists);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}