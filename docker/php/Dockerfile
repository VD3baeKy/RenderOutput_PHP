# ---- ベースイメージ -------------------------------------------------
FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Tokyo
ENV PORT=10000            # Render が既定で渡す値に合わせる

# ---- 必要パッケージ -------------------------------------------------
RUN apt-get update && apt-get install -y \
    nginx-light \
    php8.1-fpm \
    php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl \
    php8.1-zip  php8.1-gd php8.1-intl php8.1-bcmath \
    postgresql-client curl \
 && rm -rf /var/lib/apt/lists/* && apt-get clean

# ---- PHP-FPM 設定 ---------------------------------------------------
RUN sed -i 's|listen = .*|listen = 127.0.0.1:9000|' /etc/php/8.1/fpm/pool.d/www.conf \
 && echo 'ping.path = /ping' >> /etc/php/8.1/fpm/pool.d/www.conf

# ---- Nginx 設定 -----------------------------------------------------
#   * Render が注入する $PORT を listen に使う
#   * すべてを PHP-FPM に渡すシンプル構成
RUN rm /etc/nginx/sites-enabled/default
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# default.conf 例（プロジェクト側ファイル）
#---------------------------------------------------------------
# server {
#     listen ${PORT};
#     root /var/www/html;
#
#     location / {
#         index index.php;
#         try_files $uri /index.php$is_args$args;
#     }
#
#     location ~ \.php$ {
#         include fastcgi_params;
#         fastcgi_pass 127.0.0.1:9000;
#         fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
#     }
# }
#---------------------------------------------------------------

# ---- ドキュメントルート -------------------------------------------
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

# ---- ポート公開 & ヘルスチェック -----------------------------------
EXPOSE ${PORT}
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD curl -f http://localhost:${PORT}/ || exit 1

# ---- 起動コマンド ---------------------------------------------------
# php-fpm をデーモン(-D)、nginx をフォアグラウンド(daemon off)
CMD php-fpm8.1 -D && nginx -g 'daemon off;'
