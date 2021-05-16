.Phony: start
start:
	docker-compose up -d
	docker-compose exec -T php sh ./.docker/wait_for_nginx.sh

.Phony: php
php:
	docker-compose exec php bash

.Phony: stop
stop:
	docker-compose stop

.Phony: test
test:
	docker-compose exec -T php ./vendor/bin/phpunit