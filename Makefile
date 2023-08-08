#! /usr/bin/make

PHP_FOLDERS=app bin tests

install:
	composer install
	make js
	php artisan migrate

install-demo: install
	php artisan application:demo

js:
	yarn install
	yarn production

js-dev:
	yarn install
	yarn dev

js-watch:
	yarn install
	yarn watch