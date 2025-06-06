<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// データベース接続設定
$db_host = $_ENV['DB_HOST'] ?? 'postgres';
$db_name = $_ENV['DB_NAME'] ?? 'product_management';
$db_user = $_ENV['DB_USER'] ?? 'app_user';
$db_password = $_ENV['DB_PASSWORD'] ?? 'app_password';
$db_port = $_ENV['DB_PORT'] ?? '5432';

// PostgreSQL用のDSN
$dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";
$pdo->exec("SET NAMES 'UTF8'");

try {
    $pdo = new PDO($dsn, $user, $password);

    // idカラムの値をプレースホルダ（:id）に置き換えたSQL文をあらかじめ用意する
    $sql_delete = 'DELETE FROM products WHERE id = :id';
    $stmt_delete = $pdo->prepare($sql_delete);

    // bindValue()メソッドを使って実際の値をプレースホルダにバインドする（割り当てる）
    $stmt_delete->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    // SQL文を実行する
    $stmt_delete->execute();

    // 削除した件数を取得する
    $count = $stmt_delete->rowCount();

    $message = "商品を{$count}件削除しました。";

    // 商品一覧ページにリダイレクトさせる（同時にmessageパラメータも渡す）
    header("Location: read.php?message={$message}");
} catch (PDOException $e) {
    exit($e->getMessage());
}
