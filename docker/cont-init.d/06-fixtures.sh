#!/usr/bin/with-contenv bash

cd /var/www

s6-setuidgid www-data php bin/console doctrine:fixtures:load --append -n
