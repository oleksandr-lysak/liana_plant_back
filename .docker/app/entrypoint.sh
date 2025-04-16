#!/bin/sh
set -euo pipefail
trap 'echo "Error on line $LINENO"' ERR

if [ ! -z "$TELEGRAM_TOKEN" ] && [ ! -z "$TELEGRAM_CHAT_ID" ]; then
    MESSAGE="✅ Deploy has been start <b>$(hostname)</b> enviroment <code>$APP_ENV</code> 🚀"
    curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_TOKEN/sendMessage" \
        -d chat_id="$TELEGRAM_CHAT_ID" \
        -d text="$MESSAGE" \
        -d parse_mode="HTML"
fi



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

if [ ! -z "$TELEGRAM_TOKEN" ] && [ ! -z "$TELEGRAM_CHAT_ID" ]; then
    MESSAGE="✅ Deploy success on <b>$(hostname)</b> with env <code>$APP_ENV</code> 🚀"
    curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_TOKEN/sendMessage" \
        -d chat_id="$TELEGRAM_CHAT_ID" \
        -d text="$MESSAGE" \
        -d parse_mode="HTML"
fi

exec supervisord -c /etc/supervisor/supervisord.conf
