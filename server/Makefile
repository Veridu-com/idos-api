up:
	docker-compose up

down:
	docker-compose down

web-exec:
	docker exec --interactive --tty srv-web $(filter-out $@,$(MAKECMDGOALS))

php-exec:
	docker exec --interactive --tty srv-php $(filter-out $@,$(MAKECMDGOALS))

db-exec:
	docker exec --interactive --tty srv-db $(filter-out $@,$(MAKECMDGOALS))

%:
	@:
