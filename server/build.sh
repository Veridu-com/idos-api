#!/bin/bash

set -e

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
cp conf.d/*.sql docker/infra/postgresql/docker-init.d/
cd docker/infra/postgresql
make clean
make build-all
