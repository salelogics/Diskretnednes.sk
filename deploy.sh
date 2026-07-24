#!/bin/bash
set -e

cd /var/www13/p10542/diskretnednes.sk/sub/dev/public/current

echo "Current directory:"
pwd

php84 artisan config:clear
php84 artisan migrate --force
php84 artisan cache:clear
php84 artisan config:cache
php84 artisan route:cache
php84 artisan view:cache

php84 artisan storage:link || true
