# richarvey/nginx-php-fpmをベースとする
FROM richarvey/nginx-php-fpm:latest


RUN apk upgrade --update

# ベースイメージ(Alpine Linux)を更新し、
# Node.js と npm をインストールします (LTS版を指定)
RUN apk add --update nodejs-lts npm

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

# 作業ディレクトリを設定
WORKDIR /var/www/html

# ----------------------------------------------------
# ★★★ ビルド手順を最適化 ★★★
# ----------------------------------------------------

# 1. PHPの依存関係ファイルを先にコピー
COPY composer.json composer.lock ./

# 2. PHPの依存関係をインストール
RUN composer install --no-dev --no-scripts

# 3. Node.jsの依存関係ファイルのみをコピー (package-lock.json はコピーしない)
COPY package.json ./

# 4. Node.jsの依存関係をクリーンインストール
RUN npm install

# 5. アプリケーションの全ファイルをコピー
COPY . .

# 6. フロントエンドのアセットをビルド
RUN npm run build

# 7. Laravelの最適化コマンドは /scripts/00-laravel-deploy.sh で実行するため、ここでは実行しない
# RUN php artisan config:cache
# RUN php artisan route:cache
# RUN php artisan view:cache

# ----------------------------------------------------
# ★★★ ビルド手順ここまで ★★★
# ----------------------------------------------------

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
