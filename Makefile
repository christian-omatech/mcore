#!/bin/sh
.PHONY: build

NAME = $(notdir $(CURDIR))

build:
	@docker build --no-cache -f .docker/php/Dockerfile -t $(NAME) .
install:
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer update
analyse:
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer analyse
test:
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer test
	@docker run -t --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer infection
