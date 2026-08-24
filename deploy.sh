#!/bin/bash
set -Eeuo pipefail

PHP="/usr/lib64/php8.4/bin/php"

echo "🚀 Spúšťam deployment..."
echo "PHP: $($PHP -v | head -n 1)"
echo "Working directory: $(pwd)"

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
$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader

# 4. Regeneruj composer autoload (dôležité pre facade)
echo "⚡ Regenerujem composer autoload..."
$PHP /usr/local/bin/composer dump-autoload --optimize --no-dev

# 5. Vyčisti všetky cache súbory
echo "🧹 Čistím cache..."
$PHP artisan config:clear
$PHP artisan cache:clear
$PHP artisan route:clear
$PHP artisan view:clear

# 6. Vygeneruj nové cache súbory
echo "⚡ Generujem cache..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

# 7. Spusti migrácie
echo "🗄️ Spúšťam migrácie..."
$PHP artisan migrate --force

# 8. Nastav správne permissions
echo "🔒 Nastavujem permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework storage/app/public

# 9. Vytvor symlink pre storage (ak neexistuje)
echo "🔗 Vytváram storage symlink..."
$PHP artisan storage:link

echo "✅ Deployment dokončený!"
