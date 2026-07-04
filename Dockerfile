# Dockerfile
FROM php:8.2-fpm

# system deps
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nano \
    libzip-dev \
    mysql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# copy only composer files first to cache installs
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --no-autoloader

# copy rest
COPY . .

# install vendors finally
RUN composer install --no-interaction --optimize-autoloader

# set permissions (Laravel needs writeable folders)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
