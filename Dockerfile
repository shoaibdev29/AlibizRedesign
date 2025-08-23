FROM php:8.2-apache

# System deps + PHP extensions (incl. ZIP)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zlib1g-dev \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Apache mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copy code
COPY . .

# Install PHP deps (prod)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Serve /public
RUN echo '<VirtualHost *:80> \
    DocumentRoot /var/www/html/public \
    <Directory /var/www/html/public> \
        Options Indexes FollowSymLinks \
        AllowOverride All \
        Require all granted \
    </Directory> \
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]
