#!/bin/sh
set -e

# Safe Git directory
git config --global --add safe.directory /var/www/html

# access to the files
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/package-lock.json || true

# Xdebug
if php -m | grep -q xdebug; then
    echo "🧩 Xdebug allready active"
elif [ "$APP_ENV" = "development" ] || [ "$APP_ENV" = "local" ]; then
    echo "🧩 Activate Xdebug for $APP_ENV enviroment"
    if [ -f /tmp/xdebug.ini ]; then
        cp /tmp/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
    else
        echo "⚠️  File /tmp/xdebug.ini not found, Xdebug will not activate"
    fi
else
    echo "🚫 Xdebug will not activate (enviroment: $APP_ENV)"
fi

# Laravel cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Deploy front-end
npm install
npm run build

# Migrations
php artisan migrate --force

# Start Supervisor
exec supervisord -c /etc/supervisor/supervisord.conf
