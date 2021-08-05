#!/bin/sh
.PHONY: build install init update analyse test
NAME = $(notdir $(CURDIR))

build:
	@docker build --no-cache -f .docker/php/Dockerfile -t $(NAME) .
install:
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer install
init: build install
update:
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer update
analyse:
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer analyse
test:
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer test
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer infection
