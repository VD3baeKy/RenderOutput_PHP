<?php
function getPdo($dsn, $db_user, $db_password) {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->exec("SET NAMES 'UTF8'");
    return $pdo;
}

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

function getAllVendorCodes($pdo) {
    $sql = 'SELECT vendor_code FROM vendors';
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
