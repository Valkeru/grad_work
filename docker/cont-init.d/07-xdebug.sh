#!/usr/bin/with-contenv bash

if [ "$APP_ENV" = "dev" ]; then
    echo "Install Xdebug for development environment"
    DEBIAN_FRONTEND=noninteractive apt-get install -o Dpkg::Options::="--force-confold" -y php-xdebug
    exit 0
fi
