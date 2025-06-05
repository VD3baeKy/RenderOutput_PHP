# ベースイメージとしてUbuntuを指定
FROM ubuntu:20.04

# 環境変数の設定
ENV DEBIAN_FRONTEND=noninteractive

# 必要なパッケージのインストール
RUN apt-get update && \
    apt-get install -y software-properties-common && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get install -y \
    php8.1 \
    php8.1-mysql \
    php8.1-fpm \
    nginx \
    && apt-get clean

# PHP設定ファイルのコピー
COPY ./docker/php/php.ini /etc/php/8.1/fpm/php.ini

# Nginx設定ファイルのコピー
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default

# ソースコードのコピー
COPY ./src /var/www/html

# NginxとPHP-FPMの起動
CMD service php8.1-fpm start && nginx -g 'daemon off;'
