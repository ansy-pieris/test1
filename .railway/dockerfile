# Base PHP + Apache
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    libxml2-dev libzip-dev curl gnupg nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install MongoDB PHP driver
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy  the app
COPY . .

# Install PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p storage/app/public \
    && chown -R www-data:www-data storage \
    && chmod -R 755 storage



# Create storage symlink
RUN php artisan storage:link

# Copy default images into storage/app/public
COPY storage/app/public/ storage/app/public/

# inside your container
RUN ls -l public/storage
RUN ls -l storage/app/public

# Copy package.json for caching and install node deps
RUN npm ci && npm run build

# Set Apache to serve Laravel's public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf


# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Apache port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
