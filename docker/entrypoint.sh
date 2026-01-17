#!/bin/sh

set -e

echo "🚀 Starting deployment..."
echo "Environment: APP_ENV=${APP_ENV:-not_set}"
echo "Database: DB_CONNECTION=${DB_CONNECTION:-not_set}"
echo "App Key: APP_KEY=${APP_KEY:+set}"

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "❌ ERROR: APP_KEY is not set!"
    echo "Please set APP_KEY in Railway environment variables"
    exit 1
fi

# Create database directory if it doesn't exist (for SQLite)
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    echo "Setting up SQLite database..."
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
fi

# Wait for database to be ready (for MySQL on Railway)
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL connection..."
    max_attempts=15
    attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if php artisan migrate:status --no-interaction 2>/dev/null; then
            echo "✅ Database connection successful"
            break
        fi
        
        echo "Database not ready, attempt $attempt/$max_attempts..."
        sleep 3
        attempt=$((attempt + 1))
    done
    
    if [ $attempt -gt $max_attempts ]; then
        echo "❌ Failed to connect to database after $max_attempts attempts"
        echo "Continuing with deployment..."
    fi
fi

# Run migrations (Safe for production with --force)
echo "Running migrations..."
php artisan migrate --force --no-interaction || echo "⚠️  Migration failed, continuing..."

# Run database seeders
echo "Running database seeders..."
php artisan db:seed --force --no-interaction || echo "⚠️  Seeding failed, continuing..."

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

echo "✅ Entrypoint script completed. Starting services..."

exec "$@"