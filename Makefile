up: docker-up
down: docker-down
restart: docker-down docker-up

init: docker-down-clear docker-pull docker-build docker-up v-init test

v-init: composer-install

test:
	docker-compose run --rm php-cli php bin/phpunit

fixer-check:
	docker-compose run --rm php-cli vendor/bin/phpcs --standard=PSR12 src --ignore=src/Infrastructure/Migrations/

fixer-fix:
	docker-compose run --rm php-cli vendor/bin/phpcbf --standard=PSR12 src

psalm:
	docker-compose run --rm php-cli vendor/bin/psalm --show-info=false

composer-install:
	docker-compose run --rm php-cli composer install

migrations-install:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

migrations-diff:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:diff

fixtures-load:
	docker-compose run --rm php-cli php bin/console doctrine:fixtures:load --no-interaction

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build
