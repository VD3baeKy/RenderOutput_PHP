<?php
use PHPUnit\Framework\TestCase;
use App\Service\ProductService;

require_once __DIR__ . '/../../vendor/autoload.php';

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
}
