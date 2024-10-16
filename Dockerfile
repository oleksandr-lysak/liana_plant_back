
FROM php:8.2

RUN apt-get update \
    && apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y nodejs npm

RUN apt-get install -y git

WORKDIR /var/www/html

COPY . .
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN composer install

RUN mkdir -p storage/app/public \
    && chown -R www-data:www-data storage \
    && chown -R www-data:www-data bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
