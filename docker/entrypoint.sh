#!/bin/bash
set -e

echo "🚀 Starting MediDesk initialization..."

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" > /dev/null 2>&1; do
  echo "  MySQL not ready, retrying in 2 seconds..."
  sleep 2
done
echo "✅ MySQL is ready!"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
  echo "🔑 Generating APP_KEY..."
  php artisan key:generate --force
fi

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Seed database (optional - comment out if not needed)
# echo "🌱 Seeding database..."
# php artisan db:seed

# Create storage symlink if needed
if [ ! -L "public/storage" ]; then
  echo "🔗 Creating storage symlink..."
  php artisan storage:link || true
fi

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

echo "✨ MediDesk is ready!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Application URL: http://localhost:8080"
echo "⚡ Vite Dev Server: http://localhost:5173"
echo "📊 PhpMyAdmin: http://localhost:8081 (root/root)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Start PHP-FPM
exec php-fpm
