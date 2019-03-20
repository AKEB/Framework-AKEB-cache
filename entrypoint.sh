#!/bin/bash

memcached -p 11211 -d -u memcache
memcached -p 11212 -d -u memcache


# mkdir src
# mkdir tests
# mkdir vendor

# cp -r code/src/* ./src/
# cp -r code/tests/* ./tests/

# cp code/phpunit.xml ./
# cp code/composer.json ./

# cp -r code/vendor/* ./vendor/
# cp code/composer.lock ./

# composer install

php ./vendor/bin/phpunit --no-coverage
