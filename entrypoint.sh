#!/bin/bash

mkdir src
mkdir tests

cp code/src/* ./src/
cp code/tests/*.php ./tests/

cp code/phpunit.xml ./
cp code/composer.json ./

memcached -p 11211 -d -u memcache

composer install

php ./vendor/bin/phpunit --no-coverage
