#!/bin/bash

echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 5.4$(tput sgr 0)"
docker run --rm -v "$PWD":/opt -w /opt myphp:5.4 php ./vendor/bin/phpunit

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 5.5$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt myphp:5.5 php ./vendor/bin/phpunit

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 5.6$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt myphp:5.6 php ./vendor/bin/phpunit

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 7.0$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt myphp:7.0 php ./vendor/bin/phpunit

echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 7.1$(tput sgr 0)"
docker run --rm -v "$PWD":/opt -w /opt myphp:7.1 php ./vendor/bin/phpunit

# echo "$(tput setaf 16)$(tput setab 2)Run test for PHP 7.2$(tput sgr 0)"
# docker run --rm -v "$PWD":/opt -w /opt myphp:7.2 php ./vendor/bin/phpunit
