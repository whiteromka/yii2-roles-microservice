FROM php:8.3-fpm

# Установка системных зависимостей
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

# Очистка кеша
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений с поддержкой графики
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Установка Xdebug
RUN pecl install xdebug

# Копируем конфигурацию PHP, Xdebug и PHP-FPM
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание рабочей директории
WORKDIR /var/www

# Копирование файлов проекта
COPY . .

# Установка прав
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

# Установка зависимостей Yii2
RUN composer install --no-dev --optimize-autoloader

EXPOSE 9010
CMD ["php-fpm", "-F"]