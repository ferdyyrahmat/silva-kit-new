#!/bin/sh
set -e

# Wait for DB if DB_HOST is set
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection ($DB_HOST)..."
    until php -r "try { new PDO('mysql:host='.$_ENV['DB_HOST'].';port='.$_ENV['DB_PORT'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']); echo 'Connected'; } catch (Exception \$e) { exit(1); }"; do
        sleep 2
    done
    echo "Database connected successfully!"
fi

# Ensure storage & bootstrap permissions
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate key if empty
if [ -z "$APP_KEY" ]; then
    echo "Generating Application Key..."
    php artisan key:generate
fi

# Create storage link if not exists
if [ ! -d "/var/www/html/public/storage" ]; then
    php artisan storage:link || true
fi

# Run database migration & seeders in dev mode
echo "Running Database Migrations..."
php artisan migrate --force

# Execute main CMD (php-fpm)
exec "$@"
