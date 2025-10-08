FROM php:8.2-cli

# Install system dependencies for PHP extensions and git for composer
RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files and install dependencies
COPY composer.json ./
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the application files
COPY . .

# Expose port and start server
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:${PORT:-8000}", "index.php"]