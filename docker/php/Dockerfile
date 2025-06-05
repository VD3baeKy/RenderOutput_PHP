# イメージを取得
FROM php:8.1.18-fpm

# 現在のフォルダ位置を確認
RUN pwd

# 独自のphp.iniファイル(PHPの設定ファイル)を 
# コンテナ内の/usr/local/etc/php/ディレクトリにコピー
COPY php.ini /usr/local/etc/php/

# パッケージやPHPの拡張モジュールをインストールするコマンド　を実行
RUN apt-get update && apt-get install -y \
	git \
	curl \
	zip \
	unzip \
    libpq-dev && docker-php-ext-install pdo pdo_pgsql

# 作業ディレクトリを/var/wwwに設定
WORKDIR /var/www

