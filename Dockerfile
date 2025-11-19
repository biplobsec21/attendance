FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev libzip-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo pdo_mysql mbstring tokenizer ctype xml pcntl bcmath zip

# GD (most Laravel projects need it)
RUN docker-php-ext-install gd

WORKDIR /var/www/html
