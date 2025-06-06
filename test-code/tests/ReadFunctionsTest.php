<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../read_functions.php';

class ReadFunctionsTest extends TestCase
{
    private $pdo;

    public function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
    }

    // 昇順の検索
    public function testGetProductsAsc()
    {
        $stmt = $this->createMock(PDOStatement::class);

        $products = [
            [
                'id' => 1,
                'product_code' => 1001,
                'product_name' => 'てすとA',
                'price' => 500,
                'stock_quantity' => 10,
                'vendor_code' => 111,
                'updated_at' => '2023-01-01 12:00:00',
            ]
        ];

        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($products);

        $sql = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ASC';

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($sql))
            ->willReturn($stmt);

        $result = getProducts($this->pdo, 'てすと', 'asc');
        $this->assertSame($products, $result);
    }

    // 降順の検索
    public function testGetProductsDesc()
    {
        $stmt = $this->createMock(PDOStatement::class);

        $products = [
            [
                'id' => 2,
                'product_code' => 1002,
                'product_name' => 'てすとB',
                'price' => 700,
                'stock_quantity' => 20,
                'vendor_code' => 112,
                'updated_at' => '2023-02-01 12:00:00',
            ]
        ];

        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($products);

        $sql = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at DESC';

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($sql))
            ->willReturn($stmt);

        $result = getProducts($this->pdo, 'てすと', 'desc');
        $this->assertSame($products, $result);
    }

    // デフォルト（order指定なし、キーワードなし）
    public function testGetProductsDefault()
    {
        $stmt = $this->createMock(PDOStatement::class);

        $products = [];
        $stmt->expects($this->once())->method('execute');
        $stmt->expects($this->once())->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($products);

        $sql = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ASC';

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($sql))
            ->willReturn($stmt);

        $result = getProducts($this->pdo);
        $this->assertSame([], $result);
    }
}
