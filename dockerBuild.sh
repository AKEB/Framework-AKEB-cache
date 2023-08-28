#!/bin/bash

docker build --rm -f "Dockerfile.7.2" -t babadzhanyan/cache_php-unit:7.2 . --push
docker build --rm -f "Dockerfile.7.3" -t babadzhanyan/cache_php-unit:7.3 . --push
docker build --rm -f "Dockerfile.7.4" -t babadzhanyan/cache_php-unit:7.4 . --push
docker build --rm -f "Dockerfile.8.0" -t babadzhanyan/cache_php-unit:8.0 . --push
docker build --rm -f "Dockerfile.8.1" -t babadzhanyan/cache_php-unit:8.1 . --push
docker build --rm -f "Dockerfile.8.2" -t babadzhanyan/cache_php-unit:8.2 . --push
