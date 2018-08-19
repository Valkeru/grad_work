#!/usr/bin/with-contenv bash

if [ "$APP_ENV" = "dev" ]; then
    echo "Development environment detected, Xdebug installation will be performed"
    DEBIAN_FRONTEND=noninteractive apt-get install -o Dpkg::Options::="--force-confold" -y php-xdebug
    exit 0
fi

echo "Production environment, Xdebug will not be installed"
