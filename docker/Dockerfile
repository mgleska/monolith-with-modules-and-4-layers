FROM php:8.3-cli-alpine AS dev

RUN docker-php-ext-install pdo_mysql && \
    curl -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar && \
    chmod +x /usr/local/bin/composer

WORKDIR /app


FROM dev AS app-kubernetes

ENV APP_ENV=prod APP_DEBUG=0 COMPOSER_ALLOW_SUPERUSER=1

COPY . /app
RUN composer install --no-cache --no-dev --optimize-autoloader && \
    composer run-script apidoc
