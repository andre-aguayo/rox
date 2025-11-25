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
	@retries=10; \
	sleep_time=5; \
	count=0; \
	until $(SAIL) artisan migrate --seed; do \
		count=$$((count+1)); \
		if [ $$count -ge $$retries ]; then \
			echo "Migrations failed after $$count attempts. Giving up."; \
			exit 1; \
		fi; \
		echo "Migration failed. Waiting for database to be ready... (attempt $$count/$$retries)"; \
		sleep $$sleep_time; \
	done

fresh:
	$(SAIL) artisan migrate:fresh
