#!/bin/bash
set -e

echo "Starting deployment..."

php84 artisan optimize:clear

if [ -f composer.json ]; then
    php84 $(which composer) install --no-dev --optimize-autoloader
fi

php84 artisan migrate --force

echo "Deployment finished successfully."
