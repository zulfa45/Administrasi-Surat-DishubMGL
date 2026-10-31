#!/bin/bash
set -e

# Adapt Apache port to Render's dynamic $PORT environment variable if provided
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/:80>/:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

# Ensure storage directories exist with proper permissions
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Remove Vite hot-reloading flag so production build is used
rm -f public/hot

# Run Laravel optimizations
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache web server..."
exec apache2-foreground
