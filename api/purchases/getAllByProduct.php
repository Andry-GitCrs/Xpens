<?php
function getAllByProduct($product_id, $user_id, $pdo) {
  $stmt = $pdo->prepare("
    SELECT 
      purchases.id_purchase,
      purchases.description,
      purchases.purchase_date,
      purchases.number,
      purchases.unit_price,
      purchases.unit,
      purchases.total_price,
      purchases.created_at,
      purchases.updated_at,
      products.product_name,
      lists.list_name
    FROM purchases 
      JOIN products ON purchases.product_id = products.id_product
      JOIN lists ON purchases.list_id = lists.id_list 
      JOIN users ON lists.user_id = users.id_user 
    WHERE 
      purchases.is_active = 1 AND
      lists.user_id = :user_id AND
      purchases.product_id = :product_id
  ");
  $stmt->bindParam(':product_id', $product_id);
  $stmt->bindParam(':user_id', $user_id);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}