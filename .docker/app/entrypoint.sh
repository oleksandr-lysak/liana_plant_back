#!/bin/sh
set -e

# Safe Git directory
git config --global --add safe.directory /var/www/html

# Права доступу
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/package-lock.json || true

# Xdebug
if php -m | grep -q xdebug; then
    echo "🧩 Xdebug вже активований"
elif [ "$APP_ENV" = "development" ] || [ "$APP_ENV" = "local" ]; then
    echo "🧩 Активуємо Xdebug для $APP_ENV середовища"
    if [ -f /tmp/xdebug.ini ]; then
        cp /tmp/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
    else
        echo "⚠️  Файл /tmp/xdebug.ini не знайдено, Xdebug не буде активовано"
    fi
else
    echo "🚫 Xdebug не буде активовано (оточення: $APP_ENV)"
fi

# Laravel кешування
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Збірка front-end
npm install
npm run build

# Міграції
php artisan migrate --force

# Запуск Supervisor
exec supervisord -c /etc/supervisor/supervisord.conf
