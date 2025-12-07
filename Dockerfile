FROM php:8.3-fpm

# ===============================
# Системные зависимости
# ===============================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    net-tools \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libxpm-dev

# Установка gRPC system tools
RUN apt-get update && apt-get install -y \
    protobuf-compiler \
    protobuf-compiler-grpc

# ===============================
# PHP extensions
# ===============================

# PHP gRPC
RUN pecl install grpc \
    && echo "extension=grpc.so" > /usr/local/etc/php/conf.d/grpc.ini

# Xdebug
RUN pecl install xdebug

# GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Очистка кеша apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# ===============================
# Настройки PHP
# ===============================
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

RUN composer install --no-dev --optimize-autoloader

EXPOSE 9010
CMD ["php-fpm", "-F"]
