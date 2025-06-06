<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../create_functions.php';
require_once __DIR__ . '/../common_functions.php';

class CreateFunctionsTest extends TestCase
{
    private $pdo;

    public function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
    }

    public function testCreateProduct()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('rowCount')->willReturn(1);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $data = [
            'product_code' => 3001,
            'product_name' => 'テスト商品',
            'price' => 800,
            'stock_quantity' => 5,
            'vendor_code' => 202,
        ];

        $count = createProduct($this->pdo, $data);
        $this->assertSame(1, $count);
    }

    public function testGetAllVendorCodes()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $expected = [12, 13, 14];
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
