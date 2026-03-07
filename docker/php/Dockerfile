FROM php:8.2-fpm

# Устанавливаем зависимости для mbstring и других расширений
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring intl gd zip

WORKDIR /var/www/html
