<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable('/tmp')->load();

use App\Service\ProductService;

$db_host = $_ENV['DB_HOST'] ?? 'postgres';
$db_name = $_ENV['DB_NAME'] ?? 'product_management';
$db_user = $_ENV['DB_USER'] ?? 'app_user';
$db_password = $_ENV['DB_PASSWORD'] ?? 'app_password';
$db_port = $_ENV['DB_PORT'] ?? '5432';
$dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";

// パラメータ取得
$order = isset($_GET['order']) ? $_GET['order'] : null;
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

try {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->exec("SET NAMES 'UTF8'");
    $products = ProductService::searchProducts($pdo, $keyword, $order);
} catch (PDOException $e) {
    exit($e->getMessage());
}
?>

<!-- HTML部分は元のまま（$products, $keyword, $orderだけ使えばOK） -->
