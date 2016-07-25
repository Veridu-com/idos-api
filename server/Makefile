WEBSRV=srv-web
PHPSRV=srv-php
DBSRV=srv-db

rename: clean
	cat Makefile | sed s/srv-web/${WEBSRV}/ | sed s/srv-php/${PHPSRV}/ | sed s/srv-db/${DBSRV}/ > /tmp/Makefile && mv /tmp/Makefile Makefile
	cat docker-compose.yml | sed s/srv-web/${WEBSRV}/ | sed s/srv-php/${PHPSRV}/ | sed s/srv-db/${DBSRV}/ > /tmp/docker-compose.yml && mv /tmp/docker-compose.yml docker-compose.yml
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

logs:
	docker-compose logs --follow

dependencies:
	docker exec --interactive --tty ${PHPSRV} /usr/local/bin/composer update

test:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phpunit --coverage-text

cs-fixer:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/php-cs-fixer fix

security-check:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/security-checker security:check composer.lock

psysh:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/psysh

phinx-migrate:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx migrate

phinx-rollback:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx rollback

phinx-seed:
	docker exec --interactive --tty ${PHPSRV} vendor/bin/phinx seed:run

psql:
	docker exec --interactive --tty ${DBSRV} psql -h localhost idos-api idos-api
