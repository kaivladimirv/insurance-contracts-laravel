export WWWGROUP := $(shell id -g)

init: init-env docker-down-clear docker-build docker-up composer-install project-init run-queue-workers
up: docker-up run-queue-workers
down: docker-down
restart: down up
project-init: generate-project-key create-db migrate

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-build:
	docker compose build

init-env:
	[ -f .env ] || cp .env.example .env
	sed -i '' 's/^DB_HOST=.*/DB_HOST=postgres/' .env
	sed -i '' 's/^RABBITMQ_HOST=.*/RABBITMQ_HOST=rabbitmq/' .env
	sed -i '' 's/^MAIL_MAILER=.*/MAIL_MAILER=smtp/' .env
	sed -i '' 's/^MAIL_HOST=.*/MAIL_HOST=mailpit/' .env
	sed -i '' 's/^MAIL_PORT=.*/MAIL_PORT=1025/' .env

generate-project-key:
	vendor/bin/sail artisan key:generate

create-db:
	vendor/bin/sail artisan db:create

migrate:
	vendor/bin/sail artisan migrate --force

run-queue-workers:
	vendor/bin/sail composer run-script run-queue-workers

composer-install:
	docker compose exec laravel.test composer install

composer-validate:
	vendor/bin/sail composer validate

composer-outdated:
	vendor/bin/sail composer outdated --direct --major-only --strict

composer-unused:
	vendor/bin/sail exec laravel.test ./vendor/bin/composer-unused

composer-audit:
	vendor/bin/sail composer audit

composer-check-platform-reqs:
	vendor/bin/sail composer check-platform-reqs

lint:
	vendor/bin/sail composer exec --verbose phpcs -- --standard=phpcs.xml

test:
	vendor/bin/sail composer run-script test

test-with-db:
	vendor/bin/sail composer run-script test-with-db

test-coverage-clover:
	vendor/bin/sail composer exec --verbose XDEBUG_MODE=coverage phpunit tests -- -c phpunit.db.xml --coverage-clover storage/coverage/clover/clover.xml

test-coverage-html:
	vendor/bin/sail composer exec --verbose XDEBUG_MODE=coverage phpunit tests -- -c phpunit.db.xml --coverage-html storage/coverage/html

generate-docs:
	vendor/bin/sail artisan l5-swagger:generate

clear-all-logs:
	vendor/bin/sail artisan log:clear

semgrep-offline:
	docker pull semgrep/semgrep:latest
	docker run --rm -v "${PWD}:/src" semgrep/semgrep semgrep scan --config auto --severity ERROR --use-git-ignore --error

psalm:
	vendor/bin/sail exec laravel.test ./vendor/bin/psalm --threads=8

clear-all-cache:
	vendor/bin/sail artisan cache:clear
	vendor/bin/sail artisan route:clear
	vendor/bin/sail artisan config:clear
	vendor/bin/sail artisan view:clear

mutation-coverage-report-html:
	vendor/bin/sail exec laravel.test ./vendor/bin/infection --logger-html='storage/coverage/mutation-report.html'
