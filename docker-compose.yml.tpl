version: '2'
services:
  %project-name%-php:
    build: .
    container_name: "php.%project-name%"
    volumes:
      - .:/var/www/html
    environment:
      - XDEBUG_CONFIG='idekey=xdebug; xdebug.remote_host=<your_ip_address>'
      - MONGODB_HOST=%project-name%-mongodb

  %project-name%-nginx:
    build: ./config/docker/nginx/dev
    container_name: "nginx.%project-name%"
    ports:
      - 60000:80
    volumes_from:
      - %project-name%-php:ro

  %project-name%-mongodb:
    image: mongo:3.4.1
    container_name: "mongodb.%project-name%"
    volumes_from:
      - %project-name%-mongodb-data

  %project-name%-mongodb-data:
    image: alpine
    container_name: "data.mongodb.%project-name%"
    volumes:
      - /data/db
    command: "true"

  %project-name%-redis:
    image: redis:3.2.7-alpine
    container_name: "redis.%project-name%"
