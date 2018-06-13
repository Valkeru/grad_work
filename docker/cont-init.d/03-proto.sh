#!/usr/bin/env bash

#export PATH=/usr/local/bin:$PATH

cd /var/www/
rm -rf lib/api/{Valkeru,GPBMetadata,Google}
shopt -s globstar
s6-setuidgid www-data protoc -I=lib/proto --php_out=lib/api --grpc_out=lib/api \
    --plugin=protoc-gen-grpc=$(which grpc_php_plugin) lib/proto/**/*.proto
