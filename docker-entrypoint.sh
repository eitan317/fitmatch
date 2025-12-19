#!/bin/bash

# Don't exit on error - we want to continue even if some commands fail
# set -e  # REMOVED - causes script to exit if any command fails

# Fix MPM configuration at runtime (Railway may re-enable modules)
echo "=== Fixing MPM configuration ==="
# Remove all MPM symlinks first
rm -f /etc/apache2/mods-enabled/mpm_*.conf /etc/apache2/mods-enabled/mpm_*.load 2>/dev/null || true
# Disable all MPMs
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true
# Remove any LoadModule directives for MPMs from apache2.conf
sed -i '/LoadModule.*mpm_/d' /etc/apache2/apache2.conf || true
# Remove from conf-enabled as well
rm -f /etc/apache2/conf-enabled/*mpm*.conf 2>/dev/null || true
# Now enable only prefork (don't fail if already enabled)
a2enmod mpm_prefork 2>/dev/null || {
    echo "Warning: mpm_prefork may already be enabled, continuing..."
}
echo "=== MPM configuration fixed ==="

# Validate and set Apache port from Railway PORT env var
APACHE_PORT=${PORT:-8080}
if ! [[ "$APACHE_PORT" =~ ^[0-9]+$ ]]; then
    echo "Warning: Invalid PORT value '$PORT', using 8080"
    APACHE_PORT=8080
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

# Update Apache ports.conf to listen on dynamic port
echo "Configuring Apache to listen on port $APACHE_PORT..."
echo "Listen $APACHE_PORT" > /etc/apache2/ports.conf

# Update VirtualHost to use dynamic port
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
    sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:$APACHE_PORT>/g" /etc/apache2/sites-available/000-default.conf
    sed -i "s/:80/:$APACHE_PORT/g" /etc/apache2/sites-available/000-default.conf
    sed -i "s/<VirtualHost \\*:8080>/<VirtualHost *:$APACHE_PORT>/g" /etc/apache2/sites-available/000-default.conf
    sed -i "s/:8080/:$APACHE_PORT/g" /etc/apache2/sites-available/000-default.conf
    echo "Updated VirtualHost to use port $APACHE_PORT"
fi

# Enable site
echo "Enabling Apache site..."
a2ensite 000-default.conf || true

# Test Apache configuration (but don't fail if test fails)
echo "Testing Apache configuration..."
apache2ctl configtest || echo "WARNING: Apache configuration test failed, but continuing..."

# Start Apache in foreground (this should always run)
echo "=== Starting Apache server on port $APACHE_PORT ==="
exec apache2-foreground
