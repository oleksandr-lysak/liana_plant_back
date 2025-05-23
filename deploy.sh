#!/bin/bash

# Оновлюємо код
git pull origin main

# Зупиняємо контейнери
docker-compose -f docker-compose.prod.yml down

# Перебудовуємо та запускаємо контейнери
docker-compose -f docker-compose.prod.yml up -d --build

# Чекаємо поки контейнери запустяться
sleep 10

# Виконуємо команди всередині контейнера app
docker-compose -f docker-compose.prod.yml exec -T app bash -c "
    # Встановлюємо залежності
    composer install --no-dev --optimize-autoloader
    npm install
    npm run build

    # Очищаємо кеш
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    # Оновлюємо конфігурацію
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    # Запускаємо міграції
    php artisan migrate --force

    # Перезапускаємо черги
    php artisan queue:restart

    # Встановлюємо права
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
"

echo "Deploy completed successfully!" 