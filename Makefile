#! /usr/bin/make

PHP_FOLDERS=app bin tests

install:
	composer install
	make js
	php artisan migrate
    php artisan key:generate

js:
	yarn install
	yarn production

js-dev:
	yarn install
	yarn dev

js-watch:
	yarn install
	yarn watch