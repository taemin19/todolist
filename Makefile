.PHONY: cache-clear
.DEFAULT_GOAL = help

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-10s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## Main commands
dev: ## Install the app a development environment
	composer install

prod: ## Install the app for a production environment
	compser install --no-dev --optimize-autoloader

auto: composer.json ## Update the autoloader
	composer dump-autoload -a -o

## Database commands
db: ## Create the database and add tables/schema
	php bin/console doctrine:database:create
	make db-u

db-u: ## Update tables/schema
	php bin/console doctrine:schema:update --force

db--test: app/config/config_test.yml ## Create the test database and add tables/schema
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update --force --env=test

db-v: ## Validate mapping/database
	php bin/console doctrine:schema:validate

db-f: src/AppBundle/Resources/fixtures ## Load a "fake" set data into the database
	php bin/console doctrine:fixtures:load

## Symfony commands
cache: var/cache ## Clear the cache in the current environment
	php bin/console cache:clear

cache--dev: var/cache/dev ## Clear the cache in the dev environment
	php bin/console cache:clear --env=dev

cache--prod: var/cache/prod ## Clear the cache in the prod environment
	php bin/console cache:clear --env=prod --no-debug

cache--test: var/cache/test ## Clear the cache in the test environment
	php bin/console cache:clear --env=test

debug-r: app/config ## Get a list of the routes
	php bin/console debug:router

debug-a: ## Get a list of the autowireable services
	php bin/console debug:autowiring

debug-c: ## Get informations for a service in the container, [ID=service_id]* (required*)
	php bin/console debug:container $(ID)

server-on: ## Start the PHP's built-in web server
	php bin/console server:start

server-off: ## Stop the PHP's built-in web server
	php bin/console server:stop

server-i: ## Check if the PHP's built-in web server is listening
	php bin/console server:status

## Test commands

test-f: features ## Run functional tests, [FEATURE=example.feature] to test a specific feature
	vendor/bin/behat features/$(FEATURE)

test-u: tests ## Run unit tests, [TEST=Dir[/Test.php]] to test a directory or a specific test file
	php ./bin/phpunit tests/$(TEST)

## Blackfire commands
blackfire: ## Profile HTTP request, [ROUTE=path]* (required*)
	blackfire curl http://localhost:8000/$(ROUTE)
