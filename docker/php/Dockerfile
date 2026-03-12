FROM php:8.2-apache

# Устанавливаем зависимости и расширения PHP
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql mbstring intl gd zip \
    && docker-php-ext-enable opcache

# Включаем модуль rewrite для "красивых URL"
RUN a2enmod rewrite

# Включаем gzip для статики
RUN a2enmod deflate

# Устанавливаем рабочую директорию
WORKDIR /var/www/html
# Меняем DocumentRoot на public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Права доступа
RUN chown -R www-data:www-data /var/www/html

COPY apache.conf /etc/apache2/conf-enabled/public.conf

# Копируем composer (опционально)
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer