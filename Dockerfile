# PHP 8.2 + Apache image
FROM php:8.2-apache

# Composer o'rnatish
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Loyihani container ichiga nusxalash
COPY . /var/www/html/

# Apache document root
WORKDIR /var/www/html/public

# Composer dependencies o'rnatish
RUN composer install --no-dev --optimize-autoloader

# Port ochish
EXPOSE 80

# Apache start
CMD ["apache2-foreground"]
