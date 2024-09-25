#!/bin/sh

cd /app || exit 1

composer install && \
composer run-script apidoc && \
docker/wait-for database:3306 && sleep 2 && \
php bin/console doctrine:database:create --if-not-exists && \
php bin/console doctrine:migrations:migrate --no-interaction && \
php bin/console admin:init && \
php -S 0.0.0.0:8000 -t public/
