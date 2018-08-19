#!/usr/bin/with-contenv bash

echo "Waiting for Graylog"

while ! nc -uz $GRAYLOG_HOST $GRAYLOG_GELF_PORT; do
  sleep 0.1
done

echo "Graylog started"
