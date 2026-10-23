# ── Stage 1: Node build ───────────────────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app
COPY package.json vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/
RUN npm install --frozen-lockfile && npm run build

# ── Stage 2: PHP app ──────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS app

# System deps + PHP extensions
RUN apk add --no-cache \
        nginx supervisor curl \
        libpng-dev libzip-dev zip unzip \
        sqlite sqlite-dev \
        freetype-dev libjpeg-turbo-dev libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo pdo_sqlite pdo_mysql \
        zip gd opcache bcmath pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP deps (no dev)
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev --no-interaction \
        --prefer-dist --optimize-autoloader \
        --no-scripts

# Copy app source
COPY . .

# Copy compiled frontend assets
COPY --from=node-builder /app/public/build ./public/build

# Copy docker configs
COPY docker/nginx.conf       /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini          /usr/local/etc/php/conf.d/app.ini
COPY docker/entrypoint.sh    /entrypoint.sh

RUN chmod +x /entrypoint.sh \
    # Create required directories
    && mkdir -p storage/logs \
               storage/framework/cache/data \
               storage/framework/sessions \
               storage/framework/views \
               bootstrap/cache \
               database \
    # Always delete committed SQLite DB — fresh one created at runtime by entrypoint
    && rm -f database/database.sqlite \
    # Remove stale dev-machine bootstrap cache (has dev-only providers)
    && rm -f bootstrap/cache/services.php bootstrap/cache/packages.php \
    # Create a minimal .env just for package:discover to run
    && echo "APP_KEY=temp\nDB_CONNECTION=sqlite\nDB_DATABASE=/var/www/html/database/database.sqlite" > .env \
    # Regenerate package manifest (production packages only)
    && php artisan package:discover --ansi \
    # Remove temp .env — real one is created by entrypoint.sh at runtime
    && rm .env \
    # Fix ownership
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 777 database

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
