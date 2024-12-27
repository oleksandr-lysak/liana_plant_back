FROM php:8.2-apache
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync

RUN apt-get update && apt-get install -y  \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    --no-install-recommends \
    && docker-php-ext-enable opcache \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql -j$(nproc) gd \
    && apt-get autoclean -y \
    && rm -rf /var/lib/apt/lists/*

RUN DEBIAN_FRONTEND=noninteractive apt-get update -q \
    && DEBIAN_FRONTEND=noninteractive apt-get install -qq -y \
      curl \
      git \
      zip unzip \
    && install-php-extensions \
      bcmath \
      bz2 \
      calendar \
      exif \
      gd \
      intl \
      ldap \
      mcrypt \
      memcached \
      mysqli \
      opcache \
      pdo_mysql \
      pdo_pgsql \
      pgsql \
      redis \
      soap \
      xsl \
      zip \
      sockets \
      iconv \
      mbstring \
      && a2enmod rewrite

# Update apache conf to point to application public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
# Enable headers module
RUN a2enmod rewrite headers

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Update uploads config
RUN echo "file_uploads = On\n" \
         "memory_limit = 1024M\n" \
         "upload_max_filesize = 512M\n" \
         "post_max_size = 512M\n" \
         "max_execution_time = 1200\n" \
         > /usr/local/etc/php/conf.d/uploads.ini

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Update npm to the latest version
RUN npm install npm@latest -g

# Copy application files
COPY . /var/www/html

RUN composer install
RUN apt-get update && apt-get install -y time

# Виводимо змінні середовища для перевірки
RUN echo "Current APP_ENV: $APP_ENV" && \
    cat /var/www/html/.env
RUN echo "Test step 1" && \
    echo "Test step 2"
# Виконуємо npm install
RUN echo "Installing npm dependencies..." && \
    npm install && \
    echo "npm dependencies installed successfully."

# Додаємо більше логування для кожного етапу
RUN set -x && \
    echo "Starting npm install..." && \
    npm install && \
    echo "npm install completed." && \
    echo "Loading environment variables from .env" && \
    export $(cat /var/www/html/.env | xargs) && \
    echo "Loaded environment variables:" && \
    env && \
    echo "APP_ENV: $APP_ENV" && \
    if [ "$APP_ENV" = "local" ]; then \
        echo "Running npm run dev"; \
        npm run dev --verbose || echo "npm run dev failed"; \
    else \
        echo "Running npm run build"; \
        npm run build --verbose || echo "npm run build failed"; \
    fi && \
    echo "NPM command completed successfully."



CMD ["php-fpm"]
