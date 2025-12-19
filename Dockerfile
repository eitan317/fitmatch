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
    && docker-php-ext-enable pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Apache config first (needed before other files)
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
RUN npm install && npm run build

# Generate application key (will be overridden by env var if set)
RUN php artisan key:generate --force || true

# Configure Apache
RUN a2enmod rewrite

# Expose port (Render will set PORT env var)
EXPOSE 80

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Update Apache port from environment variable\n\
# Ensure PORT is a number, default to 80 if not set or invalid\n\
PORT=${PORT:-80}\n\
PORT=$(echo "$PORT" | grep -E "^[0-9]+$" || echo "80")\n\
export PORT\n\
\n\
# Update ports.conf\n\
if [ -f /etc/apache2/ports.conf ]; then\n\
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf || echo "Listen $PORT" > /etc/apache2/ports.conf\n\
else\n\
    echo "Listen $PORT" > /etc/apache2/ports.conf\n\
fi\n\
\n\
# Update VirtualHost in apache config\n\
if [ -f /etc/apache2/sites-available/000-default.conf ]; then\n\
    sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf\n\
    sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
fi\n\
\n\
# Enable site\n\
a2ensite 000-default.conf || true\n\
\n\
# Unset PORT for Laravel commands to avoid conflicts\n\
unset PORT\n\
\n\
# Clear caches before starting\n\
php artisan config:clear || true\n\
php artisan cache:clear || true\n\
php artisan view:clear || true\n\
\n\
# Run migrations (don'\''t fail if DB not ready)\n\
php artisan migrate --force || echo "Migrations failed, continuing..."\n\
\n\
# Restore PORT for Apache\n\
export PORT=${PORT:-80}\n\
\n\
# Start Apache in foreground\n\
exec apache2-foreground\n\
' > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

# Start Apache with migrations
CMD ["/usr/local/bin/start.sh"]

