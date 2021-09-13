#!/bin/bash

memcached -p 11211 -d -u memcache
memcached -p 11212 -d -u memcache

php ./vendor/bin/phpunit --no-coverage
