#!/bin/sh

set -e

echo "🚀 Starting deployment..."

# Create database directory if it doesn't exist (for SQLite)
echo "Setting up database..."
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database

# Wait for database to be ready (for MySQL on Railway)
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL connection..."
    until php artisan migrate:status --no-interaction 2>/dev/null; do
        echo "Database not ready, waiting..."
        sleep 2
    done
fi

# Run migrations (Safe for production with --force)
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Optimize Laravel
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure storage is writable
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Create nginx run directory
mkdir -p /var/run/nginx
chown -R www-data:www-data /var/run/nginx

echo "✅ Entrypoint script completed. Handing off to Supervisor."

exec "$@"