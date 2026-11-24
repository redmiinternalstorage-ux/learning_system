#!/bin/sh
set -e

echo "Starting PHP-FPM..."
php-fpm --daemonize --fpm-config /usr/local/etc/php-fpm.conf

echo "Starting Laravel Scheduler..."
# Run scheduler every minute in background
(while true; do php /var/www/html/artisan schedule:run --no-ansi >> /dev/null 2>&1; sleep 60; done) &

echo "Starting Nginx..."
exec nginx -g "daemon off;"
