# Використовуємо офіційний образ PHP 8.2
FROM php:8.2

# Встановлюємо деякі необхідні розширення PHP та інструменти
RUN apt-get update \
    && apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql zip

# Встановлюємо Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Встановлюємо Node.js та npm
RUN apt-get install -y nodejs npm

# Встановлюємо Git
RUN apt-get install -y git

# Створюємо робочу директорію для проекту Laravel
WORKDIR /var/www/html

# Копіюємо файли проекту Laravel в контейнер
COPY . .

# Встановлюємо залежності PHP та JS
RUN composer install

# Відкриваємо порт 8000, якщо ви хочете використовувати локальний сервер Laravel
EXPOSE 8000

# Запускаємо команду для запуску веб-сервера PHP при старті контейнера
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
CMD ["php", "artisan", "storage:link"]
