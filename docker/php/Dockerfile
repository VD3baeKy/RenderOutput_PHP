FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Tokyo
ENV PORT=10000

# 必要パッケージ
RUN apt-get update && apt-get install -y \
    nginx-extras \
    php8.1-fpm \
    php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl \
    php8.1-zip  php8.1-gd php8.1-intl php8.1-bcmath \
    postgresql-client curl \
    gettext \
    unzip \
 && rm -rf /var/lib/apt/lists/* && apt-get clean

# Composer本体の導入
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# nginx/php-fpm等の初期調整
RUN sed -i 's|listen = .*|listen = 127.0.0.1:9000|' /etc/php/8.1/fpm/pool.d/www.conf \
 && echo 'ping.path = /ping' >> /etc/php/8.1/fpm/pool.d/www.conf

RUN rm /etc/nginx/sites-enabled/default
COPY docker/nginx/default.conf.template /etc/nginx/conf.d/default.conf.template

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

COPY docker/error/404.html /var/www/html/error/404.html
COPY docker/error/50x.html /var/www/html/error/50x.html

# ワーキングディレクトリを/var/wwwに設定（htmlの親ディレクトリ）
WORKDIR /var/www

# composer.jsonとcomposer.lockをコピー
COPY composer.json composer.lock* ./

# vendorディレクトリを/var/wwwに作成
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# srcディレクトリの内容を/var/www/htmlにコピー
COPY src/ ./html/

# 権限設定
RUN chown -R www-data:www-data /var/www

# ワーキングディレクトリを戻す
WORKDIR /var/www/html

EXPOSE ${PORT}
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD curl -f http://localhost:${PORT}/ || exit 1

CMD ["/start.sh"]
