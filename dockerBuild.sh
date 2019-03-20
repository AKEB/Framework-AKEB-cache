#!/bin/bash

docker build --rm -f "Dockerfile.5.4" -t myphp:5.4 .

docker build --rm -f "Dockerfile.5.5" -t myphp:5.5 .

docker build --rm -f "Dockerfile.5.6" -t myphp:5.6 .

docker build --rm -f "Dockerfile.7.0" -t myphp:7.0 .

docker build --rm -f "Dockerfile.7.1" -t myphp:7.1 .

docker build --rm -f "Dockerfile.7.2" -t myphp:7.2 .
