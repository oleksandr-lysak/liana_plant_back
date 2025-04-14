ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-alpine

RUN apk update && apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    freetype-dev \
    libpng-dev \
    jpeg-dev \
    gcc \
    make \
    autoconf \
    libc-dev \
    pkgconf \
    libc6-compat \
    nodejs \
    npm \
    linux-headers \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli zip intl mbstring gd \
    && pecl install redis && docker-php-ext-enable redis \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

COPY . /var/www/html

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN apk add supervisor

COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY worker.conf /etc/supervisor/conf.d/worker.conf
RUN mkdir -p /var/run/supervisor /var/log/supervisor \
    && chmod -R 777 /var/run/supervisor /var/log/supervisor
RUN chmod -R 777 /var/log/supervisor

RUN chown -R www-data:www-data /var/www/html

RUN git config --global --add safe.directory /var/www/html

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN mkdir -p /var/www/html/vendor \
    && chown -R www-data:www-data /var/www/html/vendor

USER www-data

RUN composer install --no-scripts --no-progress --no-interaction --prefer-dist

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
