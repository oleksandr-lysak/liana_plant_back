#!/bin/sh
set -e

git config --global --add safe.directory /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/package-lock.json

if [ "$APP_ENV" = "development" ] || [ "$APP_ENV" = "local" ]; then
    echo "🧩 Enabling Xdebug"
    cp /tmp/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
npm install
npm run build
php artisan migrate

exec supervisord -c /etc/supervisor/supervisord.conf
