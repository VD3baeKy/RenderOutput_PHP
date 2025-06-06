#!/bin/bash
set -e

# Nginxのconfを環境変数PORTで置換
envsubst '$PORT' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# PHP-FPMをバックグラウンド起動
php-fpm8.1 -D

# Nginxをフォアグラウンド起動
nginx -g "daemon off;"
