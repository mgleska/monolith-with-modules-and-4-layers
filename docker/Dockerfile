FROM php:8.3-cli-alpine

RUN docker-php-ext-install pdo_mysql && \
    curl -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar && \
    chmod +x /usr/local/bin/composer

WORKDIR /app
