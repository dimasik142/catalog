.PHONY: help install up down restart shell logs migrate fresh seed test lint format phpstan quality clean build

# Default target
.DEFAULT_GOAL := help

# Docker Compose command
DC = docker-compose

# App container
APP = app

## help: Show this help message
help:
	@echo "Available targets:"
	@grep -E '^##' Makefile | sed 's/^## /  /'

## install: Full installation (setup Docker, install dependencies, migrate)
install: up
	@echo "Installing dependencies..."
	$(DC) exec $(APP) composer install
	@echo "Running migrations..."
	$(DC) exec $(APP) php artisan migrate
	@echo "Installation complete!"
	@echo "Access the application at http://localhost:8888"

## up: Start Docker containers
up:
	@echo "Starting Docker containers..."
	$(DC) up -d
	@echo "Containers started successfully!"

## down: Stop Docker containers
down:
	@echo "Stopping Docker containers..."
	$(DC) down
	@echo "Containers stopped!"

## restart: Restart Docker containers
restart: down up

## shell: Access the application container shell
shell:
	$(DC) exec $(APP) bash

## logs: Show container logs
logs:
	$(DC) logs -f $(APP)

## migrate: Run database migrations
migrate:
	@echo "Running migrations..."
	$(DC) exec $(APP) php artisan migrate

## fresh: Fresh database with migrations
fresh:
	@echo "Refreshing database..."
	$(DC) exec $(APP) php artisan migrate:fresh
	@echo "Database refreshed!"

## seed: Run database seeders
seed:
	@echo "Running seeders..."
	$(DC) exec $(APP) php artisan db:seed

## fresh-seed: Fresh database with migrations and seeds
fresh-seed:
	@echo "Refreshing database with seeds..."
	$(DC) exec $(APP) php artisan migrate:fresh --seed
	@echo "Database refreshed and seeded!"

## test: Run Pest tests
test:
	@echo "Running tests..."
	$(DC) exec $(APP) composer test

## lint: Check code style with Duster
lint:
	@echo "Checking code style..."
	$(DC) exec $(APP) composer lint

## format: Fix code style with Duster
format:
	@echo "Fixing code style..."
	$(DC) exec $(APP) composer format

## phpstan: Run PHPStan static analysis
phpstan:
	@echo "Running static analysis..."
	$(DC) exec $(APP) composer phpstan

## phpstan-baseline: Generate PHPStan baseline
phpstan-baseline:
	@echo "Generating PHPStan baseline..."
	$(DC) exec $(APP) composer phpstan:baseline

## quality: Run all quality checks (format, lint, phpstan, test)
quality:
	@echo "Running all quality checks..."
	@echo "\n==> Formatting code..."
	$(DC) exec $(APP) composer format
	@echo "\n==> Checking code style..."
	$(DC) exec $(APP) composer lint
	@echo "\n==> Running static analysis..."
	$(DC) exec $(APP) composer phpstan
	@echo "\n==> Running tests..."
	$(DC) exec $(APP) composer test
	@echo "\nAll quality checks complete!"

## clean: Clear all Laravel caches
clean:
	@echo "Clearing caches..."
	$(DC) exec $(APP) php artisan cache:clear
	$(DC) exec $(APP) php artisan config:clear
	$(DC) exec $(APP) php artisan route:clear
	$(DC) exec $(APP) php artisan view:clear
	@echo "Caches cleared!"

## optimize: Optimize Laravel for production
optimize:
	@echo "Optimizing application..."
	$(DC) exec $(APP) php artisan config:cache
	$(DC) exec $(APP) php artisan route:cache
	$(DC) exec $(APP) php artisan view:cache
	$(DC) exec $(APP) php artisan optimize
	@echo "Application optimized!"

## build: Rebuild Docker containers
build:
	@echo "Building Docker containers..."
	$(DC) build --no-cache
	@echo "Containers built!"

## ps: Show running containers
ps:
	$(DC) ps

## composer-install: Install Composer dependencies
composer-install:
	$(DC) exec $(APP) composer install

## composer-update: Update Composer dependencies
composer-update:
	$(DC) exec $(APP) composer update

## artisan: Run artisan command (use: make artisan cmd="your command")
artisan:
	$(DC) exec $(APP) php artisan $(cmd)

## module-make: Create new module (use: make module-make name="ModuleName")
module-make:
	$(DC) exec $(APP) php artisan module:make $(name)

## module-list: List all modules
module-list:
	$(DC) exec $(APP) php artisan module:list

## filament-user: Create Filament admin user
filament-user:
	$(DC) exec $(APP) php artisan make:filament-user

## dump-autoload: Regenerate Composer autoloader
dump-autoload:
	$(DC) exec $(APP) composer dump-autoload
