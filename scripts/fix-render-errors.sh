#!/usr/bin/env bash

echo "===== Fixing Common Render Errors ====="
echo ""

# エラーハンドリングを有効化
set -e

echo "Step 1: Clearing all caches..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
echo "✅ Caches cleared"
echo ""

echo "Step 2: Running migrations..."
php artisan migrate --force --isolated
echo "✅ Migrations completed"
echo ""

echo "Step 3: Checking migration status..."
php artisan migrate:status
echo ""

echo "Step 4: Creating storage link..."
php artisan storage:link || true
echo "✅ Storage link created"
echo ""

echo "Step 5: Re-caching configuration..."
php artisan config:cache
echo "✅ Config cached"
echo ""

echo "Step 6: Re-caching routes..."
php artisan route:cache
echo "✅ Routes cached"
echo ""

echo "Step 7: Optimizing application..."
php artisan optimize
echo "✅ Application optimized"
echo ""

echo "Step 8: Publishing Livewire assets..."
php artisan livewire:publish --assets || true
echo "✅ Livewire assets published"
echo ""

echo "Step 9: Checking critical routes..."
php artisan route:list --name=profile --compact || echo "⚠️  Profile route not found"
php artisan route:list --name=places --compact || echo "⚠️  Places routes not found"
echo ""

echo "===== Fix Complete! ====="
echo ""
echo "Next steps:"
echo "1. Check if the application is accessible"
echo "2. If errors persist, check Render logs"
echo "3. Run: bash scripts/render-debug.sh for detailed info"

