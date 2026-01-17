#!/bin/sh

set -e

echo "🚀 Starting deployment..."

# Run migrations (Safe for production with --force)
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Optimize Laravel
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure storage is writable (Crucial for Resume Uploads)
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Entrypoint script completed. Handing off to Supervisor."

exec "$@"