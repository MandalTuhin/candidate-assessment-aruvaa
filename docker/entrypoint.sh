#!/bin/sh

set -e

echo "🚀 Starting deployment..."
echo "Environment: APP_ENV=${APP_ENV:-not_set}"
echo "Database: DB_CONNECTION=${DB_CONNECTION:-not_set}"
echo "App Key: APP_KEY=${APP_KEY:+set}"
echo "App URL: APP_URL=${APP_URL:-not_set}"

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "❌ ERROR: APP_KEY is not set!"
    echo "Please set APP_KEY in Railway environment variables"
    exit 1
fi

# Create database directory and file for SQLite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    echo "Setting up SQLite database..."
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
    
    # Set the database path for Laravel
    export DB_DATABASE="/var/www/html/database/database.sqlite"
    echo "SQLite database path: $DB_DATABASE"
fi

# For MySQL, try to connect but don't fail if it doesn't work
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Testing MySQL connection..."
    if php artisan migrate:status --no-interaction 2>/dev/null; then
        echo "✅ Database connection successful"
        
        # Run migrations
        echo "Running migrations..."
        php artisan migrate --force --no-interaction || echo "⚠️  Migration failed"
        
        # Run seeders
        echo "Running database seeders..."
        php artisan db:seed --force --no-interaction || echo "⚠️  Seeding failed"
    else
        echo "⚠️  Database connection failed, skipping migrations and seeding"
    fi
else
    # For SQLite, always run migrations and seeders
    echo "Running migrations..."
    php artisan migrate --force --no-interaction || echo "⚠️  Migration failed"
    
    echo "Running database seeders..."
    php artisan db:seed --force --no-interaction || echo "⚠️  Seeding failed"
fi

# Optimize Laravel (but don't fail if it doesn't work)
echo "Caching configuration..."
php artisan config:cache || echo "⚠️  Config cache failed"
php artisan route:cache || echo "⚠️  Route cache failed"
php artisan view:cache || echo "⚠️  View cache failed"

# Ensure storage is writable
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || echo "⚠️  Permission fix failed"

# Create nginx run directory
mkdir -p /var/run/nginx
chown -R www-data:www-data /var/run/nginx || echo "⚠️  Nginx directory setup failed"

# Test basic Laravel functionality
echo "Testing Laravel..."
php artisan --version || echo "⚠️  Laravel test failed"

echo "✅ Entrypoint script completed successfully!"
echo "Starting supervisord with nginx and php-fpm..."

exec "$@"