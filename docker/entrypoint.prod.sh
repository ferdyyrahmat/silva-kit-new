#!/bin/sh
set -e

# Wait for DB
if [ -n "$DB_HOST" ]; then
    echo "Waiting for production database connection ($DB_HOST)..."
    until php -r "try { new PDO('mysql:host='.$_ENV['DB_HOST'].';port='.$_ENV['DB_PORT'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']); echo 'Connected'; } catch (Exception \$e) { exit(1); }"; do
        sleep 2
    done
    echo "Production Database connected successfully!"
fi

# Run Production Optimization Commands
echo "Optimizing Laravel for Production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run database migrations safely
echo "Running Production Database Migrations..."
php artisan migrate --force

# Create storage link if missing
if [ ! -d "/var/www/html/public/storage" ]; then
    php artisan storage:link || true
fi

exec "$@"
