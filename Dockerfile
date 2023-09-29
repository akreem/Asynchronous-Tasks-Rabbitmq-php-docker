# Use an official PHP runtime as a parent image
FROM php:7.4-fpm

# Install system dependencies and PHP extensions as needed
RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql bcmath

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory to /var/www/html/myproject
WORKDIR /var/www/html/myproject

# Run composer require command to install php-amqplib/php-amqplib
RUN composer require php-amqplib/php-amqplib

