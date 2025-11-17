SHELL := /bin/bash

SAIL := ./vendor/bin/sail
COMPOSER_IMAGE := laravelsail/php84-composer:latest

.PHONY: help init up down restart logs test artisan migrate fresh

help:
	@echo "Available targets:"
	@echo "  make init      - Initialize environment (.env + Sail + APP_KEY)"
	@echo "  make up        - Start the application using Laravel Sail (Docker) in detached mode"
	@echo "  make down      - Stop and remove Sail containers"
	@echo "  make restart   - Restart Sail containers"
	@echo "  make logs      - Follow application logs from Sail"
	@echo "  make test      - Run the test suite inside the Sail PHP container"
	@echo "  make artisan   - Run an arbitrary Artisan command inside Sail (usage: make artisan cmd='migrate')"
	@echo "  make migrate   - Run database migrations inside Sail"
	@echo "  make fresh     - Drop all tables and re-run migrations (artisan migrate:fresh)"

init:
	@if [ ! -f .env ]; then \
			cp .env.example .env; \
			echo ".env created from .env.example"; \
	else \
			echo ".env already exists, skipping copy from .env.example"; \
	fi
	
	docker run --rm \
		-u "$$(id -u):$$(id -g)" \
		-v "$$(pwd):/var/www/html" \
		-w /var/www/html \
		$(COMPOSER_IMAGE) \
		composer install --ignore-platform-reqs

	-@docker rmi -f $(COMPOSER_IMAGE) >/dev/null 2>&1 || true

	$(SAIL) up -d
	$(SAIL) artisan key:generate
	make migrate

up:
	$(SAIL) up -d

down:
	$(SAIL) down

restart: down up

logs:
	$(SAIL) logs -f

test:
	$(SAIL) test

artisan:
	@if [ -z "$(cmd)" ]; then \
			echo "Usage: make artisan cmd='your:command'"; \
			exit 1; \
	fi
	$(SAIL) artisan $(cmd)

migrate:
	$(SAIL) artisan migrate --seed

fresh:
	$(SAIL) artisan migrate:fresh