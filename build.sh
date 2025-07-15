#!/bin/bash

# Exit on any error
set -e

echo "Starting build process..."

# Install Composer
echo "Installing Composer..."
curl -sS https://getcomposer.org/installer | php

# Install PHP dependencies with verbose output
echo "Installing PHP dependencies..."
php composer.phar install --no-dev --optimize-autoloader --verbose

# Verify vendor directory exists
if [ ! -d "vendor" ]; then
    echo "Error: vendor directory not found after composer install"
    echo "Current directory: $(pwd)"
    echo "Directory contents:"
    ls -la
    exit 1
fi

echo "PHP dependencies installed successfully"

# Build frontend with verbose output
echo "Building React frontend..."
cd frontend
echo "Installing npm dependencies..."
npm install --verbose
echo "Running npm build..."
npm run build --verbose
cd ..

# Verify frontend build exists
if [ ! -d "frontend/build" ]; then
    echo "Error: frontend/build directory not found after npm run build"
    echo "Frontend directory contents:"
    ls -la frontend/
    exit 1
fi

echo "React frontend built successfully"

# Copy built React app to public directory
echo "Copying React build to public directory..."
cp -r frontend/build/* public/

echo "React build copied to public directory"

# Only run Laravel commands if vendor exists and we're not in a failing state
if [ -d "vendor" ]; then
    echo "Running Laravel optimizations..."
    php artisan key:generate --force || echo "Warning: Could not generate key"
    php artisan config:cache || echo "Warning: Could not cache config"
    php artisan route:cache || echo "Warning: Could not cache routes"
    php artisan view:cache || echo "Warning: Could not cache views"
fi

echo "Build completed successfully!" 