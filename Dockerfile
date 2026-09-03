FROM php:8.3-cli-alpine

# Install system dependencies and complete PHP extensions for Laravel 11 & Filament
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    sqlite \
    sqlite-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        bcmath \
        intl \
        zip \
        gd \
        exif \
        pcntl \
        opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . /var/www

# Set environment for Composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install composer packages safely without running artisan scripts during container build
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction

# Ensure storage & database directories exist and have full write permissions
RUN mkdir -p /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/logs \
    /var/www/bootstrap/cache \
    /var/www/database \
    && touch /var/www/database/database.sqlite \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Expose default port
EXPOSE 8080

# Auto-migrate, auto-seed admin and start server
CMD ["sh", "-c", "touch /var/www/database/database.sqlite && chmod 777 /var/www/database/database.sqlite && php artisan package:discover --ansi && php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
