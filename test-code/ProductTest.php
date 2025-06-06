<?php
/**
 * 商品管理アプリ テストコード
 * test-code/ProductTest.php
 */

class ProductTest {
    private $pdo;
    private $test_results = [];
    
    public function __construct() {
        $this->setupTestDatabase();
    }
    
    /**
     * テスト用データベース接続設定
     */
    private function setupTestDatabase() {
        try {
            // 本番環境とは別のテスト用データベースを使用
            $dsn = "pgsql:host=postgres;port=5432;dbname=product_management_test;charset=utf8";
            $user = $_ENV['DB_USER'] ?? 'postgres';
            $password = $_ENV['DB_PASSWORD'] ?? 'password';
            
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            echo "✅ テスト用データベース接続成功\n";
        } catch (PDOException $e) {
            echo "❌ テスト用データベース接続失敗: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    /**
     * テスト実行前の準備（テストデータのクリーンアップと初期化）
     */
    private function setUp() {
        try {
            // テスト用テーブルをクリーンアップ
            $this->pdo->exec("TRUNCATE TABLE products, vendors RESTART IDENTITY CASCADE");
            
            // テスト用の仕入先データを挿入
            $sql = "INSERT INTO vendors (vendor_code, vendor_name) VALUES (9999, 'テスト仕入先')";
            $this->pdo->exec($sql);
            
        } catch (PDOException $e) {
            echo "❌ テストセットアップ失敗: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * テスト1: 商品の新規作成テスト
     */
    public function testCreateProduct() {
        $this->setUp();
        
        try {
            // テスト用商品データ
            $test_product = [
                'product_code' => 99999,
                'product_name' => 'テスト商品',
                'price' => 1500,
                'stock_quantity' => 10,
                'vendor_code' => 9999
            ];
            
            // 商品を作成
            $sql = "INSERT INTO products (product_code, product_name, price, stock_quantity, vendor_code) 
                    VALUES (:product_code, :product_name, :price, :stock_quantity, :vendor_code)";
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':product_code', $test_product['product_code'], PDO::PARAM_INT);
            $stmt->bindValue(':product_name', $test_product['product_name'], PDO::PARAM_STR);
            $stmt->bindValue(':price', $test_product['price'], PDO::PARAM_INT);
            $stmt->bindValue(':stock_quantity', $test_product['stock_quantity'], PDO::PARAM_INT);
            $stmt->bindValue(':vendor_code', $test_product['vendor_code'], PDO::PARAM_INT);
            
            $result = $stmt->execute();
            $inserted_count = $stmt->rowCount();
            
            // アサーション（期待値との比較）
            $this->assertEquals(1, $inserted_count, "商品の作成件数が期待値と一致しません");
            
            // 実際にデータが保存されているかを確認
            $check_sql = "SELECT * FROM products WHERE product_code = :product_code";
            $check_stmt = $this->pdo->prepare($check_sql);
            $check_stmt->bindValue(':product_code', $test_product['product_code'], PDO::PARAM_INT);
            $check_stmt->execute();
            $saved_product = $check_stmt->fetch();
            
            $this->assertNotFalse($saved_product, "商品データが保存されていません");
            $this->assertEquals($test_product['product_name'], $saved_product['product_name'], "商品名が期待値と一致しません");
            $this->assertEquals($test_product['price'], $saved_product['price'], "価格が期待値と一致しません");
            
            $this->addTestResult('testCreateProduct', true, "商品作成テスト成功");
            
        } catch (Exception $e) {
            $this->addTestResult('testCreateProduct', false, "商品作成テスト失敗: " . $e->getMessage());
        }
    }
    
    /**
     * テスト2: 商品の読み取り（一覧取得）テスト
     */
    public function testReadProducts() {
        $this->setUp();
        
        try {
            // テスト用商品を複数作成
            $test_products = [
                [99991, 'テスト商品A', 1000, 5, 9999],
                [99992, 'テスト商品B', 2000, 15, 9999],
                [99993, 'テスト商品C', 1500, 8, 9999]
            ];
            
            foreach ($test_products as $product) {
                $sql = "INSERT INTO products (product_code, product_name, price, stock_quantity, vendor_code) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($product);
            }
            
            // 商品一覧を取得
            $sql = "SELECT * FROM products ORDER BY product_code ASC";
            $stmt = $this->pdo->query($sql);
            $products = $stmt->fetchAll();
            
            // アサーション
            $this->assertEquals(3, count($products), "取得した商品数が期待値と一致しません");
            $this->assertEquals('テスト商品A', $products[0]['product_name'], "1番目の商品名が期待値と一致しません");
            $this->assertEquals(99993, $products[2]['product_code'], "3番目の商品コードが期待値と一致しません");
            
            $this->addTestResult('testReadProducts', true, "商品一覧取得テスト成功");
            
        } catch (Exception $e) {
            $this->addTestResult('testReadProducts', false, "商品一覧取得テスト失敗: " . $e->getMessage());
        }
    }
    
    /**
     * テスト3: 商品の更新テスト
     */
    public function testUpdateProduct() {
        $this->setUp();
        
        try {
            // まずテスト用商品を作成
            $sql = "INSERT INTO products (product_code, product_name, price, stock_quantity, vendor_code) 
                    VALUES (99998, '更新前商品', 1000, 10, 9999)";
            $this->pdo->exec($sql);
            
            // 作成した商品のIDを取得
            $get_id_sql = "SELECT id FROM products WHERE product_code = 99998";
            $stmt = $this->pdo->query($get_id_sql);
            $product = $stmt->fetch();
            $product_id = $product['id'];
            
            // 商品情報を更新
            $update_sql = "UPDATE products 
                          SET product_name = :product_name, price = :price, stock_quantity = :stock_quantity 
                          WHERE id = :id";
            $update_stmt = $this->pdo->prepare($update_sql);
            
            $update_stmt->bindValue(':product_name', '更新後商品', PDO::PARAM_STR);
            $update_stmt->bindValue(':price', 1500, PDO::PARAM_INT);
            $update_stmt->bindValue(':stock_quantity', 20, PDO::PARAM_INT);
            $update_stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
            
            $result = $update_stmt->execute();
            $updated_count = $update_stmt->rowCount();
            
            // アサーション
            $this->assertEquals(1, $updated_count, "更新件数が期待値と一致しません");
            
            // 更新後のデータを確認
            $check_sql = "SELECT * FROM products WHERE id = :id";
            $check_stmt = $this->pdo->prepare($check_sql);
            $check_stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
            $check_stmt->execute();
            $updated_product = $check_stmt->fetch();
            
            $this->assertEquals('更新後商品', $updated_product['product_name'], "更新後の商品名が期待値と一致しません");
            $this->assertEquals(1500, $updated_product['price'], "更新後の価格が期待値と一致しません");
            $this->assertEquals(20, $updated_product['stock_quantity'], "更新後の在庫数が期待値と一致しません");
            
            $this->addTestResult('testUpdateProduct', true, "商品更新テスト成功");
            
        } catch (Exception $e) {
            $this->addTestResult('testUpdateProduct', false, "商品更新テスト失敗: " . $e->getMessage());
        }
    }
    
    /**
     * テスト4: 商品の削除テスト
     */
    public function testDeleteProduct() {
        $this->setUp();
        
        try {
            // テスト用商品を作成
            $sql = "INSERT INTO products (product_code, product_name, price, stock_quantity, vendor_code) 
                    VALUES (99997, '削除テスト商品', 1000, 5, 9999)";
            $this->pdo->exec($sql);
            
            // 作成した商品のIDを取得
            $get_id_sql = "SELECT id FROM products WHERE product_code = 99997";
            $stmt = $this->pdo->query($get_id_sql);
            $product = $stmt->fetch();
            $product_id = $product['id'];
            
            // 商品を削除
            $delete_sql = "DELETE FROM products WHERE id = :id";
            $delete_stmt = $this->pdo->prepare($delete_sql);
            $delete_stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
            $result = $delete_stmt->execute();
            $deleted_count = $delete_stmt->rowCount();
            
            // アサーション
            $this->assertEquals(1, $deleted_count, "削除件数が期待値と一致しません");
            
            // データが削除されていることを確認
            $check_sql = "SELECT * FROM products WHERE id = :id";
            $check_stmt = $this->pdo->prepare($check_sql);
            $check_stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
            $check_stmt->execute();
            $deleted_product = $check_stmt->fetch();
            
            $this->assertFalse($deleted_product, "商品が削除されていません");
            
            $this->addTestResult('testDeleteProduct', true, "商品削除テスト成功");
            
        } catch (Exception $e) {
            $this->addTestResult('testDeleteProduct', false, "商品削除テスト失敗: " . $e->getMessage());
        }
    }
    
    /**
     * テスト5: バリデーションテスト（無効なデータでの作成）
     */
    public function testValidation() {
        $this->setUp();
        
        try {
            // 価格が負の値の商品を作成しようとする
            $sql = "INSERT INTO products (product_code, product_name, price, stock_quantity, vendor_code) 
                    VALUES (99996, '無効商品', -100, 5, 9999)";
            
            $validation_failed = false;
            try {
                $this->pdo->exec($sql);
            } catch (PDOException $e) {
                $validation_failed = true;
            }
            
            $this->assertTrue($validation_failed, "価格の負の値チェックが機能していません");
            
            $this->addTestResult('testValidation', true, "バリデーションテスト成功");
            
        } catch (Exception $e) {
            $this->addTestResult('testValidation', false, "バリデーションテスト失敗: " . $e->getMessage());
        }
    }
    
    /**
     * すべてのテストを実行
     */
    public function runAllTests() {
        echo "\n=== 商品管理アプリ テスト開始 ===\n\n";
        
        $this->testCreateProduct();
        $this->testReadProducts();
        $this->testUpdateProduct();
        $this->testDeleteProduct();
        $this->testValidation();
        
        $this->printTestResults();
    }
    
    /**
     * テスト結果を記録
     */
    private function addTestResult($test_name, $passed, $message) {
        $this->test_results[] = [
            'name' => $test_name,
            'passed' => $passed,
            'message' => $message
        ];
        
        $status = $passed ? '✅' : '❌';
        echo "{$status} {$test_name}: {$message}\n";
    }
    
    /**
     * テスト結果を出力
     */
    private function printTestResults() {
        echo "\n=== テスト結果サマリ ===\n";
        
        $total_tests = count($this->test_results);
        $passed_tests = array_filter($this->test_results, function($result) {
            return $result['passed'];
        });
        $passed_count = count($passed_tests);
        $failed_count = $total_tests - $passed_count;
        
        echo "実行テスト数: {$total_tests}\n";
        echo "成功: {$passed_count}\n";
        echo "失敗: {$failed_count}\n";
        
        if ($failed_count === 0) {
            echo "\n🎉 すべてのテストが成功しました！\n";
        } else {
            echo "\n⚠️  {$failed_count}個のテストが失敗しました。\n";
        }
        
        echo "\n=== テスト終了 ===\n";
    }
    
    /**
     * アサーション用のヘルパーメソッド
     */
    private function assertEquals($expected, $actual, $message = "") {
        if ($expected !== $actual) {
            throw new Exception($message . " (期待値: {$expected}, 実際の値: {$actual})");
        }
    }
    
    private function assertNotFalse($value, $message = "") {
        if ($value === false) {
            throw new Exception($message);
        }
    }
    
    private function assertTrue($value, $message = "") {
        if ($value !== true) {
            throw new Exception($message);
        }
    }
    
    private function assertFalse($value, $message = "") {
        if ($value !== false) {
            throw new Exception($message);
        }
    }
}

// テストの実行
if (php_sapi_name() === 'cli') {
    // コマンドライン実行時のみテストを実行
    $test = new ProductTest();
    $test->runAllTests();
}
?>
