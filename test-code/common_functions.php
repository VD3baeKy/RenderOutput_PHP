<?php
function getPdo($dsn, $db_user, $db_password) {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->exec("SET NAMES 'UTF8'");
    return $pdo;
}

function getAllVendorCodes($pdo) {
    $sql = 'SELECT vendor_code FROM vendors';
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

