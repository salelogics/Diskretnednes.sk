#!/bin/bash
set -e

echo "PWD:"
pwd

echo "PHP:"
php84 -v

echo "Laravel env:"
php84 artisan env

echo "DB_CONNECTION:"
php84 artisan tinker --execute="dump(config('database.default'));"

echo "DB_DATABASE:"
php84 artisan tinker --execute="dump(config('database.connections.mysql.database'));"

cd /var/www13/p10542/diskretnednes.sk/sub/dev/public/current

echo "Current directory:"
pwd

php84 artisan migrate --force
php84 artisan cache:clear
php84 artisan config:cache
php84 artisan route:cache
php84 artisan view:cache

php84 artisan storage:link || true
