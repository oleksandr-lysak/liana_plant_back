FROM php:8.3-fpm

# Встановлюємо необхідні пакети
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    autoconf \
    build-essential \
    && pecl install redis \
    && docker-php-ext-enable redis

# Встановлюємо PHP розширення
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Встановлюємо Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Встановлюємо Node.js
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# Створюємо робочу директорію
WORKDIR /var/www

# Копіюємо файли проекту
COPY . /var/www

# Встановлюємо залежності
RUN composer install
RUN npm install

# Встановлюємо права
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Збираємо фронтенд
RUN npm run build
