.PHONY: start stop init build tests

start:
	docker-compose up -d

stop:
	docker-compose stop

down:
	docker-compose down --remove-orphans

logs:
	docker-compose logs

ls:
	docker container ls

cs:
	docker-compose exec php php vendor/bin/phpcs

cbf:
	docker-compose exec php php vendor/bin/phpcbf

init:
	docker-compose build
	docker-compose up -d
	docker-compose exec php composer install
	docker-compose exec php php bin/console doctrine:database:create
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction

build:
	docker-compose build

tests:
	docker-compose exec php php vendor/bin/simple-phpunit
