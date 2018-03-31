<p align="center"><img width="128" height="128" src="https://avatars2.githubusercontent.com/u/13236453">
</p>

<p align="center">
<a href="https://travis-ci.org/AgentCoop/phpcrystal"><img src="https://travis-ci.org/AgentCoop/phpcrystal.svg?branch=master" alt="Build Status"></a>
<a href="https://packagist.org/packages/agentcoop/phpcrystal"><img src="https://poser.pugx.org/agentcoop/phpcrystal/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/agentcoop/phpcrystal"><img src="https://poser.pugx.org/agentcoop/phpcrystal/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/agentcoop/phpcrystal"><img src="https://poser.pugx.org/agentcoop/phpcrystal/license" alt="License"></a>
</p>

## About PhpCrystal
PhpCrystal is an extension of the popular PHP framework Laravel shipped with a modular approach and functionalities such as:
 * Declaration and auto-generation of routes and services using annotations
 * Error logging and reporting
 * Extended MVC classes
 * Docker configuration

## Installation
Create a new project:
```bash
composer create-project --no-install agentcoop/phpcrystal myapp
```
Build Docker images:
```bash
cd myapp && docker-compose up --build
```
Go to http://localhost:60000 and, if everything is good, you'll see the Laravel welcome page.

## Default application stack
 - MongoDb (*3.4.1v*)
 - Nginx (*1.13.1v*)
 - PHP (*7.2.1v*)
 - Linux Alpine (*3.7v*)

## Version compatibility
| Laravel | Package |
|---------|---------|
| 5.6.*   | 1.0.x   |

## License
Licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Documenation

## Contents
1. [Model](docs/model.md)
2. [View](docs/view.md)
    1. [Blade templates compiling](docs/blade-compiling.md)
    2. [jQuery DataTables plugin](docs/jquery-datatables.md)
3. [Controller](docs/controller.md)
5. [Service layer](docs/services.md)
4. Modules
    1. [Overview](docs/modules.md)
    2. [Annotated routing](docs/annotated-routing.md)
