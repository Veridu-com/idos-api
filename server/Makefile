WEBSRV=srv-web
PHPSRV=srv-php
DBSRV=srv-db
QUEUESRV=srv-queue
WORKERSRV=srv-worker
REDISSRV=srv-redis
NOSQLSRV=srv-nosql
SKYNETSRV=srv-skynet

rename: clean
	cat Makefile |
	sed s/srv-web/${WEBSRV}/ |
	sed s/srv-php/${PHPSRV}/ |
	sed s/srv-db/${DBSRV}/ |
	sed s/srv-queue/${QUEUESRV}/ |
	sed s/srv-worker/${WORKERSRV}/ |
	sed s/srv-redis/${REDISSRV}/ |
	sed s/srv-mongo/${NOSQLSRV}/ > /tmp/Makefile && mv /tmp/Makefile Makefile
	cat docker-compose.yml |
	sed s/srv-web/${WEBSRV}/ |
	sed s/srv-php/${PHPSRV}/ |
	sed s/srv-db/${DBSRV}/ |
	sed s/srv-queue/${QUEUESRV}/ |
	sed s/srv-worker/${WORKERSRV}/ |
	sed s/srv-redis/${REDISSRV}/ |
	sed s/srv-mongo/${NOSQLSRV}/ > /tmp/docker-compose.yml && mv /tmp/docker-compose.yml docker-compose.yml
	@echo You must run $(tput bold) \> make reset \< $(tput sgr0) to start your containers

build: clean
	./build.sh
	docker-compose up

up:
	docker-compose start

start: up

down:
	docker-compose stop

stop: down

clean:
	docker-compose down

reset: clean
	docker-compose up

remove-all:
	docker rm $(docker ps -a -q)

web-bash:
	docker exec --interactive --tty ${WEBSRV} bash

web-log:
	docker logs ${WEBSRV}

php-bash:
	docker exec --interactive --tty ${PHPSRV} bash

php-log:
	docker logs ${PHPSRV}

db-bash:
	docker exec --interactive --tty ${DBSRV} bash

db-log:
	docker logs ${DBSRV}

skynet-bash:
	docker exec --interactive --tty --user root ${SKYNETSRV} bash

stats:
	docker stats

logs:
	docker-compose logs --follow

dependencies:
	docker exec --interactive --tty ${PHPSRV} /usr/local/bin/composer update

deps: dependencies

test:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phpunit --coverage-text

test-quick:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phpunit

test-unit:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phpunit test/Unit --coverage-text

test-func:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phpunit test/Functional --coverage-text

humbug:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/humbug

cs-fixer:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/php-cs-fixer fix

security-check:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/security-checker security:check composer.lock

psysh:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/psysh

phinx-migrate:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx migrate

phinx-migrate-testing:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx migrate -e testing

phinx-rollback:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx rollback

phinx-rollback-all:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx rollback -t 0

phinx-seed:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx seed:run

phinx-seed-testing:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx seed:run -e testing

phinx-restore: phinx-migrate phinx-seed

psql:
	docker exec --interactive --tty ${DBSRV} psql -h localhost idos-api idos-api

supervisorctl:
	docker exec --interactive --tty ${QUEUESRV} supervisorctl

gearadmin:
	docker exec --interactive --tty ${QUEUESRV} gearadmin --status

redis-cli:
	docker exec --interactive --tty ${REDISSRV} redis-cli

mongo:
	docker exec --interactive --tty ${NOSQLSRV} mongo
