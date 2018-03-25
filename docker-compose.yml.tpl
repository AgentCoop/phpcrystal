version: '3.2'

volumes:
  %project-name%-mongodb-data:

services:
  %project-name%-php:
    build: .
    container_name: "php.%project-name%"
    volumes:
      - type: bind
        source: ./
        target: /var/www/html
      - /var/www/html/vendor
      - /var/www/html/node_modules
    environment:
      - XDEBUG_CONFIG='idekey=xdebug; xdebug.remote_host=<your IP address>'
      - MONGODB_HOST=%project-name%-mongodb

  %project-name%-nginx:
    build: ./config/docker/nginx/dev
    container_name: "nginx.%project-name%"
    ports:
      - 60000:80
    volumes:
      - type: bind
        source: ./
        target: /var/www/html

  %project-name%-mongodb:
    image: mongo:3.4.1
    container_name: "mongodb.%project-name%"
    volumes:
      - %project-name%-mongodb-data:/data/db

  %project-name%-mongodb-data:
    image: alpine
    container_name: "data.mongodb.%project-name%"
    volumes:
      - type: volume
        source: %project-name%-mongodb-data
        target: /data/db
    command: "true"

  %project-name%-redis:
    image: redis:3.2.7-alpine
    container_name: "redis.%project-name%"

