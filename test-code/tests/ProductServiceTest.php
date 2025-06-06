<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Service\ProductService;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    public function testCreateProduct()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('rowCount')->willReturn(1);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $data = [
            'product_code'   => 123,
            'product_name'   => 'テスト商品',
            'price'          => 1000,
            'stock_quantity' => 10,
            'vendor_code'    => 1,
        ];
        $result = ProductService::createProduct($pdo, $data);
        $this->assertSame(1, $result);
    }

    public function testGetAllVendorCodes()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $expect = [1,2,3];
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_COLUMN)
            ->willReturn($expect);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('query')
            ->willReturn($stmt);

        $codes = ProductService::getAllVendorCodes($pdo);
        $this->assertSame($expect, $codes);
    }

    public function testDeleteProduct()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('rowCount')->willReturn(1);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $id = 10;
        $result = \App\Service\ProductService::deleteProduct($pdo, $id);
        $this->assertSame(1, $result);
    }

    public function testSearchProductsAsc()
    {
        $stmt = $this->createMock(PDOStatement::class);
    
        $expected = [
            ['id' => 1, 'product_code' => 1001, 'product_name' => 'サンプル', 'price' => 100, 'stock_quantity' => 5, 'vendor_code' => 111, 'updated_at' => '2024-01-01 00:00:00'],
        ];
    
        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);
    
        $pdo = $this->createMock(PDO::class);
        $sql = "SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ASC";
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($sql))
            ->willReturn($stmt);
    
        $result = \App\Service\ProductService::searchProducts($pdo, 'サンプル', 'asc');
        $this->assertSame($expected, $result);
    }

    public function testSearchProductsDesc()
    {
        $stmt = $this->createMock(PDOStatement::class);
    
        $expected = [
            ['id' => 2, 'product_code' => 2001, 'product_name' => '本番B', 'price' => 200, 'stock_quantity' => 20, 'vendor_code' => 222, 'updated_at' => '2024-05-05 00:00:00'],
        ];
    
        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);
    
        $pdo = $this->createMock(PDO::class);
        $sql = "SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at DESC";
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($sql))
            ->willReturn($stmt);
    
        $result = \App\Service\ProductService::searchProducts($pdo, '本番', 'desc');
        $this->assertSame($expected, $result);
    }

    public function testGetProductById()
    {
        $stmt = $this->createMock(PDOStatement::class);
    
        $expected = [
            'id' => 1, 'product_code' => 123, 'product_name' => 'テスト商品', 'price' => 1000,
            'stock_quantity' => 10, 'vendor_code' => 1, 'updated_at' => '2024-01-01 00:00:00'
        ];
        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);
    
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('WHERE id = :id'))
            ->willReturn($stmt);
    
        $result = \App\Service\ProductService::getProductById($pdo, 1);
        $this->assertSame($expected, $result);
    }
    
    public function testGetProductByIdNotFound()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);
    
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);
    
        $result = \App\Service\ProductService::getProductById($pdo, 999);
        $this->assertNull($result);
    }
    
    public function testUpdateProduct()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('rowCount')->willReturn(1);
    
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);
    
        $data = [
            'product_code' => 123,
            'product_name' => '商品A',
            'price' => 2000,
            'stock_quantity' => 3,
            'vendor_code' => 99,
        ];
        $result = \App\Service\ProductService::updateProduct($pdo, 5, $data);
        $this->assertSame(1, $result);
    }
    
}
