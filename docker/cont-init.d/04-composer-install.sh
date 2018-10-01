#!/usr/bin/with-contenv bash


export HOME=/var/www

cd /var/www
if [ "$APP_ENV" = "dev" ]; then
    s6-setuidgid 1000:1000 composer install
else
    s6-setuidgid 1000:1000 composer install --no-dev
fi
