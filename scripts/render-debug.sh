#!/usr/bin/env bash

echo "===== Render Debug Information ====="
echo ""

echo "1. PHP Version:"
php -v
echo ""

echo "2. Laravel Version:"
php artisan --version
echo ""

echo "3. Environment:"
echo "APP_ENV: ${APP_ENV:-not set}"
echo "APP_DEBUG: ${APP_DEBUG:-not set}"
echo "APP_KEY: ${APP_KEY:0:20}... (truncated)"
echo ""

echo "4. Database Connection:"
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "DB_DATABASE: ${DB_DATABASE:-not set}"
echo ""

echo "5. Directory Permissions:"
ls -la storage/ | head -n 10
echo ""

echo "6. Migration Status:"
php artisan migrate:status || echo "Migration check failed"
echo ""

echo "7. Route List (first 20):"
php artisan route:list | head -n 20 || echo "Route list failed"
echo ""

echo "8. Config Cache Status:"
if [ -f bootstrap/cache/config.php ]; then
    echo "✅ Config is cached"
else
    echo "❌ Config is not cached"
fi
echo ""

echo "9. Route Cache Status:"
if [ -f bootstrap/cache/routes-v7.php ]; then
    echo "✅ Routes are cached"
else
    echo "❌ Routes are not cached"
fi
echo ""

echo "10. Storage Link Status:"
if [ -L public/storage ]; then
    echo "✅ Storage link exists"
    ls -l public/storage
else
    echo "❌ Storage link does not exist"
fi
echo ""

echo "===== Debug Complete ====="

