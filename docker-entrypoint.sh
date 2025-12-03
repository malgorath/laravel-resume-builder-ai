#!/bin/bash
set -e

# Wait for database to be ready
echo "Waiting for database connection..."
while ! php artisan db:monitor --max=1 2>/dev/null; do
    sleep 2
done

echo "Database is ready!"

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and cache configuration
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm

