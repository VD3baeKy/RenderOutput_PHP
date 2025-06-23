#!/bin/bash
set -e

# /tmp ディレクトリに全ユーザーがアクセスできるようにパーミッションを設定
# これにより、www-dataユーザーが/tmpディレクトリ内に入ってファイルを探せるようになる
chmod 1777 /tmp
echo "[start.sh] Set permissions for /tmp directory"

# Secretファイルをweb公開外領域（/tmp）にコピー
if [ -f /cpenv.txt ]; then
    cp /cpenv.txt /tmp/.env
    chown www-data:www-data /tmp/.env
    chmod 640 /tmp/.env
    echo "[start.sh] Copied /cpenv.txt to /tmp/.env"
fi

# Nginxのconfを環境変数PORTで置換
envsubst '$PORT' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# PHP-FPMをバックグラウンド起動
php-fpm8.1 -D

# Nginxをフォアグラウンド起動
nginx -g "daemon off;"
