FROM ubuntu:22.04

# タイムゾーンの設定
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Tokyo

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    php8.1-fpm \
    php8.1-pgsql \
    php8.1-mbstring \
    php8.1-xml \
    php8.1-curl \
    php8.1-zip \
    php8.1-gd \
    php8.1-intl \
    php8.1-bcmath \
    postgresql-client \
    curl \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# PHP設定ファイルをコピー
COPY docker/php/php.ini /etc/php/8.1/fpm/conf.d/99-custom.ini

# PHP-FPMの設定を修正
RUN sed -i 's/listen = \/run\/php\/php8.1-fpm.sock/listen = 0.0.0.0:9000/' /etc/php/8.1/fpm/pool.d/www.conf && \
    sed -i 's/;listen.allowed_clients = 127.0.0.1/listen.allowed_clients = any/' /etc/php/8.1/fpm/pool.d/www.conf && \
    sed -i 's/pm.max_children = 5/pm.max_children = 20/' /etc/php/8.1/fpm/pool.d/www.conf && \
    sed -i 's/pm.start_servers = 2/pm.start_servers = 3/' /etc/php/8.1/fpm/pool.d/www.conf && \
    sed -i 's/pm.min_spare_servers = 1/pm.min_spare_servers = 2/' /etc/php/8.1/fpm/pool.d/www.conf && \
    sed -i 's/pm.max_spare_servers = 3/pm.max_spare_servers = 4/' /etc/php/8.1/fpm/pool.d/www.conf

# ピング用設定を追加
RUN echo 'ping.path = /ping' >> /etc/php/8.1/fpm/pool.d/www.conf

# 作業ディレクトリの設定
WORKDIR /var/www/html

# 権限の設定
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# PHP-FPMのポートを公開
EXPOSE 9000

# ヘルスチェック
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:9000/ping || exit 1

# PHP-FPMをフォアグラウンドで起動
CMD ["php-fpm8.1", "-F"]
