#!/bin/bash
set -e

echo "Starting deployment..."

php84 artisan migrate --force
php84 artisan cache:clear
php84 artisan config:cache
php84 artisan route:cache
php84 artisan view:cache
php84 artisan storage:link || true

echo "Deployment finished successfully."
