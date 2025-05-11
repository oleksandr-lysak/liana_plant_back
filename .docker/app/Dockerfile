ARG PHP_VERSION
ARG APP_ENV

FROM php:${PHP_VERSION}-fpm-alpine

# Встановлення потрібних пакетів та PHP-розширень
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
    cronie \
    su-exec \
    supervisor \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli zip intl mbstring gd \
    && pecl install redis \
    && pecl install xdebug \
    && docker-php-ext-enable redis \
    && docker-php-ext-enable xdebug \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/cache/apk/* /tmp/* \
    && rm -rf /var/lib/apt/lists/*

# Робоча директорія
WORKDIR /var/www/html

# Копіюємо код проекту
COPY . /var/www/html

# Конфігурація Xdebug
COPY .docker/app/xdebug.ini /tmp/xdebug.ini

# Supervisor конфігурація
COPY .docker/app/supervisord.conf /etc/supervisor/supervisord.conf
COPY .docker/app/worker.conf /etc/supervisor/conf.d/worker.conf

# Створюємо необхідні директорії з правильними правами доступу
RUN mkdir -p /var/log/supervisor /var/run/supervisor /root/.cache/crontab \
    && chown -R www-data:www-data /var/log/supervisor /var/run/supervisor /root/.cache \
    && chmod -R 755 /var/log/supervisor /var/run/supervisor /root/.cache

# Права на файли проекту
RUN chown -R www-data:www-data /var/www/html

# Дозволити Git працювати в контейнері
RUN git config --global --add safe.directory /var/www/html

# Встановлення залежностей PHP через Composer
RUN composer install --no-scripts --no-progress --no-interaction --prefer-dist \
    && chown -R www-data:www-data /var/www/html/vendor

# Копіюємо entrypoint
COPY .docker/app/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Права на storage та cache
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Запуск entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Запуск під www-data (можеш зняти цей рядок, якщо хочеш запускати як root)
#USER www-data
