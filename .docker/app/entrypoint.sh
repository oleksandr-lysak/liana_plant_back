#!/bin/sh

set -e

git config --global --add safe.directory /var/www/html
if [ ! -d "/var/www/html/vendor" ]; then
  mkdir -p /var/www/html/vendor
  chown -R www-data:www-data /var/www/html/vendor
fi
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate

exec supervisord -c /etc/supervisor/supervisord.conf
