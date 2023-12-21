# Levanta la arquitectura

file_selected := -f infrastructure/docker-compose.$(env).yml
environment := $(env)

up:
	@docker-compose $(file_selected) up -d

ps:
	@docker-compose $(file_selected) ps

down:
	@docker-compose $(file_selected) down

build:
	@docker-compose $(file_selected) build $(c)

restart:
	@docker-compose $(file_selected) restart $(c)

logs:
	@docker-compose $(file_selected) logs -f $(c)

logs_php:
	@docker-compose $(file_selected) exec -T php tail -f var/logs/$(environment).log

connect:
	@docker-compose $(file_selected) exec $(c) bash

connect_root:
	@docker-compose $(file_selected) exec -u root $(c) bash

install: up install_dependencies install_assets cache_clear update_database

install_dependencies:
	@docker-compose $(file_selected) exec -T php composer install

install_assets:
	@docker-compose $(file_selected) exec -T php php bin/console assets:install

cache_clear: up
	@docker-compose $(file_selected) exec -T backend php bin/console cache:clear --env=dev
	@docker-compose $(file_selected) exec -T backend php bin/console cache:clear --env=prod
	@docker-compose $(file_selected) exec -T backend rm -rf var/cache/dev
	@docker-compose $(file_selected) exec -T backend rm -rf var/cache/prod
	@docker-compose $(file_selected) exec -T backend chown -R www-data:www-data var/
	@docker-compose $(file_selected) exec -T backend chown -R www-data:www-data public/
	@docker-compose $(file_selected) exec -T backend chmod 755 -R var/cache

update_database: up
	@docker-compose $(file_selected) exec -T php php bin/console doctrine:migrations:migrate
