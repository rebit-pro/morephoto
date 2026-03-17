FROM php:8.4-cli

# Устанавливаем системные зависимости
RUN apt-get update \
    && apt-get install -y \
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
        libxml2-utils \
    && pecl install memcache \
    && docker-php-ext-enable memcache \
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
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y netcat-openbsd

RUN apt-get update && apt-get install -y --no-install-recommends libxml2-utils \
    && rm -rf /var/lib/apt/lists/*

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php/conf.d /usr/local/etc/php/conf.d

ENV COMPOSER_ALLOW_SUPERUSER 1

# Установка последней версии Composer v2
#RUN curl -sS https://getcomposer.org/installer | php -- \
#    --install-dir=/usr/local/bin \
#    --filename=composer \
#    --version=2.8.8

# Заменить RUN curl ... на:
COPY --from=composer:2.8 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

# Установить владельца для /app
RUN chown -R www-data:www-data /app
