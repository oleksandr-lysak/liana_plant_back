#!/bin/sh

set -e

git config --global --add safe.directory /var/www/html
sudo chmod -R 777 /var/www/html/package-lock.json
npm install
npm run build
php artisan migrate

exec supervisord -c /etc/supervisor/supervisord.conf
