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

# Clear and cache (suppress verbose output)
php artisan config:cache > /dev/null 2>&1
php artisan route:cache > /dev/null 2>&1
php artisan view:cache > /dev/null 2>&1

# Run migrations silently
php artisan migrate --force --isolated > /dev/null 2>&1 || true

# Storage link and Livewire assets
php artisan storage:link > /dev/null 2>&1 || true
php artisan livewire:publish --assets > /dev/null 2>&1 || true

echo "✅ Laravel initialized successfully"

# Start supervisord in the background
/start.sh &

# Wait for services to be ready
sleep 2

echo "✅ Services started - Application ready"

# Keep container running
wait

