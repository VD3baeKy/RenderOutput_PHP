<?php

require_once __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable('/tmp')->load();

use App\Service\ProductService;

// DB設定
$db_host = $_ENV['DB_HOST'] ?? 'postgres';
$db_name = $_ENV['DB_NAME'] ?? 'product_management';
$db_user = $_ENV['DB_USER'] ?? 'app_user';
$db_password = $_ENV['DB_PASSWORD'] ?? 'app_password';
$db_port = $_ENV['DB_PORT'] ?? '5432';
$dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";

try {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->exec("SET NAMES 'UTF8'");
} catch (PDOException $e) {
    exit($e->getMessage());
}

if (isset($_POST['submit'])) {
    try {
        $count = ProductService::createProduct($pdo, [
            'product_code'   => $_POST['product_code'],
            'product_name'   => $_POST['product_name'],
            'price'          => $_POST['price'],
            'stock_quantity' => $_POST['stock_quantity'],
            'vendor_code'    => $_POST['vendor_code'],
        ]);
        $message = "商品を{$count}件登録しました。";
        header("Location: read.php?message={$message}");
        exit;
    } catch (PDOException $e) {
        exit($e->getMessage());
    }
}

try {
    $vendor_codes = ProductService::getAllVendorCodes($pdo);
} catch (PDOException $e) {
    exit($e->getMessage());
}
?>

<!-- HTML部はそのまま -->
