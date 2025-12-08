FROM php:8.4-fpm

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \
    curl \
    libldap2-dev \
    unzip \
    git \
    gnupg2 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        gd \
        bcmath \
        exif \
        mysqli \
        opcache \
        pdo \
        pdo_mysql \
        intl \
        soap \
        sockets \
        zip \
    && pecl install memcache \
    && docker-php-ext-enable memcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Копируем конфигурации PHP
COPY docker/common/php/conf.d /usr/local/etc/php/conf.d
COPY docker/common/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# 2. Установка Composer (v2)
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    --version=2.8.8

WORKDIR /app

COPY ./ ./

RUN cd public/local \
    && composer install --no-dev --optimize-autoloader

RUN mkdir -p /app \
    && chown -R www-data:www-data /app \
    && mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions

CMD ["php-fpm"]