#!/bin/bash
set -e

# Secretファイルの権限を修正
if [ -f /etc/secrets/.env ]; then
    echo "[start.sh] Fixing permissions for /etc/secrets/.env"
    chown www-data:www-data /etc/secrets/.env
    chmod 640 /etc/secrets/.env
fi

# Nginxのconfを環境変数PORTで置換
envsubst '$PORT' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# PHP-FPMをバックグラウンド起動
php-fpm8.1 -D

# Nginxをフォアグラウンド起動
nginx -g "daemon off;"
