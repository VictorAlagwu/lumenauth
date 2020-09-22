
ssh:
	@docker-compose $(project) exec $(optionT) laravel bash

ssh-mysql:
	@docker-compose $(project) exec mysql bash

exec:
	@docker-compose $(project) exec $(optionT) laravel $$cmd

exec-bash:
	@docker-compose $(project) exec $(optionT) laravel bash -c "$(cmd)"



composer-install-prod:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-dev"

composer-install:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader"

composer-update:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer update"

info:
	@make exec cmd="php artisan --version"
	@make exec cmd="php --version"

logs:
	@docker logs -f laravel

logs-supervisord:
	@docker logs -f supervisord

logs-mysql:
	@docker logs -f mysql

drop-migrate:
	@make exec cmd="php artisan migrate:fresh"
	@make exec cmd="php artisan migrate:fresh --env=test"

migrate-prod:
	@make exec cmd="php artisan migrate --force"

migrate:
	@make exec cmd="php artisan migrate --force"
	@make exec cmd="php artisan migrate --force --env=test"

seed:
	@make exec cmd="php artisan db:seed --force"

phpunit:
	@make exec cmd="./vendor/bin/phpunit -c phpunit.xml --coverage-html reports/coverage --coverage-clover reports/clover.xml --log-junit reports/junit.xml"

###> phpcs ###
phpcs: ## Run PHP CodeSniffer
	@make exec-bash cmd="./vendor/bin/phpcs --version && ./vendor/bin/phpcs --standard=PSR2 --colors -p app"
###< phpcs ###

###> ecs ###
ecs: ## Run Easy Coding Standard
	@make exec-bash cmd="error_reporting=0 ./vendor/bin/ecs --clear-cache check app"

ecs-fix: ## Run The Easy Coding Standard to fix issues
	@make exec-bash cmd="error_reporting=0 ./vendor/bin/ecs --clear-cache --fix check app"
###< ecs ###

###> phpmetrics ###
phpmetrics:
	@make exec cmd="make phpmetrics-process"

phpmetrics-process: ## Generates PhpMetrics static analysis, should be run inside symfony container
	@mkdir -p reports/phpmetrics
	@if [ ! -f reports/junit.xml ] ; then \
		printf "\033[32;49mjunit.xml not found, running tests...\033[39m\n" ; \
		./vendor/bin/phpunit -c phpunit.xml --coverage-html reports/coverage --coverage-clover reports/clover.xml --log-junit reports/junit.xml ; \
	fi;
	@echo "\033[32mRunning PhpMetrics\033[39m"
	@php ./vendor/bin/phpmetrics --version
	@./vendor/bin/phpmetrics --junit=reports/junit.xml --report-html=reports/phpmetrics .
###< phpmetrics ###