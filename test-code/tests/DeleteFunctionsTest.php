<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../delete_functions.php';

class DeleteFunctionsTest extends TestCase
{
    private $pdo;

    public function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
    }

    public function testDeleteProduct()
    {
        $stmt = $this->createMock(PDOStatement::class);

        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('rowCount')->willReturn(1);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('DELETE FROM products WHERE id = :id'))
            ->willReturn($stmt);

        $result = deleteProduct($this->pdo, 10);
        $this->assertSame(1, $result);
    }
}
