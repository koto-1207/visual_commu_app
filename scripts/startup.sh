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

echo "Clearing all caches..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan event:clear || true

# Manually delete cache files to ensure clean state
echo "Removing old cache files..."
rm -f /var/www/html/bootstrap/cache/routes-v7.php || true
rm -f /var/www/html/bootstrap/cache/config.php || true
rm -f /var/www/html/bootstrap/cache/services.php || true
rm -f /var/www/html/bootstrap/cache/packages.php || true
rm -rf /var/www/html/storage/framework/cache/* || true
rm -rf /var/www/html/storage/framework/views/* || true

echo "Optimizing autoloader..."
composer dump-autoload --optimize --no-dev || true

echo "Caching configuration..."
php artisan config:cache

echo "Optimizing for production..."
php artisan optimize

echo "Verifying Volt can find views..."
test -d /var/www/html/resources/views/livewire/pages/auth && echo "✅ Auth views directory exists" || echo "❌ Auth views directory missing"

echo "Running migrations..."
php artisan migrate --force --isolated 2>&1 || echo "⚠️ Migrations skipped"

echo "Setting up storage and assets..."
php artisan storage:link 2>&1 || true
php artisan livewire:publish --assets 2>&1 || true

echo "✅ Laravel pre-initialization complete"

# Verify route cache
echo "Verifying route cache..."
if [ -f /var/www/html/bootstrap/cache/routes-v7.php ]; then
    echo "✅ Route cache file exists"
    echo "Listing authentication routes..."
    php artisan route:list --path=login || echo "Could not list login route"
    php artisan route:list --path=register || echo "Could not list register route"
else
    echo "❌ WARNING: Route cache file NOT created!"
fi

echo "✅ Application fully ready"

echo "Starting web services..."
# Use the original start script from the base image
exec /start.sh
