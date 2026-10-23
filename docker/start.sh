#!/bin/sh
set -e

echo "Starting PHP-FPM..."
php-fpm --daemonize --fpm-config /usr/local/etc/php-fpm.conf

echo "Starting Nginx..."
exec nginx -g "daemon off;"
