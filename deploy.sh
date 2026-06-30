#!/bin/bash

# Deployment script pre Erotikon.sk
echo "🚀 Spúšťam deployment..."

# 1. Skontroluj .env súbor (bez zmeny)
echo "📝 Kontrolujem .env súbor..."
if [ -f .env ]; then
    echo "✅ .env súbor existuje"
else
    echo "❌ .env súbor neexistuje!"
    exit 1
fi

# 2. Vytvor a vyčisti bootstrap cache adresár
echo "🧹 Pripravujem bootstrap cache..."
mkdir -p bootstrap/cache
rm -f bootstrap/cache/*.php
chmod 755 bootstrap/cache

# 3. Inštaluj composer dependencies a regeneruj autoload
echo "📦 Inštalujem composer dependencies..."
composer install --no-dev --optimize-autoloader

# 4. Regeneruj composer autoload (dôležité pre facade)
echo "⚡ Regenerujem composer autoload..."
composer dump-autoload --optimize --no-dev

# 5. Vyčisti všetky cache súbory
echo "🧹 Čistím cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 6. Vygeneruj nové cache súbory
echo "⚡ Generujem cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Spusti migrácie
echo "🗄️ Spúšťam migrácie..."
php artisan migrate --force

# 8. Nastav správne permissions
echo "🔒 Nastavujem permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework storage/app/public

# 9. Vytvor symlink pre storage (ak neexistuje)
echo "🔗 Vytváram storage symlink..."
php artisan storage:link

echo "✅ Deployment dokončený!"