# Makefile for Attendance Project (Dockerized Laravel)

# Container names
APP_CONTAINER = app
DB_CONTAINER  = mariadb

# ========================
# Laravel Shortcuts
# ========================

# Run artisan commands
artisan:
	docker-compose exec $(APP_CONTAINER) php artisan $(cmd)

# Run composer commands
composer:
	docker-compose exec $(APP_CONTAINER) composer $(cmd)

# Open bash inside the app container
bash:
	docker-compose exec $(APP_CONTAINER) bash

# Clear caches & optimize Laravel
optimize:
	docker-compose exec $(APP_CONTAINER) php artisan optimize:clear && \
	docker-compose exec $(APP_CONTAINER) php artisan config:cache && \
	docker-compose exec $(APP_CONTAINER) php artisan route:cache && \
	docker-compose exec $(APP_CONTAINER) php artisan view:cache

# Run migrations
migrate:
	docker-compose exec $(APP_CONTAINER) php artisan migrate

# Run database seeders
seed:
	docker-compose exec $(APP_CONTAINER) php artisan db:seed

# Run queue worker
queue:
	docker-compose exec $(APP_CONTAINER) php artisan queue:work

# Generate new APP_KEY
keygen:
	docker-compose exec $(APP_CONTAINER) php artisan key:generate

# Refresh database (drop all, migrate, seed)
refresh:
	docker-compose exec $(APP_CONTAINER) php artisan migrate:fresh --seed

# ========================
# Docker Management
# ========================

# Restart all containers
restart:
	docker-compose down && docker-compose up -d

# Build and start fresh
build:
	docker-compose up -d --build

# Stop containers
stop:
	docker-compose down

# ========================
# Database
# ========================

# Login to MariaDB CLI inside container
dblogin:
	docker-compose exec $(DB_CONTAINER) mysql -u bn_laravel -p'laravel123' c_attendance

# Open bash inside mariadb container
dbbash:
	docker-compose exec $(DB_CONTAINER) bash

# ========================
# PhpMyAdmin
# ========================

# Open phpMyAdmin in the default browser
pma:
	@echo "Opening phpMyAdmin at http://localhost:8080"
	@if which xdg-open > /dev/null; then xdg-open http://localhost:8080; \
	elif which open > /dev/null; then open http://localhost:8080; \
	else echo "Please open http://localhost:8080 manually"; fi

# ========================
# Start Project (All-in-One)
# ========================

start:
	@echo "=== Building and starting containers ==="
	docker-compose up -d --build
	@echo "=== Waiting for MariaDB to be ready (10 seconds) ==="
	@sleep 10
	@echo "=== Installing composer dependencies if missing ==="
	@if [ ! -d ./vendor ]; then \
		docker-compose exec $(APP_CONTAINER) composer install; \
	fi
	@echo "=== Generating APP_KEY if missing ==="
	docker-compose exec $(APP_CONTAINER) php artisan key:generate
	@echo "=== Running migrations and seeders ==="
	docker-compose exec $(APP_CONTAINER) php artisan migrate --seed
	@echo "=== Serving Laravel app ==="
	@docker-compose exec -d $(APP_CONTAINER) php artisan serve --host=0.0.0.0 --port=8000
	@echo "=== Opening phpMyAdmin ==="
	@if which xdg-open > /dev/null; then xdg-open http://localhost:8080; \
	elif which open > /dev/null; then open http://localhost:8080; \
	else echo "Please open http://localhost:8080 manually"; fi
