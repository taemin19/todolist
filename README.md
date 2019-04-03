# OpenClassrooms-Projet8

[Améliorez une application existante de ToDo & Co](https://openclassrooms.com/fr/projects/v1/ameliorer-un-projet-existant-1/assignment)

## Description

The application is written in PHP with the Symfony framework.

The development is based on:
- [Symfony 3.4](https://symfony.com/doc/3.4/index.html)
- [Encore](https://symfony.com/doc/3.4/frontend/encore/installation.html) (webpack)
- [PHPUnit](https://phpunit.de/index.html) (unit/integration tests)
- [Behat](http://behat.org/en/latest/) & [Chrome Behat](https://gitlab.com/DMore/behat-chrome-extension) (functional tests)
- [Travis](https://travis-ci.org/) (CI)

## Requirements
- PHP 7.1 or higher
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/en/download/) & [Yarn](https://yarnpkg.com/lang/en/docs/install/#debian-stable) (for webpack)
- Google chrome (59+) or chromium (for functional tests)
- [Redis](https://redis.io/) (for caching)

## Installation
#### Install the project
The project should be installed using [Make](https://www.gnu.org/software/make/).
    
    $ git clone https://github.com/taemin19/todolist.git
    $ cd todolist
    $ make dev

See the available commands of the Makefile:

    $ make

#### Database
Add the tables/schema to database:

    $ make db

Load a set of data:

    $ make db-f

#### Testing
Create test database and add tables/schema:

    $ make db--test

**Run unit tests:**

    $ make test-u

**Run integration tests:**

    $ make test-i

**Run functional tests:**

    $ make test-f

Before running functional test, make sure to run:
- [Chrome with headless mode](https://developers.google.com/web/updates/2017/04/headless-chrome)


    # Example
    $ google-chrome-stable --headless --disable-gpu --remote-debugging-port=9222

- Symfony Web Server


    $ make server-on



### Author
- Daniel Thébault
