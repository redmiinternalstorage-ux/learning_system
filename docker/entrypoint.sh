#!/bin/sh
set -e

cd /var/www/html

# ── Detect DB type: use MySQL if DB_HOST is set, else SQLite ─────────────────
if [ -n "$DB_HOST" ]; then
    DB_CONN="mysql"
else
    DB_CONN="sqlite"
fi

# ── Write .env ────────────────────────────────────────────────────────────────
cat > .env << ENVEOF
APP_NAME="${APP_NAME:-Learning Curve}"
APP_ENV=production
APP_KEY=${APP_KEY:-}
APP_DEBUG=false
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=${DB_CONN}
DB_HOST=${DB_HOST:-}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-/var/www/html/database/database.sqlite}
DB_USERNAME=${DB_USERNAME:-}
DB_PASSWORD=${DB_PASSWORD:-}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_HOST=${MAIL_HOST:-}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME:-}
MAIL_PASSWORD=${MAIL_PASSWORD:-}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-hello@example.com}
MAIL_FROM_NAME="${APP_NAME:-Learning Curve}"
ENVEOF

# ── SQLite: create DB file if needed ─────────────────────────────────────────
if [ "$DB_CONN" = "sqlite" ]; then
    touch /var/www/html/database/database.sqlite
    chmod 666 /var/www/html/database/database.sqlite
fi

# ── Generate APP_KEY if missing ───────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# ── Wait for MySQL if used (max 30s) ─────────────────────────────────────────
if [ "$DB_CONN" = "mysql" ]; then
    echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}..."
    i=0
    while [ $i -lt 30 ]; do
        php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}','${DB_USERNAME}','${DB_PASSWORD}');" 2>/dev/null && break
        i=$((i+1))
        sleep 1
    done
    echo "MySQL ready."
fi

# ── Run migrations ────────────────────────────────────────────────────────────
php artisan migrate --force

# ── Cache for production ──────────────────────────────────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Permissions ───────────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache
chmod -R 777 database

# ── Start server ──────────────────────────────────────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
