FROM php:8.3-fpm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libicu-dev \
    && docker-php-ext-install pdo_mysql zip intl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g www-data www-data

WORKDIR /var/www/html

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
