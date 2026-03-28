FROM php:8.2-apache

# Install MySQL extension
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite (optional but good)
RUN a2enmod rewrite

# Copy all your project files
COPY . /var/www/html/
