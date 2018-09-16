#!/usr/bin/with-contenv bash

cd /var/www

fails=0
while ! s6-setuidgid www-data php bin/console doctrine:migration:migrate -qn > /dev/null 2>&1; do
    (( ++fails ))
    if [ "$fails" -gt 5 ]; then
        echo 'Migrations failed'
        exit 1
    fi
    sleep 5
done;
