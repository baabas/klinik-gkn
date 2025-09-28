# syntax=docker/dockerfile:1.6
FROM composer:2 AS vendor
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --no-scripts
COPY . .
RUN composer dump-autoload --optimize

FROM node:20-bookworm AS assets
WORKDIR /var/www/html
COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
RUN npm ci
RUN npm run build

FROM php:8.2-fpm-bullseye AS runtime
ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libicu-dev libonig-dev libxml2-dev pkg-config libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd intl zip bcmath pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html

# Salin source code + vendor + hasil build Vite
COPY --from=vendor --chown=${UID}:${GID} /var/www/html /var/www/html
COPY --from=assets --chown=${UID}:${GID} /var/www/html/public/build /var/www/html/public/build

# Pastikan izin storage & cache benar
RUN chown -R ${UID}:${GID} storage bootstrap/cache

USER ${UID}:${GID}
CMD ["php-fpm"]
