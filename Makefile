# Docker Makefile for Laravel Resume Builder AI

.PHONY: help build up down restart logs shell artisan composer npm fresh migrate seed seeds test test-verbose test-file cache-clear setup

# Default target
help:
	@echo "Laravel Resume Builder AI - Docker Commands"
	@echo ""
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@echo "  build       Build Docker containers"
	@echo "  up          Start containers in background"
	@echo "  down        Stop and remove containers"
	@echo "  restart     Restart all containers"
	@echo "  logs        View container logs"
	@echo "  shell       Open bash shell in app container"
	@echo "  artisan     Run artisan command (usage: make artisan cmd='migrate')"
	@echo "  composer    Run composer command (usage: make composer cmd='install')"
	@echo "  npm         Run npm command (usage: make npm cmd='run dev')"
	@echo "  fresh       Fresh install (build, migrate, seed)"
	@echo "  migrate     Run database migrations"
	@echo "  seed        Run database seeders"
	@echo "  seeds       Alias for seed command"
	@echo "  test        Run tests"
	@echo "  cache-clear Clear all Laravel caches"
	@echo "  setup       Initial setup for new install"

# Build containers
build:
	docker compose build

# Start containers
up:
	docker compose up -d

# Stop containers
down:
	docker compose down

# Restart containers
restart:
	docker compose restart

# View logs
logs:
	docker compose logs -f

# Shell into app container
shell:
	docker compose exec app bash

# Run artisan commands
artisan:
	docker compose exec app php artisan $(cmd)

# Run composer commands
composer:
	docker compose exec app composer $(cmd)

# Run npm commands
npm:
	docker compose exec app npm $(cmd)

# Fresh database
fresh:
	docker compose exec app php artisan migrate:fresh --seed

# Run migrations
migrate:
	docker compose exec app php artisan migrate

# Run seeders
seed:
	docker compose exec app php artisan db:seed

# Alias for seed
seeds: seed

# Run tests
test:
	docker compose exec app php artisan test

# Run tests with verbose output
test-verbose:
	docker compose exec app php artisan test --verbose

# Run specific test file
test-file:
	@if [ -z "$(file)" ]; then \
		echo "Usage: make test-file file=ResumeTest"; \
	else \
		docker compose exec app php artisan test --filter $(file); \
	fi

# Clear all caches
cache-clear:
	docker compose exec app php artisan optimize:clear

# Initial setup
setup:
	@echo "Setting up Laravel Resume Builder AI..."
	@cp docker.env.example .env 2>/dev/null || true
	docker compose build
	docker compose up -d
	@echo "Waiting for database to be ready..."
	@timeout=60; \
	while [ $$timeout -gt 0 ]; do \
		if docker compose exec -T db mysqladmin ping -h localhost -u root -psecret 2>/dev/null | grep -q "mysqld is alive"; then \
			echo "Database is ready!"; \
			break; \
		fi; \
		echo "Waiting for database... ($$timeout seconds remaining)"; \
		sleep 2; \
		timeout=$$((timeout - 2)); \
	done; \
	if [ $$timeout -le 0 ]; then \
		echo "Error: Database did not become ready in time"; \
		exit 1; \
	fi
	@echo "Generating application key..."
	@docker compose exec app php artisan key:generate
	@echo "Running migrations..."
	@docker compose exec app php artisan migrate
	@echo ""
	@echo "Setup complete! Application is running at http://localhost:8080"

