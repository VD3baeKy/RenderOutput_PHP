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
    postgresql-client \
    curl \
    && rm -rf /var/lib/apt/lists/*

# PHP設定ファイルをコピー
COPY ./php.ini /etc/php/8.1/fpm/conf.d/99-custom.ini

# PHP-FPMの設定
RUN sed -i 's/listen = \/run\/php\/php8.1-fpm.sock/listen = 0.0.0.0:9000/' /etc/php/8.1/fpm/pool.d/www.conf

# 作業ディレクトリの設定
WORKDIR /var/www/html

# 権限の設定
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm8.1", "-F"]
