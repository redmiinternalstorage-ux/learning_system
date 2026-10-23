# ── Stage 1: Node build ───────────────────────────────────────────────────────
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package.json vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/
RUN npm install --frozen-lockfile && npm run build

# ── Stage 2: PHP app ──────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine

# System deps
RUN apk add --no-cache \
        nginx supervisor curl \
        libpng-dev libzip-dev zip unzip \
        sqlite sqlite-dev \
        freetype-dev libjpeg-turbo-dev libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql zip gd opcache bcmath pcntl \
    # Create nginx pid dir
    && mkdir -p /run/nginx

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
COPY --from=node-builder /app/public/build ./public/build

COPY docker/nginx.conf       /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini          /usr/local/etc/php/conf.d/app.ini
COPY docker/entrypoint.sh    /entrypoint.sh

RUN chmod +x /entrypoint.sh \
    && mkdir -p storage/logs \
               storage/framework/cache/data \
               storage/framework/sessions \
               storage/framework/views \
               bootstrap/cache database \
    && rm -f database/database.sqlite \
    && rm -f bootstrap/cache/services.php bootstrap/cache/packages.php \
    # Use printf for proper newlines in sh
    && printf 'APP_KEY=temp\nDB_CONNECTION=sqlite\nDB_DATABASE=/tmp/temp.sqlite\n' > .env \
    && php artisan package:discover --ansi \
    && rm .env \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 777 database

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
