<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
//Dotenv\Dotenv::createImmutable('/tmp')->load();

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
    $order = $_GET['order'] ?? 'ASC';  // デフォルトはASC
    $products = ProductService::searchProducts($pdo, $keyword, $order);
} catch (PDOException $e) {
    exit($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧</title>
    <link rel="stylesheet" href="css/style.css">

    <!-- Google Fontsの読み込み -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav>
            <a href="index.php">商品管理アプリ</a>
        </nav>
    </header>
    <main>
        <article class="products">
            <h1>商品一覧</h1>
            <?php
            // （商品の登録・編集・削除後）messageパラメータの値を受け取っていれば、それを表示する
            if (isset($_GET['message'])) {
                echo "<p class='success'>{$_GET['message']}</p>";
            }
            ?>
            <div class="products-ui">
                <div>
                    <a href="read.php?order=desc&keyword=<?= $keyword ?>">
                        <img src="images/desc.png" alt="降順に並び替え" class="sort-img">
                    </a>
                    <a href="read.php?order=asc&keyword=<?= $keyword ?>">
                        <img src="images/asc.png" alt="昇順に並び替え" class="sort-img">
                    </a>
                    <form action="read.php" method="get" class="search-form">
                        <input type="hidden" name="order" value="<?= $order ?>">
                        <input type="text" class="search-box" placeholder="商品名で検索" name="keyword" value="<?= $keyword ?>">
                    </form>
                </div>
                <a href="create.php" class="btn">商品登録</a>
            </div>
            <table class="products-table">
                <tr>
                    <th>商品コード</th>
                    <th>商品名</th>
                    <th>単価</th>
                    <th>在庫数</th>
                    <th>仕入先コード</th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
                <?php
                // 配列の中身を順番に取り出し、表形式で出力する
                foreach ($products as $product) {
                    $table_row = "
                        <tr>
                        <td>{$product['product_code']}</td>
                        <td>{$product['product_name']}</td>
                        <td>{$product['price']}</td>
                        <td>{$product['stock_quantity']}</td>
                        <td>{$product['vendor_code']}</td>
                        <td><a href='update.php?id={$product['id']}'><img src='images/edit.png' alt='編集' class='edit-icon'></a></td>
                        <td><a href='delete.php?id={$product['id']}'><img src='images/delete.png' alt='削除' class='delete-icon'></a></td>                        
                        </tr>                    
                    ";
                    echo $table_row;
                }
                ?>
            </table>
        </article>
    </main>
    <footer>
        <p class="copyright">&copy; 商品管理アプリ All rights reserved.</p>
    </footer>
</body>

</html>
