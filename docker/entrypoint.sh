#!/bin/sh
set -e

cd /var/www/html

# ─────────────────────────────────────────────────────────────────────────────
# Step 1: Build .env from environment variables
# We write directly instead of relying on .env.example to avoid wrong defaults
# ─────────────────────────────────────────────────────────────────────────────
cat > .env <<EOF
APP_NAME="${APP_NAME:-Learning Curve}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=stderr
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-learning_system}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}

CACHE_STORE=${CACHE_STORE:-database}

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_HOST=${MAIL_HOST:-127.0.0.1}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME:-null}
MAIL_PASSWORD=${MAIL_PASSWORD:-null}
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-hello@example.com}"
MAIL_FROM_NAME="${APP_NAME:-Learning Curve}"
EOF

# ─────────────────────────────────────────────────────────────────────────────
# Step 2: Generate APP_KEY if not provided
# ─────────────────────────────────────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# ─────────────────────────────────────────────────────────────────────────────
# Step 3: Wait for DB to be ready (max 30s)
# ─────────────────────────────────────────────────────────────────────────────
echo "Waiting for database..."
for i in $(seq 1 30); do
    php -r "
        try {
            \$pdo = new PDO(
                '${DB_CONNECTION:-mysql}:host=${DB_HOST:-127.0.0.1};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-learning_system}',
                '${DB_USERNAME:-root}',
                '${DB_PASSWORD:-}'
            );
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    " && break || sleep 1
done
echo "Database is ready."

# ─────────────────────────────────────────────────────────────────────────────
# Step 4: Run migrations
# ─────────────────────────────────────────────────────────────────────────────
php artisan migrate --force

# ─────────────────────────────────────────────────────────────────────────────
# Step 5: Cache config/routes/views for performance
# ─────────────────────────────────────────────────────────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─────────────────────────────────────────────────────────────────────────────
# Step 6: Fix permissions and start
# ─────────────────────────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
