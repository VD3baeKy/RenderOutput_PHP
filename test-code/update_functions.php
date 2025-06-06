<?php

function updateProduct($pdo, $id, $data) {
    $sql = '
        UPDATE products
           SET product_code = :product_code,
               product_name = :product_name,
               price = :price,
               stock_quantity = :stock_quantity,
               vendor_code = :vendor_code
         WHERE id = :id
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':product_code', $data['product_code'], PDO::PARAM_INT);
    $stmt->bindValue(':product_name', $data['product_name'], PDO::PARAM_STR);
    $stmt->bindValue(':price', $data['price'], PDO::PARAM_INT);
    $stmt->bindValue(':stock_quantity', $data['stock_quantity'], PDO::PARAM_INT);
    $stmt->bindValue(':vendor_code', $data['vendor_code'], PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
}

function getProductById($pdo, $id) {
    $sql = 'SELECT * FROM products WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

