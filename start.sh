#!/bin/bash
set -e

# Secretファイルをweb公開外領域（/tmp）にコピー
if [ -f /etc/secrets/.env ]; then
    cp /etc/secrets/.env /tmp/.env
    chown www-data:www-data /tmp/.env
    chmod 640 /tmp/.env
    echo "[start.sh] Copied /etc/secrets/.env to /tmp/.env"
fi

# Nginxのconfを環境変数PORTで置換
envsubst '$PORT' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# PHP-FPMをバックグラウンド起動
php-fpm8.1 -D

# Nginxをフォアグラウンド起動
nginx -g "daemon off;"
