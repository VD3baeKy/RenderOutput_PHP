FROM ubuntu:22.04

# タイムゾーンの設定（対話的インストールを避けるため）
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Tokyo

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    nginx \
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

# PHP-FPMの設定
RUN sed -i 's/listen = \/run\/php\/php8.1-fpm.sock/listen = 127.0.0.1:9000/' /etc/php/8.1/fpm/pool.d/www.conf

# Nginxの設定ファイルをコピー
COPY default.conf /etc/nginx/sites-available/default

# アプリケーションファイルをコピー
COPY . /var/www/html/

# 権限の設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# PHP-FPMとNginxを起動するスクリプトを作成
RUN echo '#!/bin/bash\n\
service php8.1-fpm start\n\
nginx -g "daemon off;"' > /start.sh \
    && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
