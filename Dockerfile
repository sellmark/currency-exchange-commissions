# Use the official PHP CLI image
FROM php:8.3-cli

# Install necessary dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    && docker-php-ext-install intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a non-root user and group
RUN groupadd -g 1000 appuser && useradd -m -u 1000 -g appuser -s /bin/bash appuser

# Set the working directory
WORKDIR /app

# Copy application files and change ownership to the non-root user
COPY . .

# Ensure correct permissions for bin/console
RUN chown -R appuser:appuser /app && chmod +x /app/bin/console

# Switch to the non-root user
USER appuser

# Install Composer dependencies
RUN composer install --no-interaction --optimize-autoloader

# Set the command to run the Symfony console
CMD ["php", "bin/console"]
