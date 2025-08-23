FROM php:8.2-apache

# System deps + PHP extensions (incl. ZIP)
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libonig-dev libxml2-dev \
    libzip-dev zlib1g-dev \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Apache + working dir
RUN a2enmod rewrite
WORKDIR /var/www/html

# Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copy code
COPY . .

# Composer install (prod)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Permissions for storage/bootstrap (avoid write errors)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Proper Apache vhost via heredoc
RUN bash -lc 'cat > /etc/apache2/sites-available/000-default.conf << "EOF"\n\
<VirtualHost *:80>\n\
    ServerName localhost\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n\
EOF'

EXPOSE 80
CMD ["apache2-foreground"]
