<?php

require_once __DIR__ . '/../vendor/autoload.php';
//Dotenv\Dotenv::createImmutable('/tmp')->load();

use App\Service\ProductService;

// DB接続設定
$db_host = $_ENV['DB_HOST'] ?? 'ep-bold-sound-ab1w4r9g-pooler.eu-west-2.aws.neon.tech';
$db_name = $_ENV['DB_NAME'] ?? 'RenderOutput_PHP';
$db_user = $_ENV['DB_USER'] ?? 'RenderOutput_PHP_owner';
$db_password = $_ENV['DB_PASSWORD'] ?? 'npg_3psvCBekh9dI';
$db_port = $_ENV['DB_PORT'] ?? '5432';

$dsn        = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";

try {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->exec("SET NAMES 'UTF8'");
} catch (PDOException $e) {
    exit($e->getMessage());
}

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if (!$id) {
        exit('ID指定がありません。');
    }
    $count = ProductService::deleteProduct($pdo, $id);
    $message = "商品を{$count}件削除しました。";
    header("Location: read.php?message={$message}");
    exit;
} catch (PDOException $e) {
    exit($e->getMessage());
}
