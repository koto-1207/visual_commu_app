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
php artisan livewire:clear || echo "Livewire clear not available"

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

echo "NOTE: Skipping route:cache because it may conflict with Volt dynamic components"
echo "NOTE: Skipping view:cache because it conflicts with Volt dynamic components"

echo "Testing if routes can be loaded without cache..."
php artisan route:list --path=login 2>&1 | head -n 20 || echo "Could not load login route"

echo "Verifying Volt can find views..."
test -d /var/www/html/resources/views/livewire/pages/auth && echo "✅ Auth views directory exists" || echo "❌ Auth views directory missing"
echo "Checking specific view files..."
test -f /var/www/html/resources/views/livewire/pages/auth/login.blade.php && echo "✅ login.blade.php exists" || echo "❌ login.blade.php missing"
test -f /var/www/html/resources/views/livewire/pages/auth/register.blade.php && echo "✅ register.blade.php exists" || echo "❌ register.blade.php missing"
echo "Listing auth directory contents..."
ls -la /var/www/html/resources/views/livewire/pages/auth/ || echo "Cannot list directory"

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
else
    echo "❌ WARNING: Route cache file NOT created!"
fi

echo "Listing all routes..."
php artisan route:list || echo "Could not list routes"

echo "Testing login route with artisan..."
php artisan route:list --name=login --json || true

echo "Verifying auth.php is accessible..."
test -f /var/www/html/routes/auth.php && echo "✅ auth.php exists" || echo "❌ auth.php missing"
echo "Checking routes directory..."
ls -la /var/www/html/routes/ || echo "Cannot list routes directory"

echo "Verifying Volt mount path..."
php -r "echo 'Resolved path: ' . realpath(__DIR__ . '/../app/Providers/../../resources/views/livewire') . PHP_EOL;" || true
test -d /var/www/html/resources/views/livewire && echo "✅ Livewire directory exists" || echo "❌ Livewire directory missing"

echo "Testing if Volt can resolve pages.auth.login component..."
php artisan tinker --execute="echo Livewire\Volt\Volt::class;" 2>&1 || echo "Volt class not available"

echo "Checking Laravel storage/logs for errors..."
if [ -f /var/www/html/storage/logs/laravel.log ]; then
    echo "Recent Laravel errors:"
    tail -n 20 /var/www/html/storage/logs/laravel.log || true
else
    echo "No laravel.log file found yet"
fi

echo "✅ Application fully ready"

echo "Starting web services..."
# Use the original start script from the base image
exec /start.sh
