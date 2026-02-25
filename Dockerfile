FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    git \
    curl \
    zip \
    unzip \
    postgresql-client \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        mbstring \
        xml \
        bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Set environment
ENV PHP_MEMORY_LIMIT=512M

# Install PHP dependencies (production only)
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Install Node dependencies and build frontend
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build \
    && apt-get purge -y nodejs npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Create storage directories
RUN mkdir -p \
    storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Clear config cache (will be regenerated)
RUN php artisan config:clear

# Expose port
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Run migrations and start server
CMD ["sh", "-c", "php artisan migrate --force && php artisan cache:clear && php artisan serve --host=0.0.0.0 --port=8000"]
