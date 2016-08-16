#!/bin/bash

set -e

# updates docker project
if [ -d "docker" ]; then
    cd docker/
    git pull origin master
    cd ..
else
    git clone git@bitbucket.org:veridu/docker.git
fi

# builds idos/api/nginx
cd docker/idos/api/nginx
if [ `docker ps -a | grep nginx` ]; then
    make clean
fi
make build
cd -

# builds idos/api/php-fpm
cd docker/idos/api/php-fpm
if [ `docker ps -a | grep php-fpm` ]; then
    make clean
fi
make build
cd -

# builds infra/postgresql
if [ ! -d "docker/infra/postgresql/docker-init.d/" ]; then
    mkdir docker/infra/postgresql/docker-init.d/;
fi
cp conf.d/*.sql docker/infra/postgresql/docker-init.d/
cd docker/infra/postgresql
if [ `docker ps -a | grep postgresql` ]; then
    make clean
fi
make build
cd -

# builds infra/gearman
cd docker/infra/gearman
if [ `docker ps -a | grep gearman` ]; then
    make clean
fi
make build
cd -

# builds tools/cli-php
cd docker/tools/cli-php
if [ `docker ps -a | grep cli-php` ]; then
    make clean
fi
make build
cd -

# builds idos/manager/php
if [ -d "docker/idos/manager/php/idos-manager" ]; then
    cd docker/idos/manager/php/idos-manager/
    git pull origin master
    cd -
else
    cd docker/idos/manager/php
    git clone git@bitbucket.org:veridu/idos-manager.git
    cd -
fi
cd docker/idos/manager/php
if [ `docker ps -a | grep php` ]; then
    make clean
fi
make build
cd -

# builds infra/redis
cd docker/infra/redis
if [ `docker ps -a | grep redis` ]; then
    make clean
fi
make build
cd -
