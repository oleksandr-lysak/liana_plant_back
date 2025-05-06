#!/bin/sh
set -e

echo "🚀 Entrypoint start"

# Safe Git
git config --global --add safe.directory /var/www/html

# File permissions
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod 664 /var/www/html/package-lock.json || true

# Xdebug
if php -m | grep -q xdebug; then
    echo "🧩 Xdebug already active"
elif [ "$APP_ENV" = "development" ] || [ "$APP_ENV" = "local" ]; then
    echo "🧩 Activate Xdebug for $APP_ENV environment"
    if [ -f /tmp/xdebug.ini ]; then
        cp /tmp/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
    else
        echo "⚠️  File /tmp/xdebug.ini not found, Xdebug will not activate"
    fi
else
    echo "🚫 Xdebug will not activate (environment: $APP_ENV)"
fi

# Laravel cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Frontend build
echo "📦 Building frontend..."
npm install
npm run build

# Laravel migrations
echo "🧱 Running migrations..."
php artisan migrate --force

# Add Laravel Scheduler to crontab
echo "* * * * * php /var/www/html/artisan schedule:run >> /dev/null 2>&1" | crontab -
crond -f -l 8 &

# Start supervisord
echo "🧩 Starting supervisord..."
exec su-exec www-data supervisord -c /etc/supervisor/supervisord.conf
