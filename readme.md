## About this repository
LPB is a Web skeleton application based on Laravel PHP framework. Out of the box you will get:
1. Extended Laravel controller
2. Basic classes for models and view services
3. Error logging and reporting
4. A default view template for emails
5. A Docker configuration to run your application

## Installation
Create a new project:
```bash
composer create-project agentcoop/laravel-project-blueprint
```
Build Docker images:
```bash
docker-compose up --build
```
Go to https://localhost:60001 and, if everything is good, you'll see the Laravel welcome page. The SSL certificate is a
self-signed one, so don't be confused by a browser warning.

## License
Licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Documenation

## Contents
1. [Model](docs/model.md)
2. [View](docs/view.md)
    1. [Blade templates compiling](docs/blade-compiling.md)
    2. [jQuery DataTables plugin](docs/jquery-datatables.md)
3. [Controller](docs/controller.md)
