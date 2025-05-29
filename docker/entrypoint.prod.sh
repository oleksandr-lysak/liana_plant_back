#!/bin/sh

php artisan migrate --force

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

exec /opt/docker/bin/entrypoint supervisord 