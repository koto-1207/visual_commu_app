#!/usr/bin/env bash
set -e

echo "===== Initializing Laravel Application ====="

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set!"
    exit 1
fi

# Set permissions
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public

echo "Clearing caches..."
# Clear all caches first
php artisan cache:clear 2>&1 || true
php artisan config:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true

echo "Caching configuration and routes..."
# Cache config and routes (show errors if any)
php artisan config:cache 2>&1
php artisan route:cache 2>&1

echo "Running migrations..."
# Run migrations
php artisan migrate --force --isolated 2>&1 || echo "⚠️ Migrations skipped"

echo "Setting up storage and assets..."
# Storage link and Livewire assets
php artisan storage:link 2>&1 || true
php artisan livewire:publish --assets 2>&1 || true

echo "✅ Laravel initialized successfully"

# Start supervisord in the background
/start.sh &

# Wait for services to be ready
sleep 3

echo "✅ Services started - Application ready"

# Keep container running
wait

