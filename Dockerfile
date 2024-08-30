# Використовуємо офіційний образ PHP 8.2
FROM php:8.2

# Встановлюємо необхідні розширення PHP та інструменти
RUN apt-get update \
    && apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql zip

# Встановлюємо Xdebug через PECL
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

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
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Встановлюємо залежності PHP та JS
RUN composer install

# Налаштовуємо права доступу до директорій
RUN mkdir -p storage/app/public \
    && chown -R www-data:www-data storage \
    && chown -R www-data:www-data bootstrap/cache

# Відкриваємо порт 8000, якщо ви хочете використовувати локальний сервер Laravel
EXPOSE 8000

# Запускаємо команду для запуску веб-сервера PHP при старті контейнера
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
