#!/usr/bin/with-contenv bash

echo "Waiting for MySQL"

while ! nc -z $DB_HOST $DB_PORT; do
  sleep 0.1
done

# ХЗ, что за дичь, но иначе миграции падают
# Ждём, пока всё полностью поднимется
sleep 20

echo "MySQL started"
