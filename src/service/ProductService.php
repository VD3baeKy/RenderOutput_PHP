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

    /**
     * 商品一覧取得（部分一致検索＆並び順）
     * 
     * @param PDO    $pdo
     * @param string $keyword  検索ワード（空OK）
     * @param string $order    並び順('asc'|'desc'以外ならasc)
     * @return array
     */
    public static function searchProducts(PDO $pdo, string $keyword = '', string $order = 'asc'): array
    {
        $order = ($order === 'desc') ? 'DESC' : 'ASC';
        $sql = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ' . $order;
        $stmt = $pdo->prepare($sql);
        // 検索語の前後に%をつける（部分一致）
        $partial_match = "%" . $keyword . "%";
        $stmt->bindValue(':keyword', $partial_match, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getProductById(PDO $pdo, int $id): ?array
    {
        $sql = 'SELECT * FROM products WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    // 商品情報を更新
    public static function updateProduct(PDO $pdo, int $id, array $data): int
    {
        $sql = '
            UPDATE products
            SET
                product_code = :product_code,
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
    
}
