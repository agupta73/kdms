# Apache + PHP (Cloud Run: listens on $PORT, default 8080)
# syntax=docker/dockerfile:1.6
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Vendor image PHP may lack ext-gd; final image installs gd. Lock still resolves.
RUN composer install --no-dev --no-interaction --no-scripts --optimize-autoloader --ignore-platform-req=ext-gd

FROM php:8.3-apache
RUN a2enmod rewrite \
    && apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo pdo_mysql mysqli zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/kdms
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY docker/kdms-vhost.conf /etc/apache2/sites-enabled/000-default.conf

RUN chown -R www-data:www-data /var/www/kdms

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENV PORT=8080
EXPOSE 8080
ENTRYPOINT ["/entrypoint.sh"]
