#!/bin/bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Run database seeder (will only run if db is empty)
php artisan db:seed --force

# Start Apache in the foreground
apache2-foreground
