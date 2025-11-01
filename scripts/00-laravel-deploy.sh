#!/usr/bin/env bash
set -e

echo "===== Starting deployment ====="

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set!"
    exit 1
fi

echo "Running composer install..."
composer install --no-dev --working-dir=/var/www/html --optimize-autoloader --no-interaction

echo "Installing npm dependencies..."
npm ci --prefix /var/www/html

echo "Building frontend assets..."
npm run build --prefix /var/www/html

echo "Setting storage permissions..."
chmod -R 775 /var/www/html/storage || true
chmod -R 775 /var/www/html/bootstrap/cache || true

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public

echo "Clearing all caches..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force --isolated

echo "Checking migrations status..."
php artisan migrate:status || true

echo "Creating storage link..."
php artisan storage:link || true

echo "Publishing Livewire assets..."
php artisan livewire:publish --assets

echo "Optimizing..."
php artisan optimize

echo "===== Deployment finished successfully ====="
