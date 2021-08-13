#!/bin/sh
.PHONY: build down clear install init update dump analyse test

UID := $(shell id -u)
GID := $(shell id -g)

build:
	@env UID=${UID} GID=${GID} docker-compose -f .docker/docker-compose.yml build --force-rm --no-cache
down:
	@env UID=${UID} GID=${GID} docker-compose -f .docker/docker-compose.yml down
clear:
	@env UID=${UID} GID=${GID} docker-compose -f .docker/docker-compose.yml down --rmi all --volumes
	@env UID=${UID} GID=${GID} docker system prune --all --force
install:
	@env UID=${UID} GID=${GID} docker-compose -f .docker/docker-compose.yml run --rm php composer install
init: build install
update:
	@env UID=${UID} GID=${GID} docker-compose -f .docker/docker-compose.yml run --rm php composer update
dump:
	@env UID=${UID} GID=${GID} docker-compose -f .docker/docker-compose.yml run --rm php composer dump
analyse:
	@env UID=${UID} GID=${GID} docker-compose -f .docker/docker-compose.yml run --rm php composer analyse
test:
	@env UID=${UID} GID=${GID} docker-compose -f .docker/docker-compose.yml run --rm php composer test
