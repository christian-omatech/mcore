#!/bin/sh
.PHONY: build

NAME = $(notdir $(CURDIR))

build:
	@docker build --no-cache -f .docker/php/Dockerfile -t $(NAME) .
install:
	@docker run -it --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer update
test:
	@docker run -it --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer test
	@docker run -it --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer infection
analyse:
	@docker run -it --rm --name $(NAME) -v $(shell pwd)/:/app $(NAME) composer analyse
