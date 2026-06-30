#!/usr/bin/env bash

set -e
set -o pipefail

alias php='/usr/lib64/php8.3/bin/php'

export PATH="/usr/lib64/php8.3/bin:$PATH"
cd /var/www11/p2832/prosimsi.sk/web/release_20250606122029

printf '#c#d#Spúšťam composer install\n'
/usr/lib64/php8.3/bin/php $(which composer) install
printf '#c#s#/usr/lib64/php8.3/bin/php composer install\n'
printf '#c#d#Spúšťam npm install \n'
printf '#c#d#Spúšťam npm prune\n'
sudo /etc/hosting/deploy/scripts/dockerRunBuild.sh $(pwd) 'npm install ; npm prune' '22.16.0'
printf '#c#d#Spúšťam cp -n .env.example .env\n'
cp -n .env.example .env
printf '#c#s#cp -n .env.example .env\n'
printf '#c#d#Spúšťam artisan migrate\n'
/usr/lib64/php8.3/bin/php artisan migrate --force
printf '#c#s#artisan migrate\n'
printf '#c#d#Spúšťam artisan cache:clear\n'
/usr/lib64/php8.3/bin/php artisan cache:clear
printf '#c#s#artisan cache:clear\n'
printf '#c#d#Spúšťam artisan config:cache\n'
/usr/lib64/php8.3/bin/php artisan config:cache
printf '#c#s#artisan config:cache\n'
printf '#c#d#Spúšťam artisan route:cache\n'
/usr/lib64/php8.3/bin/php artisan route:cache
printf '#c#s#artisan route:cache\n'
printf '#c#d#Spúšťam artisan view:clear\n'
/usr/lib64/php8.3/bin/php artisan view:clear
printf '#c#s#artisan view:clear\n'
printf '#c#d#Spúšťam artisan storage:link\n'
/usr/lib64/php8.3/bin/php artisan storage:link
printf '#c#s#artisan storage:link\n'