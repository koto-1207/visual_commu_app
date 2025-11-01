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
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Manually delete cache files
echo "Removing old cache files..."
rm -f /var/www/html/bootstrap/cache/routes-v7.php || true
rm -f /var/www/html/bootstrap/cache/config.php || true
rm -rf /var/www/html/storage/framework/cache/* || true
rm -rf /var/www/html/storage/framework/views/* || true

echo "Caching configuration..."
php artisan config:cache

echo "Running migrations..."
php artisan migrate --force --isolated 2>&1 || echo "⚠️ Migrations skipped"

echo "Setting up storage and assets..."
php artisan storage:link 2>&1 || true
php artisan livewire:publish --assets 2>&1 || true

echo "✅ Laravel pre-initialization complete"

# Create a custom entrypoint script that will run route:cache after services start
cat > /tmp/post-start.sh << 'POSTSTART'
#!/bin/bash
sleep 5
echo "Caching routes after services started..."
php artisan route:cache
echo "Verifying route cache..."
if [ -f /var/www/html/bootstrap/cache/routes-v7.php ]; then
    echo "✅ Route cache file exists"
    php artisan route:list --path=login || true
else
    echo "❌ ERROR: Route cache file NOT created!"
fi
echo "✅ Application fully ready"
POSTSTART

chmod +x /tmp/post-start.sh

# Run post-start script in background
/tmp/post-start.sh &

echo "Starting web services..."
# Use the original start script from the base image
exec /start.sh
