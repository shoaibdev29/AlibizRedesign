FROM php:8.2-apache

# System deps + PHP extensions (incl. ZIP)
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libonig-dev libxml2-dev \
    libzip-dev zlib1g-dev \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Apache + workdir
RUN a2enmod rewrite
WORKDIR /var/www/html

# Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Code
COPY . .

# Composer install (prod)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Permissions (Laravel write)
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Proper vhost (heredoc — variables not expanded)
RUN cat >/etc/apache2/sites-available/000-default.conf <<'EOF'
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

EXPOSE 80
CMD ["apache2-foreground"]
