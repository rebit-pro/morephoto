FROM php:8.4-cli
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
# Сменить UID/GID www-data на хостовый (1000) — чтобы CLI-процессы
# могли писать в bind-mounted volume (миграции, симлинки модулей и т.д.)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
# Копируем конфигурации PHP
COPY docker/common/php/conf.d /usr/local/etc/php/conf.d
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2.8 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY ./ ./
RUN composer dump-autoload --no-dev --optimize

RUN mkdir -p /app /var/lib/php/sessions \
    && chown -R www-data:www-data /app /var/lib/php/sessions
