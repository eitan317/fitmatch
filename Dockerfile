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
# This prevents "More than one MPM loaded" error
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true && \
    a2dismod mpm_prefork 2>/dev/null || true && \
    a2enmod mpm_prefork && \
    a2enmod rewrite && \
    a2enmod headers

# Copy Apache config (will be updated at runtime with PORT)
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copy startup script
COPY docker/start-apache.sh /usr/local/bin/start-apache.sh
RUN chmod +x /usr/local/bin/start-apache.sh

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

# Expose port (Railway will set PORT env var dynamically)
EXPOSE 80

# Start Apache using the startup script
CMD ["/usr/local/bin/start-apache.sh"]
