#!/bin/bash
set -e

echo "=== Starting Apache with Laravel ==="

# Validate and set Apache port from Railway PORT env var
APACHE_PORT=${PORT:-80}
if ! [[ "$APACHE_PORT" =~ ^[0-9]+$ ]]; then
    echo "Warning: Invalid PORT value '$PORT', using 80"
    APACHE_PORT=80
fi
echo "Apache will listen on port: $APACHE_PORT"

# Unset PORT for Laravel commands (prevents ServeCommand errors)
unset PORT

# Ensure APP_KEY exists (required for Laravel)
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY not set, generating temporary key..."
    php artisan key:generate --force || true
fi

# Clear Laravel caches
echo "Clearing Laravel caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Run migrations (don't fail if DB not ready)
echo "Running migrations..."
php artisan migrate --force || echo "Migrations failed, continuing..."

# Ensure only mpm_prefork is enabled (fix MPM conflict)
echo "Verifying Apache MPM configuration..."
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Update Apache ports.conf to listen on dynamic port
echo "Configuring Apache to listen on port $APACHE_PORT..."
echo "Listen $APACHE_PORT" > /etc/apache2/ports.conf

# Update VirtualHost to use dynamic port
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
    sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:$APACHE_PORT>/g" /etc/apache2/sites-available/000-default.conf
    sed -i "s/:80/:$APACHE_PORT/g" /etc/apache2/sites-available/000-default.conf
    echo "Updated VirtualHost to use port $APACHE_PORT"
fi

# Verify only one MPM is loaded (safety check)
MPM_COUNT=$(apache2ctl -M 2>/dev/null | grep -c "^ mpm_" || echo "0")
if [ "$MPM_COUNT" -gt 1 ]; then
    echo "ERROR: Multiple MPMs detected! Fixing..."
    a2dismod mpm_event mpm_worker 2>/dev/null || true
    a2enmod mpm_prefork || true
    apache2ctl -M | grep "^ mpm_"
fi

# Enable site
echo "Enabling Apache site..."
a2ensite 000-default.conf || true

# Test Apache configuration
echo "Testing Apache configuration..."
if apache2ctl configtest; then
    echo "Apache configuration is valid"
else
    echo "WARNING: Apache configuration test failed, but continuing..."
fi

# Start Apache in foreground
echo "=== Starting Apache server ==="
exec apache2-foreground

