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
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Also manually delete cache files to ensure clean slate
echo "Removing old cache files..."
rm -f /var/www/html/bootstrap/cache/routes-v7.php || true
rm -f /var/www/html/bootstrap/cache/config.php || true
rm -rf /var/www/html/storage/framework/cache/* || true
rm -rf /var/www/html/storage/framework/views/* || true

echo "Caching configuration..."
# Cache config
php artisan config:cache

echo "Running migrations..."
# Run migrations
php artisan migrate --force --isolated 2>&1 || echo "⚠️ Migrations skipped"

echo "Setting up storage and assets..."
# Storage link and Livewire assets
php artisan storage:link 2>&1 || true
php artisan livewire:publish --assets 2>&1 || true

echo "✅ Laravel initialized successfully"
echo "Starting supervisord..."

# Start supervisord in the background
/usr/bin/supervisord -c /etc/supervisord.conf &
SUPERVISOR_PID=$!

# Wait for PHP-FPM and nginx to start
sleep 3

echo "Services started, now caching routes..."
# Cache routes AFTER services are running
# This ensures any composer scripts run by /start.sh don't interfere
php artisan route:cache

echo "Verifying route cache..."
if [ -f /var/www/html/bootstrap/cache/routes-v7.php ]; then
    echo "✅ Route cache file exists"
    ls -lh /var/www/html/bootstrap/cache/routes-v7.php
    
    echo "Checking for /login route..."
    php artisan route:list --path=login || echo "⚠️ Could not list routes"
else
    echo "❌ ERROR: Route cache file NOT created!"
fi

echo "✅ Application ready"

# Wait for supervisord to finish (keeps container running)
wait $SUPERVISOR_PID
