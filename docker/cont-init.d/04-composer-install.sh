#!/usr/bin/env bash


export HOME=/var/www

cd /var/www
s6-setuidgid 1000:1000 composer install
