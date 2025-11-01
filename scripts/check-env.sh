#!/usr/bin/env bash

# 環境変数チェックスクリプト
# Renderデプロイ前に必要な環境変数が設定されているか確認します

echo "===== Checking required environment variables ====="

ERRORS=0

check_var() {
    if [ -z "${!1}" ]; then
        echo "❌ ERROR: $1 is not set"
        ERRORS=$((ERRORS + 1))
    else
        echo "✅ $1 is set"
    fi
}

# 必須の環境変数
check_var "APP_KEY"
check_var "APP_ENV"
check_var "DB_CONNECTION"
check_var "DB_HOST"
check_var "DB_DATABASE"
check_var "DB_USERNAME"
check_var "DB_PASSWORD"

# APP_KEYの形式チェック
if [ ! -z "$APP_KEY" ]; then
    if [[ ! "$APP_KEY" =~ ^base64: ]]; then
        echo "⚠️  WARNING: APP_KEY should start with 'base64:'"
        echo "   Run: php artisan key:generate --show"
    fi
fi

# デバッグモードの警告（本番環境）
if [ "$APP_ENV" = "production" ] && [ "$APP_DEBUG" = "true" ]; then
    echo "⚠️  WARNING: APP_DEBUG is true in production environment"
    echo "   This should be set to false for security reasons"
fi

echo ""
if [ $ERRORS -eq 0 ]; then
    echo "✅ All required environment variables are set!"
    exit 0
else
    echo "❌ Found $ERRORS missing environment variable(s)"
    echo "Please set them in Render dashboard or your .env file"
    exit 1
fi

