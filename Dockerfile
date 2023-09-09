FROM php:7.4.33-apache

# Install the MySQL extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Set the working directory to /var/www/html/kdms
WORKDIR /var/www/html/kdms

# Start the Apache web server
CMD ["apache2-foreground"]
