#!/bin/bash

# updates docker project
if [ -d "docker" ]; then
    cd docker/
    git pull origin master
    cd ../
else
    git clone git@bitbucket.org:veridu/docker.git
fi

# builds idos/api/nginx
cd docker/idos/api/nginx
make clean
make build-all
cd -

# builds idos/api/php-fpm
cd docker/idos/api/php-fpm
make clean
make build-all
cd -

# builds infra/postgresql
if [ ! -d "docker/infra/postgresql/docker-init.d/" ]; then
    mkdir docker/infra/postgresql/docker-init.d/;
fi
cp conf.d/*.sql docker/infra/postgresql/docker-init.d/
cd docker/infra/postgresql
make clean
make build-all
cd -

# builds infra/gearman
cd docker/infra/gearman
make clean
make build-all
cd -

# builds idos/manager/php
cd docker/idos/manager/php
make clean
make build-all
cd -

# builds infra/redis
cd docker/infra/redis
make clean
make build-all
cd -
