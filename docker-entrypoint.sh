#!/bin/sh
set -e

echo "========================================="
echo " Laravel Backend Starting..."
echo "========================================="

git config --global --add safe.directory /var/www/html

echo "Installing/repairing Composer dependencies..."
composer install --prefer-dist --no-interaction

# Create .env if missing
if [ ! -f .env ]; then
    echo "Creating .env from example..."
    cp .env.example .env
fi

# Generate key if missing
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Ensure writable directories
mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chmod -R 775 storage bootstrap/cache || true

# Clear cached config/routes/views in development
php artisan optimize:clear

echo "Waiting for PostgreSQL..."
sleep 5

# Run migrations
php artisan migrate --force || true

echo "Laravel is ready."

exec "$@"