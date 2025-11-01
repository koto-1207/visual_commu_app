# richarvey/nginx-php-fpmをベースとする
FROM richarvey/nginx-php-fpm:latest

# 1. Alpine Linuxのパッケージをすべて最新にアップグレード
# (php-pgsqlドライバーを更新し、SCRAM認証をサポートするため)
RUN apk upgrade --update

# 2. Node.js LTS (v20+) と npm をインストール
RUN apk add --update nodejs-lts npm

# Image config
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# 作業ディレクトリを設定
WORKDIR /var/www/html

# 3. アプリケーションの全ファイルをコピー
# (.dockerignoreにより、node_modulesとpackage-lock.jsonは除外される)
COPY . .

# 4. PHPの依存関係をインストール
RUN composer install --no-dev --no-scripts

# 5. Node.jsの依存関係をクリーンインストール
# (Alpine Linux用の正しいpackage-lock.jsonがここで新規作成されます)
RUN npm install

# 6. フロントエンドのアセットをビルド
RUN npm run build

# Set proper permissions for Laravel storage and bootstrap cache
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/app/public \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R nginx:nginx /var/www/html/storage \
    && chown -R nginx:nginx /var/www/html/bootstrap/cache

# Make startup script executable
RUN chmod +x /var/www/html/scripts/startup.sh

# Use custom startup script that runs Laravel initialization before starting services
CMD ["/var/www/html/scripts/startup.sh"]
