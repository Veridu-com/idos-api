#!/bin/bash

# updates docker project
if [ -d "docker" ]; then
    cd docker/
    git pull origin master
    cd ../
else
    git clone git@bitbucket.org:veridu/docker.git
fi

# builds tools/cli-php
cd docker/tools/cli-php
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
