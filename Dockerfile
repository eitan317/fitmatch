# Use PHP 8.3 with Apache
FROM php:8.3-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && docker-php-ext-enable pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure Apache MPM FIRST (before any other Apache config)
# Disable all MPMs first, then enable only prefork (required for mod_php)
RUN a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true && \
    a2enmod mpm_prefork && \
    a2enmod rewrite && \
    a2enmod headers

# Copy Apache config (will be updated at runtime with PORT)
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copy application files
COPY . /var/www/html

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache && \
    mkdir -p /var/www/html/storage/framework/{sessions,views,cache} && \
    mkdir -p /var/www/html/storage/logs && \
    chown -R www-data:www-data /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

# Install Node dependencies and build assets
RUN npm install && npm run build || true

# Expose port (Railway will set PORT env var)
EXPOSE 80

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Validate and set Apache port from Railway PORT env var\n\
APACHE_PORT=${PORT:-80}\n\
if ! [[ "$APACHE_PORT" =~ ^[0-9]+$ ]]; then\n\
    echo "Warning: Invalid PORT value, using 80"\n\
    APACHE_PORT=80\n\
fi\n\
export APACHE_PORT\n\
\n\
# Unset PORT for Laravel commands (prevents ServeCommand errors)\n\
unset PORT\n\
\n\
# Ensure APP_KEY exists (required for Laravel)\n\
if [ -z "$APP_KEY" ]; then\n\
    echo "Warning: APP_KEY not set, generating temporary key..."\n\
    php artisan key:generate --force || true\n\
fi\n\
\n\
# Clear Laravel caches\n\
php artisan config:clear || true\n\
php artisan cache:clear || true\n\
php artisan view:clear || true\n\
php artisan route:clear || true\n\
\n\
# Run migrations (don'\''t fail if DB not ready)\n\
php artisan migrate --force || echo "Migrations failed, continuing..."\n\
\n\
# Update Apache ports.conf to listen on dynamic port\n\
echo "Listen $APACHE_PORT" > /etc/apache2/ports.conf\n\
\n\
# Update VirtualHost to use dynamic port\n\
sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:$APACHE_PORT>/g" /etc/apache2/sites-available/000-default.conf\n\
sed -i "s/:80/:$APACHE_PORT/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Verify only one MPM is loaded (safety check)\n\
MPM_COUNT=$(apache2ctl -M 2>/dev/null | grep -c "^ mpm_" || echo "0")\n\
if [ "$MPM_COUNT" -gt 1 ]; then\n\
    echo "Warning: Multiple MPMs detected, fixing..."\n\
    a2dismod mpm_event mpm_worker 2>/dev/null || true\n\
    a2enmod mpm_prefork || true\n\
fi\n\
\n\
# Enable site\n\
a2ensite 000-default.conf || true\n\
\n\
# Test Apache configuration\n\
apache2ctl configtest || echo "Apache config test failed, but continuing..."\n\
\n\
# Start Apache in foreground\n\
exec apache2-foreground\n\
' > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

# Start Apache with migrations
CMD ["/usr/local/bin/start.sh"]
