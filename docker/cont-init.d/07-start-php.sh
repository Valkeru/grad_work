#!/usr/bin/env bash

sed -i 's/listen = \/run\/php\/php7.2-fpm.sock/listen = 0.0.0.0:9000/g' /etc/php/7.2/fpm/pool.d/www.conf
service php7.2-fpm start
