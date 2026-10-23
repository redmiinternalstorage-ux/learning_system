#!/bin/sh
set -e

cd /var/www/html

# ── Detect DB type: use MySQL if DB_HOST is set, else SQLite ─────────────────
if [ -n "$DB_HOST" ]; then
    DB_CONN="mysql"
else
    DB_CONN="sqlite"
fi

# ── Write .env using printf (avoids heredoc quote issues) ────────────────────
printf 'APP_NAME="%s"\n'           "${APP_NAME:-Learning Curve}"   > .env
printf 'APP_ENV=production\n'                                      >> .env
printf 'APP_KEY=%s\n'              "${APP_KEY:-}"                  >> .env
printf 'APP_DEBUG=true\n'                                          >> .env
printf 'APP_URL=%s\n'              "${APP_URL:-http://localhost}"   >> .env
printf '\n'                                                         >> .env
printf 'LOG_CHANNEL=stderr\n'                                      >> .env
printf 'LOG_LEVEL=error\n'                                         >> .env
printf '\n'                                                         >> .env
printf 'DB_CONNECTION=%s\n'        "${DB_CONN}"                    >> .env
printf 'DB_HOST=%s\n'              "${DB_HOST:-}"                  >> .env
printf 'DB_PORT=%s\n'              "${DB_PORT:-3306}"              >> .env
printf 'DB_DATABASE=%s\n'          "${DB_DATABASE:-/var/data/database.sqlite}" >> .env
printf 'DB_USERNAME=%s\n'          "${DB_USERNAME:-}"              >> .env
printf 'DB_PASSWORD=%s\n'          "${DB_PASSWORD:-}"              >> .env
printf '\n'                                                         >> .env
printf 'SESSION_DRIVER=file\n'                                     >> .env
printf 'SESSION_LIFETIME=120\n'                                    >> .env
printf 'SESSION_ENCRYPT=false\n'                                   >> .env
printf '\n'                                                         >> .env
printf 'BROADCAST_CONNECTION=log\n'                                >> .env
printf 'FILESYSTEM_DISK=local\n'                                   >> .env
printf 'QUEUE_CONNECTION=sync\n'                                   >> .env
printf 'CACHE_STORE=file\n'                                        >> .env
printf '\n'                                                         >> .env
printf 'MAIL_MAILER=smtp\n'                                        >> .env
printf 'MAIL_HOST=smtp.gmail.com\n'                                >> .env
printf 'MAIL_PORT=587\n'                                           >> .env
printf 'MAIL_ENCRYPTION=tls\n'                                     >> .env
printf 'MAIL_USERNAME=patoliyapatoliya5656@gmail.com\n'            >> .env
printf 'MAIL_PASSWORD="otbj rclb enxr wxol"\n'                    >> .env
printf 'MAIL_FROM_ADDRESS=patoliyapatoliya5656@gmail.com\n'        >> .env
printf 'MAIL_FROM_NAME="%s"\n'     "${APP_NAME:-Learning Curve}"   >> .env

# ── SQLite: create persistent dir and DB file if needed ──────────────────────
if [ "$DB_CONN" = "sqlite" ]; then
    mkdir -p /var/data
    touch /var/data/database.sqlite
    chmod 666 /var/data/database.sqlite
fi

# ── Generate APP_KEY if missing ───────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# ── Wait for MySQL if used (max 30s) ─────────────────────────────────────────
if [ "$DB_CONN" = "mysql" ]; then
    echo "Waiting for MySQL..."
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

# ── Cache config/routes/views ────────────────────────────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Permissions ───────────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache
chmod -R 777 database

# ── Start supervisor ──────────────────────────────────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
