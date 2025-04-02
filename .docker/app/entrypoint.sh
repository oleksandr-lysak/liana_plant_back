#!/bin/sh

set -e

git config --global --add safe.directory /var/www/html
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate

exec supervisord -c /etc/supervisor/supervisord.conf
