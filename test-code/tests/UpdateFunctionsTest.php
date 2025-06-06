<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../update_functions.php';
require_once __DIR__ . '/../common_functions.php';

class UpdateFunctionsTest extends TestCase
{
    private $pdo;

    public function setUp(): void
    {
        // PDOスタブ作成
        $this->pdo = $this->createMock(PDO::class);
    }

    public function testUpdateProduct()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('rowCount')->willReturn(1);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $data = [
            'product_code' => 1122,
            'product_name' => 'Test product',
            'price' => 1000,
            'stock_quantity' => 10,
            'vendor_code' => 201,
        ];

        $count = updateProduct($this->pdo, 3, $data);
        $this->assertSame(1, $count);
    }

    public function testGetProductById()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $expected = [
            'id' => 1,
            'product_code' => 123,
            'product_name' => '商品A',
            'price' => 5000,
            'stock_quantity' => 20,
            'vendor_code' => 77
        ];
        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $result = getProductById($this->pdo, 1);
        $this->assertSame($expected, $result);
    }

    public function testGetAllVendorCodes()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $expected = [11, 22, 33];
        $stmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_COLUMN)
            ->willReturn($expected);

        $this->pdo->expects($this->once())
            ->method('query')
            ->willReturn($stmt);

        $result = getAllVendorCodes($this->pdo);
        $this->assertSame($expected, $result);
    }
}
