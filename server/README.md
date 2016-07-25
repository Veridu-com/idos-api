# Docker Containers

To run docker containers, there's a `Makefile` script to make your life easier.

idOS currently relies on **docker/idos/api/nginx**, **docker/idos/api/php-fpm** and **docker/infra/postgresql**.

You MUST have access to [this repository](https://bitbucket.org/veridu/docker).

## Internals

### Names

Currently the **nginx** container is named as **srv-web**; the **php-fpm** container is named as **srv-php** and the **postgresql** container is named as **srv-db**.

You MUST have unique names if running the containers in a shared environment.

If needed, you can run `make rename` and pass one or more of `WEBSRV`, `PHPSRV` and `DBSRV` (e.g. `make rename WEBSRV=mywebserver PHPSRV=phprocks` to rename *srv-web* to *mywebserver* and *srv-php* to *phprocks*).

### IP Address

In case you need, internal IP addresses for each server:

- **srv-web**: 172.16.238.10
- **srv-php**: 172.16.238.11, 172.16.239.11
- **srv-db**: 172.16.239.12

### Database

There are two database available:

- **idos-api** (user: **idos-api** / password: **idos-api**)
- **idos-test** (user: **idos-test** / password: **idos-test**)

## Building

Either on your first usage or whenever you want to update your containers with upstream changes, you may run `make build`, it will fetch/update the docker project with all required image definitions and then build the containers.

**ALERT!** This will **destroy** all your database data.

### Cleanup

If you want to rebuild your local containers, you can run `make reset`.

**ALERT!** This will **destroy** all your database data.

## Execution

### Start Services

To start the services, simply run `make up` (or `make start`).

### Stop Services

To stop the services, simply run `make down` (or `make stop`).

## Tools

### Server

You can get bash access to any **running** server by simply running `make web-bash` (for bash access to **nginx** server), `make php-bash` (for bash access to **php-fpm** server) or `make db-bash` (for bash access to **postgresql** server).

You can also get the tail output of a service log by running `make web-log` (for **nginx** log), `make php-log` (for **php-fpm** log) or `make db-log` (for **postgresql** log).

You can also get the tail follow output of all services logs by running `make logs`.

### PHP

#### Composer Install/Update

You can install/update project dependencies by running `make dependencies`.

#### PHPUnit Tests

You can execute PHPUnit Tests by running `make test`.

#### PHP-CS-Fixer

You can execute PHP-CS-Fixer by running `make cs-fixer`.

#### Security Check

You can execute Security-Checker by running `make security-check`.

#### PsySH

You can execute PsySH by running `make psysh`.

#### Phinx

##### Migrate

You can execute Phinx's Migrate command by running `make phinx-migrate`.

##### Rollback

You can execute Phinx's Rollback command by running `make phinx-rollback`.

##### Seed

You can execute Phinx's Seed command by running `make phinx-seed`.

### PostgreSQL

You can execute PostgreSQL's terminal-based front-end by running `make psql` - it will require **idos-api** user password.
