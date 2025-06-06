FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Tokyo
ENV PORT=10000

RUN apt-get update && apt-get install -y \
    nginx-light \
    php8.1-fpm \
    php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl \
    php8.1-zip  php8.1-gd php8.1-intl php8.1-bcmath \
    postgresql-client curl \
    gettext \
 && rm -rf /var/lib/apt/lists/* && apt-get clean

#php-fpm
RUN sed -i 's|listen = .*|listen = 127.0.0.1:9000|' /etc/php/8.1/fpm/pool.d/www.conf \
 && echo 'ping.path = /ping' >> /etc/php/8.1/fpm/pool.d/www.conf

RUN rm /etc/nginx/sites-enabled/default
COPY docker/nginx/default.conf.template /etc/nginx/conf.d/default.conf.template

# start.shをコピーし、実行権付与
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

COPY /src /var/www/html
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html
EXPOSE ${PORT}
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD curl -f http://localhost:${PORT}/ || exit 1

CMD ["/start.sh"]
