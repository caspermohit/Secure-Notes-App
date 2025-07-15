# Use the official PHP image with required extensions
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_pgsql pgsql pdo_mysql mbstring exif pcntl bcmath gd

# Install Node.js 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy existing application directory contents
COPY . /app

# Make build script executable
RUN chmod +x build.sh

# Set environment variables to handle Composer issues
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=600

# Install PHP dependencies with timeout handling
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Build React frontend
RUN cd frontend && npm install && npm run build && cd .. && cp -r frontend/build/* public/

# Set permissions for Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expose port 8000 and start the Laravel server
EXPOSE 8000 8001
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
