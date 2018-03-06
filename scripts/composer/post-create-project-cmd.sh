#!/usr/bin/env bash

NGINX_CONF_DIR=./config/docker/nginx/dev/sites-enabled/

mv -f docker-compose.yml.tpl docker-compose.yml
mv $NGINX_CONF_DIR/app.conf.tpl $NGINX_CONF_DIR/app.conf

sed -i "s/%project-name%/$(basename $(pwd))/g" \
    ./docker-compose.yml \
    $NGINX_CONF_DIR/app.conf