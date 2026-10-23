#!/bin/sh
set -e

cd /var/www/html

# ── Create .env from example if not present ───────────────────────────────────
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# ── Override .env values with environment variables (if set by Render) ────────
update_env() {
    local key=$1
    local val=$2
    if [ -n "$val" ]; then
        # replace existing key or append
        if grep -q "^${key}=" .env; then
            sed -i "s|^${key}=.*|${key}=${val}|" .env
        else
            echo "${key}=${val}" >> .env
        fi
    fi
}

update_env APP_NAME        "${APP_NAME}"
update_env APP_ENV         "${APP_ENV:-production}"
update_env APP_DEBUG       "${APP_DEBUG:-false}"
update_env APP_URL         "${APP_URL}"
update_env APP_KEY         "${APP_KEY}"
update_env DB_CONNECTION   "${DB_CONNECTION:-mysql}"
update_env DB_HOST         "${DB_HOST}"
update_env DB_PORT         "${DB_PORT:-3306}"
update_env DB_DATABASE     "${DB_DATABASE}"
update_env DB_USERNAME     "${DB_USERNAME}"
update_env DB_PASSWORD     "${DB_PASSWORD}"
update_env SESSION_DRIVER  "${SESSION_DRIVER:-database}"
update_env CACHE_STORE     "${CACHE_STORE:-database}"
update_env QUEUE_CONNECTION "${QUEUE_CONNECTION:-database}"
update_env MAIL_MAILER     "${MAIL_MAILER:-log}"
update_env MAIL_HOST       "${MAIL_HOST}"
update_env MAIL_PORT       "${MAIL_PORT}"
update_env MAIL_USERNAME   "${MAIL_USERNAME}"
update_env MAIL_PASSWORD   "${MAIL_PASSWORD}"
update_env MAIL_FROM_ADDRESS "${MAIL_FROM_ADDRESS}"
update_env MAIL_FROM_NAME  "${MAIL_FROM_NAME}"

# ── Generate app key only if not already set ──────────────────────────────────
if grep -q "^APP_KEY=$" .env || ! grep -q "^APP_KEY=" .env; then
    php artisan key:generate --force
fi

# ── Run migrations ────────────────────────────────────────────────────────────
php artisan migrate --force

# ── Cache for production performance ─────────────────────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Fix permissions ───────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── Start supervisor (nginx + php-fpm + queue worker) ────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
