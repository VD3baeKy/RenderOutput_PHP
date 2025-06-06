<?php
namespace App\Service;

use PDO;

class ProductService
{
    public static function createProduct(PDO $pdo, array $data): int
    {
        $sql = '
            INSERT INTO products (product_code, product_name, price, stock_quantity, vendor_code)
            VALUES (:product_code, :product_name, :price, :stock_quantity, :vendor_code)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':product_code', $data['product_code'], PDO::PARAM_INT);
        $stmt->bindValue(':product_name', $data['product_name'], PDO::PARAM_STR);
        $stmt->bindValue(':price', $data['price'], PDO::PARAM_INT);
        $stmt->bindValue(':stock_quantity', $data['stock_quantity'], PDO::PARAM_INT);
        $stmt->bindValue(':vendor_code', $data['vendor_code'], PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public static function getAllVendorCodes(PDO $pdo): array
    {
        $sql = 'SELECT vendor_code FROM vendors';
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function deleteProduct(PDO $pdo, int $id): int
    {
        $sql = 'DELETE FROM products WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
}
