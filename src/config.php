<?php
// データベース接続設定
$db_host = $_ENV['DB_HOST'] ?? 'postgres';
$db_name = $_ENV['DB_NAME'] ?? 'product_management';
$db_user = $_ENV['DB_USER'] ?? 'app_user';
$db_password = $_ENV['DB_PASSWORD'] ?? 'app_password';
$db_port = $_ENV['DB_PORT'] ?? '5432';

// PostgreSQL用のDSN
$dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";
$pdo->exec("SET NAMES 'UTF8'");

// 接続をテストする関数
function getDatabaseConnection() {
    global $dsn, $db_user, $db_password;
    
    try {
        $pdo = new PDO($dsn, $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("データベース接続エラー: " . $e->getMessage());
    }
}

// グローバルで使用する接続情報（後方互換性のため）
$GLOBALS['dsn'] = $dsn;
$GLOBALS['user'] = $db_user;
$GLOBALS['password'] = $db_password;
?>
