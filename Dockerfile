# richarvey/nginx-php-fpmをベースとする
FROM richarvey/nginx-php-fpm:latest

# --- ★ここから追加★ ---
# ベースイメージ(Alpine Linux)を更新し、
# Node.js と npm をインストールします
RUN apk add --update nodejs npm
# --- ★ここまで追加★ ---

COPY . .

# --- ★SKIP_COMPOSER 1 を削除しました★ ---
# ENV SKIP_COMPOSER 1

# Image config
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# --- ★ここから追加★ ---
# 作業ディレクトリを設定
WORKDIR /var/www/html

# PHPの依存関係をインストール
RUN composer install --no-dev --no-scripts

# フロントエンドの依存関係をインストールし、ビルドを実行
RUN npm install
RUN npm run build

# Laravelの最適化コマンドを実行
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
# --- ★ここまで追加★ ---


# Set proper permissions for Laravel storage and bootstrap cache
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/app/public \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R nginx:nginx /var/www/html/storage \
    && chown -R nginx:nginx /var/www/html/bootstrap/cache

CMD ["/start.sh"]
