#!/bin/sh
set -e

cd /var/www/html

# DB type — always PostgreSQL (permanent external DB)
DB_CONN="pgsql"
DB_HOST="${DB_HOST:-dpg-dbv6jn8u01pc73cvlsf0-a.oregon-postgres.render.com}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE:-db_mt56}"
DB_USERNAME="${DB_USERNAME:-db_mt56_user}"
DB_PASSWORD="${DB_PASSWORD:-wcDzmgoffuZJTg1exS82NK4DWv5ZhXJZ}"

# HTTPS detection
SECURE_COOKIE="false"
if echo "${APP_URL:-}" | grep -q "^https"; then SECURE_COOKIE="true"; fi

# Mail transport — Render's free plan BLOCKS outbound SMTP (25/465/587),
# so prefer an HTTPS email API when a key is configured in Render env vars.
if [ -z "${MAIL_MAILER:-}" ]; then
    if [ -n "${BREVO_API_KEY:-}" ]; then MAIL_MAILER="brevo"
    elif [ -n "${RESEND_API_KEY:-}" ]; then MAIL_MAILER="resend"
    else MAIL_MAILER="smtp"; fi
fi
echo "[entrypoint] MAIL_MAILER=${MAIL_MAILER}"
if [ "$MAIL_MAILER" = "smtp" ]; then
    echo "[entrypoint] WARNING: SMTP selected. Render free instances block SMTP ports — set BREVO_API_KEY for delivery."
fi

# Write .env — all values with potential spaces MUST be double-quoted
{
printf 'APP_NAME="Learning Curve"\n'
printf 'APP_ENV=production\n'
printf 'APP_KEY="%s"\n'    "${APP_KEY:-}"
printf 'APP_DEBUG=false\n'
printf 'APP_URL="%s"\n'    "${APP_URL:-http://localhost}"
printf 'LOG_CHANNEL=stderr\n'
printf 'LOG_LEVEL=error\n'
printf 'DB_CONNECTION=%s\n'   "${DB_CONN}"
printf 'DB_HOST=%s\n'         "${DB_HOST}"
printf 'DB_PORT=%s\n'         "${DB_PORT}"
printf 'DB_DATABASE="%s"\n'   "${DB_DATABASE}"
printf 'DB_USERNAME="%s"\n'   "${DB_USERNAME}"
printf 'DB_PASSWORD="%s"\n'   "${DB_PASSWORD}"
printf 'DB_SSLMODE=require\n'
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
printf 'MAIL_MAILER=%s\n'           "${MAIL_MAILER}"
printf 'MAIL_HOST=%s\n'             "${MAIL_HOST:-smtp.gmail.com}"
printf 'MAIL_PORT=%s\n'             "${MAIL_PORT:-587}"
printf 'MAIL_ENCRYPTION=%s\n'       "${MAIL_ENCRYPTION:-tls}"
printf 'MAIL_USERNAME="%s"\n'       "${MAIL_USERNAME:-}"
printf 'MAIL_PASSWORD="%s"\n'       "${MAIL_PASSWORD:-}"
printf 'MAIL_FROM_ADDRESS="%s"\n'   "${MAIL_FROM_ADDRESS:-noreplychatzone@gmail.com}"
printf 'MAIL_FROM_NAME="%s"\n'      "${MAIL_FROM_NAME:-Learning Curve}"
printf 'BREVO_API_KEY="%s"\n'       "${BREVO_API_KEY:-}"
printf 'RESEND_API_KEY="%s"\n'      "${RESEND_API_KEY:-}"
printf 'FINANCE_PIN="%s"\n'         "${FINANCE_PIN:-123}"
} > .env

# APP_KEY
if [ -z "$APP_KEY" ]; then php artisan key:generate --force; fi

# PostgreSQL wait — retry until DB is ready
i=0
while [ $i -lt 30 ]; do
    php -r "new PDO('pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}','${DB_USERNAME}','${DB_PASSWORD}');" 2>/dev/null && break
    i=$((i+1)); sleep 1
done

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
