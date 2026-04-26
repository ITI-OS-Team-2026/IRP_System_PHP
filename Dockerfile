# Use official PHP 8.2 CLI image
FROM php:8.2-cli

# Install system dependencies (including freetype/gd for mPDF)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for layer caching
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction 2>&1 || echo "COMPOSER INSTALL WARNING (will retry after full copy)"

# Copy the rest of the application
COPY . .

# Run composer install again to ensure vendor is fully populated
# (COPY . . may have overwritten vendor if it existed in source)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ensure storage/logs is writable
RUN mkdir -p storage/logs storage/receipts storage/certificates storage/submissions storage/tmp public/uploads/ids \
    && chmod -R 775 storage public/uploads \
    && chown -R www-data:www-data storage public/uploads

# Railway provides PORT env var; default to 8080 for local testing
ENV PORT=8080

# Expose the port
EXPOSE 8080

# Start the built-in PHP server, listening on 0.0.0.0:$PORT
# Serve from project root because index.php forwards to public/index.php
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t /app"]
