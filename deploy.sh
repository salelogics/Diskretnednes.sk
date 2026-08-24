#!/bin/bash
set -e
echo "🚀 Spúšťam deployment..."

php84 /usr/local/bin/composer install --no-dev --optimize-autoloader
php84 /usr/local/bin/composer dump-autoload --optimize --no-dev

php84 artisan config:clear
php84 artisan migrate --force

php84 artisan cache:clear
php84 artisan config:cache
php84 artisan route:cache
php84 artisan view:cache

php84 artisan storage:link || true

echo "Deployment completed successfully."
