#! /usr/bin/make

PHP_FOLDERS=app bin tests

install:
	composer install
	make js
	php bin/command.php migrate:migrate
	php bin/command.php db:seed

js:
	yarn install
	yarn production

js-dev:
	yarn install
	yarn dev

js-watch:
	yarn install
	yarn watch