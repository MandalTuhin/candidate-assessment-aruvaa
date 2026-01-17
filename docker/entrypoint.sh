#!/bin/sh

set -e

echo "🚀 Starting deployment..."

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
    max_attempts=30
    attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if php artisan migrate:status --no-interaction 2>/dev/null; then
            echo "✅ Database connection successful"
            break
        fi
        
        echo "Database not ready, attempt $attempt/$max_attempts..."
        sleep 2
        attempt=$((attempt + 1))
    done
    
    if [ $attempt -gt $max_attempts ]; then
        echo "❌ Failed to connect to database after $max_attempts attempts"
        echo "Continuing anyway - migrations will be attempted..."
    fi
fi

# Run migrations (Safe for production with --force)
echo "Running migrations..."
if ! php artisan migrate --force --no-interaction; then
    echo "⚠️  Migration failed, but continuing..."
fi

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

# Test if Laravel can boot
echo "Testing Laravel application..."
if ! php artisan --version >/dev/null 2>&1; then
    echo "❌ Laravel application failed to boot!"
    exit 1
fi

echo "✅ Entrypoint script completed. Handing off to Supervisor."

exec "$@"