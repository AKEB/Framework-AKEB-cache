#!/bin/bash

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 7.2$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt babadzhanyan/cache_php-unit:7.2 /bin/bash -c "memcached -p 11211 -d -u memcache; memcached -p 11212 -d -u memcache; composer install --prefer-source --no-interaction; php ./vendor/bin/phpunit --no-coverage"

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 7.3$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt babadzhanyan/cache_php-unit:7.3 /bin/bash -c "memcached -p 11211 -d -u memcache; memcached -p 11212 -d -u memcache; composer install --prefer-source --no-interaction; php ./vendor/bin/phpunit --no-coverage"

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 7.4$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt babadzhanyan/cache_php-unit:7.4 /bin/bash -c "memcached -p 11211 -d -u memcache; memcached -p 11212 -d -u memcache; composer install --prefer-source --no-interaction; php ./vendor/bin/phpunit --no-coverage"

echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 8.0$(tput sgr 0)"
docker run --rm -v "$PWD":/opt -w /opt babadzhanyan/cache_php-unit:8.0 /bin/bash -c "memcached -p 11211 -d -u memcache; memcached -p 11212 -d -u memcache; composer install --prefer-source --no-interaction; php ./vendor/bin/phpunit --no-coverage"

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 8.1$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt babadzhanyan/cache_php-unit:8.1 /bin/bash -c "memcached -p 11211 -d -u memcache; memcached -p 11212 -d -u memcache; composer install --prefer-source --no-interaction; php ./vendor/bin/phpunit --no-coverage"

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 8.2$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt babadzhanyan/cache_php-unit:8.2 /bin/bash -c "memcached -p 11211 -d -u memcache; memcached -p 11212 -d -u memcache; composer install --prefer-source --no-interaction; php ./vendor/bin/phpunit --no-coverage"
