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
# CRITICAL: Remove ALL MPM symlinks manually, then enable ONLY prefork
# This prevents "More than one MPM loaded" error
RUN echo "=== Configuring Apache MPM ===" && \
    # Remove all MPM symlinks (both .conf and .load files)
    rm -f /etc/apache2/mods-enabled/mpm_*.conf /etc/apache2/mods-enabled/mpm_*.load 2>/dev/null || true && \
    # Disable all MPMs using a2dismod (in case symlinks exist)
    a2dismod mpm_event mpm_worker 2>/dev/null || true && \
    # Enable ONLY prefork
    a2enmod mpm_prefork && \
    # Enable other required modules
    a2enmod rewrite && \
    a2enmod headers && \
    # Verify only one MPM is enabled
    echo "=== Verifying MPM configuration ===" && \
    MPM_COUNT=$(ls -1 /etc/apache2/mods-enabled/ 2>/dev/null | grep -c "^mpm_" || echo "0") && \
    if [ "$MPM_COUNT" -ne 1 ]; then \
        echo "ERROR: Expected 1 MPM, found $MPM_COUNT. Fixing..."; \
        rm -f /etc/apache2/mods-enabled/mpm_*.conf /etc/apache2/mods-enabled/mpm_*.load 2>/dev/null || true; \
        a2enmod mpm_prefork; \
    fi && \
    echo "MPM modules enabled:" && \
    ls -la /etc/apache2/mods-enabled/ | grep "mpm_" && \
    echo "=== MPM configuration complete ==="

# Remove all MPM LoadModule directives from apache2.conf
RUN sed -i '/LoadModule.*mpm_/d' /etc/apache2/apache2.conf || true && \
    # Verify removal
    echo "=== Checking apache2.conf for MPM directives ===" && \
    grep -i "LoadModule.*mpm" /etc/apache2/apache2.conf || echo "No MPM LoadModule directives found (good)"

# Configure Apache DocumentRoot to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Copy Apache config first (before updating DocumentRoot)
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

# Update Apache DocumentRoot in config files
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy and setup entrypoint script (fixes MPM at runtime - Railway re-enables modules)
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 8080 for Railway
EXPOSE 8080

# Start Apache via entrypoint
CMD ["/usr/local/bin/docker-entrypoint.sh"]
