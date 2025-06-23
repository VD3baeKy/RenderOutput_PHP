<?php

require_once __DIR__ . '/../vendor/autoload.php';
//Dotenv\Dotenv::createImmutable('/tmp')->load();

use App\Service\ProductService;

// DB設定
$db_host = $_ENV['DB_HOST'] ?? 'ep-bold-sound-ab1w4r9g-pooler.eu-west-2.aws.neon.tech';
$db_name = $_ENV['DB_NAME'] ?? 'RenderOutput_PHP';
$db_user = $_ENV['DB_USER'] ?? 'RenderOutput_PHP_owner';
$db_password = $_ENV['DB_PASSWORD'] ?? 'npg_3psvCBekh9dI';
$db_port = $_ENV['DB_PORT'] ?? '5432';

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
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-DSP0S2L4BK"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-DSP0S2L4BK');
    </script>
    <title>商品登録</title>
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
        <article class="registration">
            <h1>商品登録</h1>
            <div class="back">
                <a href="read.php" class="btn">&lt; 戻る</a>
            </div>
            <form action="create.php" method="post" class="registration-form">
                <div>
                    <label for="product_code">商品コード</label>
                    <input type="number" id="product_code" name="product_code" min="0" max="100000000" required>

                    <label for="product_name">商品名</label>
                    <input type="text" id="product_name" name="product_name" maxlength="50" required>

                    <label for="price">単価</label>
                    <input type="number" id="price" name="price" min="0" max="100000000" required>

                    <label for="stock_quantity">在庫数</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" min="0" max="100000000" required>

                    <label for="vendor_code">仕入先コード</label>
                    <select id="vendor_code" name="vendor_code" required>
                        <option disabled selected value>選択してください</option>
                        <?php
                        // 配列の中身を順番に取り出し、セレクトボックスの選択肢として出力する
                        foreach ($vendor_codes as $vendor_code) {
                            echo "<option value='{$vendor_code}'>{$vendor_code}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn" name="submit" value="create">登録</button>
            </form>
        </article>
    </main>
    <footer>
        <p class="copyright">&copy; 商品管理アプリ All rights reserved.</p>
    </footer>
</body>

</html>

