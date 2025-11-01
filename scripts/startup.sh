#!/usr/bin/env bash
set -e

echo "===== Container Startup: Initializing Laravel ====="

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set!"
    exit 1
fi

echo "Setting storage permissions..."
chmod -R 775 /var/www/html/storage || true
chmod -R 775 /var/www/html/bootstrap/cache || true
chown -R nginx:nginx /var/www/html/storage || true
chown -R nginx:nginx /var/www/html/bootstrap/cache || true

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
php artisan migrate --force --isolated || echo "⚠️ Migration failed (database may not be ready yet)"

echo "Checking migrations status..."
php artisan migrate:status || true

echo "Creating storage link..."
php artisan storage:link || true

echo "Publishing Livewire assets..."
php artisan livewire:publish --assets || true

echo "Optimizing..."
php artisan optimize

echo "===== Laravel initialization complete ====="
echo "===== Starting nginx and php-fpm ====="

# Start supervisord (which will start nginx and php-fpm)
exec /start.sh

