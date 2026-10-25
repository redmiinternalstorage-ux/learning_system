#!/bin/sh
set -e

cd /var/www/html

# DB type
if [ -n "$DB_HOST" ]; then DB_CONN="mysql"; else DB_CONN="sqlite"; fi

# HTTPS detection
SECURE_COOKIE="false"
if echo "${APP_URL:-}" | grep -q "^https"; then SECURE_COOKIE="true"; fi

# Write .env
{
printf 'APP_NAME="Learning Curve"\n'
printf 'APP_ENV=production\n'
printf 'APP_KEY=%s\n'  "${APP_KEY:-}"
printf 'APP_DEBUG=false\n'
printf 'APP_URL=%s\n'  "${APP_URL:-http://localhost}"
printf 'LOG_CHANNEL=stderr\n'
printf 'LOG_LEVEL=error\n'
printf 'DB_CONNECTION=%s\n'  "${DB_CONN}"
printf 'DB_HOST=%s\n'        "${DB_HOST:-}"
printf 'DB_PORT=%s\n'        "${DB_PORT:-3306}"
printf 'DB_DATABASE=%s\n'    "${DB_DATABASE:-/var/data/database.sqlite}"
printf 'DB_USERNAME=%s\n'    "${DB_USERNAME:-}"
printf 'DB_PASSWORD=%s\n'    "${DB_PASSWORD:-}"
printf 'SESSION_DRIVER=database\n'
printf 'SESSION_LIFETIME=120\n'
printf 'SESSION_ENCRYPT=false\n'
printf 'SESSION_SECURE_COOKIE=%s\n' "${SECURE_COOKIE}"
printf 'SESSION_SAME_SITE=lax\n'
printf 'SESSION_DOMAIN=\n'
printf 'BROADCAST_CONNECTION=log\n'
printf 'FILESYSTEM_DISK=local\n'
printf 'QUEUE_CONNECTION=sync\n'
printf 'CACHE_STORE=file\n'
printf 'MAIL_MAILER=smtp\n'
printf 'MAIL_HOST=smtp.gmail.com\n'
printf 'MAIL_PORT=587\n'
printf 'MAIL_ENCRYPTION=tls\n'
printf 'MAIL_USERNAME=patoliyapatoliya5656@gmail.com\n'
printf 'MAIL_PASSWORD="osgb zhhp vtmy swhy"\n'
printf 'MAIL_FROM_ADDRESS=patoliyapatoliya5656@gmail.com\n'
printf 'MAIL_FROM_NAME="Learning Curve"\n'
} > .env

# SQLite persistent disk
if [ "$DB_CONN" = "sqlite" ]; then
    mkdir -p /var/data
    touch /var/data/database.sqlite
    chown -R www-data:www-data /var/data
    chmod -R 777 /var/data
fi

# APP_KEY
if [ -z "$APP_KEY" ]; then php artisan key:generate --force; fi

# MySQL wait
if [ "$DB_CONN" = "mysql" ]; then
    i=0
    while [ $i -lt 30 ]; do
        php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}','${DB_USERNAME}','${DB_PASSWORD}');" 2>/dev/null && break
        i=$((i+1)); sleep 1
    done
fi

# Migrate
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

exec "$@"
