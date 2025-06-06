<?php
function getProducts($pdo, $keyword = '', $order = 'asc')
{
    $order = ($order === 'desc') ? 'DESC' : 'ASC';
    $sql = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ' . $order;
    $stmt = $pdo->prepare($sql);
    $partial_match = "%{$keyword}%";
    $stmt->bindValue(':keyword', $partial_match, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
