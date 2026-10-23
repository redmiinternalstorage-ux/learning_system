# ── Stage 1: Node build (compile assets) ─────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm install --frozen-lockfile && npm run build

# ── Stage 2: PHP / Laravel app ────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS app

# System deps + PHP extensions
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng-dev \
        libzip-dev \
        zip \
        unzip \
        sqlite \
        sqlite-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        zip \
        gd \
        opcache \
        bcmath \
        pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first (better layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev)
RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts

# Copy full application source
COPY . .

# Copy compiled assets from node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Copy configs
COPY docker/nginx.conf       /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini          /usr/local/etc/php/conf.d/app.ini
COPY docker/entrypoint.sh    /entrypoint.sh

RUN chmod +x /entrypoint.sh \
    && mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
