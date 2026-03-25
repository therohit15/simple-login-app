FROM php:8.2-apache

# Install system deps and PHP extensions needed by the app.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libssl-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && pecl install mongodb-1.21.2 \
    && docker-php-ext-enable mongodb \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer binary.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app first to leverage Docker layer caching for composer install.
COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 80
