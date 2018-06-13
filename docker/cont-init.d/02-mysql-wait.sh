#!/usr/bin/with-contenv bash

echo "Waiting for MySQL"

while ! nc -z $DB_HOST $DB_PORT; do
  sleep 0.1
done

echo "MySQL started"
