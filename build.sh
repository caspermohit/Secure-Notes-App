#!/bin/bash

# Install Composer
curl -sS https://getcomposer.org/installer | php

# Install PHP dependencies
php composer.phar install --no-dev --optimize-autoloader

# Build frontend
cd frontend
npm install
npm run build
cd ..

# Copy built React app to public directory
cp -r frontend/build/* public/

# Generate Laravel key and optimize
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build completed successfully!" 