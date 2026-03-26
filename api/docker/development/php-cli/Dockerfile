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
        msmtp \
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
COPY ./common/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# Supercronic — cron-демон для Docker (поддержка секундных интервалов)
ENV SUPERCRONIC_VERSION=v0.2.33
RUN curl -fsSL "https://github.com/aptible/supercronic/releases/download/${SUPERCRONIC_VERSION}/supercronic-linux-amd64" \
        -o /usr/local/bin/supercronic \
    && chmod +x /usr/local/bin/supercronic

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
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && chown -R www-data:www-data /app

ENTRYPOINT ["docker-entrypoint.sh"]
