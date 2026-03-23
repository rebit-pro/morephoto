# Загрузить переменные из .env
ifneq (,$(wildcard .env))
    include .env
    export $(shell sed 's/=.*//' .env)
endif

init: api-clear docker-down-clear docker-pull docker-build docker-up
up: docker-up
down: docker-down
restart: down up
lint: api-lint
fix: api-cs-fix
analyze: api-analyze
check: api-cs-check lint analyze
test: api-test
test-unit: api-test-unit
test-unit-coverage: api-test-unit--coverage
test-functional: api-test-functional
test-functional-coverage: api-test-functional-coverage

update-deps: api-composer-update frontend-yarn-upgrade cucumber-yarn-upgrade restart
migrate: api-migrate

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

# frontend
frontend-init: frontend-npm-install

frontend-npm-install:
	docker compose run --rm frontend-node-cli npm install

# api
api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine:3.21 sh -c 'rm -rf var/*'

api-init: api-composer-install

api-composer-install:
	docker compose run --rm api-php-cli composer install

api-lint:
	docker compose run --rm api-php-cli composer lint

api-cs-check:
	docker compose run --rm api-php-cli composer cs-check

api-cs-fix:
	docker compose run --rm api-php-cli composer cs-fix

api-analyze:
	docker compose run --rm api-php-cli composer phpstan

api-analyze-baseline:
	docker compose run --rm api-php-cli php vendor/bin/phpstan analyse --configuration=phpstan.neon --generate-baseline=phpstan-baseline.neon

api-test:
	docker compose run --rm api-php-cli composer test

api-test-unit:
	docker compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-unit-coverage:
	docker compose run --rm api-php-cli composer test-coverage -- --testsuite=unit

api-test-functional:
	docker compose run --rm api-php-cli composer test -- --testsuite=functional

api-test-functional-coverage:
	docker compose run --rm api-php-cli composer test-coverage -- --testsuite=functional

api-cli:
	docker compose run --rm api-php-cli composer app

MODULE_NAME ?= rebit.wallet

annotate:
	docker compose run --rm api-php-cli sh -c "cd /app/public && php bitrix/bitrix.php orm:annotate -c -m $(MODULE_NAME) local/modules/$(MODULE_NAME)/orm_annotation.php"

api-migrate:
	docker compose run --rm api-php-cli php /app/public/local/modules/sprint.migration/tools/migrate.php up

api-migrate-status:
	docker compose run --rm api-php-cli php /app/public/local/modules/sprint.migration/tools/migrate.php ls

# ==============================================================================
# PRODUCTION
# ==============================================================================

PORT ?= 22
DEPLOY_USER ?= deploy
STACK_NAME ?= site
REMOTE ?= $(DEPLOY_USER)@$(HOST)
RELEASE_DIR ?= site_$(BUILD_NUMBER)
LINK_DIR ?= site
COMPOSE_SRC ?= docker-compose-production.yml
COMPOSE_DST ?= docker-compose.yml
VITE_API_URL ?= https://api.rebit-pro.ru
KEEP_RELEASES ?= 2
BITRIX_HOST_DIR ?= /srv/rebit-p2p/bitrix
LOGS_HOST_DIR ?= /srv/rebit-p2p/logs

# --- Build ---
# Собирает Docker-образы для production
#
# Требуемые переменные: REGISTRY, IMAGE_TAG, VITE_API_URL
#
# Пример:
#   REGISTRY=ghcr.io/rebit-pro IMAGE_TAG=abc12345 make build
build: build-frontend build-api

build-frontend:
	docker --log-level=debug build --pull --load \
		--build-arg VITE_API_URL=$(VITE_API_URL) \
		--file=frontend/docker/production/nginx/Dockerfile \
		--tag=$(REGISTRY)/rebit-p2p-frontend:$(IMAGE_TAG) frontend

build-api:
	docker --log-level=debug build --pull --load --file=api/docker/production/nginx/Dockerfile --tag=$(REGISTRY)/rebit-p2p-api:$(IMAGE_TAG) api
	docker --log-level=debug build --pull --load --file=api/docker/production/php-fpm/Dockerfile --tag=$(REGISTRY)/rebit-p2p-api-php-fpm:$(IMAGE_TAG) api
	docker --log-level=debug build --pull --load --file=api/docker/production/php-cli/Dockerfile --tag=$(REGISTRY)/rebit-p2p-api-php-cli:$(IMAGE_TAG) api

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

# --- Push ---
# Пушит собранные образы в Docker-реестр
#
# Требуемые переменные: REGISTRY, IMAGE_TAG
#
# Пример:
#   REGISTRY=ghcr.io/rebit-pro IMAGE_TAG=abc12345 make push
push: push-frontend push-api

push-frontend:
	docker push $(REGISTRY)/rebit-p2p-frontend:$(IMAGE_TAG)

push-api:
	docker push $(REGISTRY)/rebit-p2p-api:$(IMAGE_TAG)
	docker push $(REGISTRY)/rebit-p2p-api-php-fpm:$(IMAGE_TAG)
	docker push $(REGISTRY)/rebit-p2p-api-php-cli:$(IMAGE_TAG)

# --- Deploy ---
# Деплоит приложение в Docker Swarm на удалённый сервер.
# После деплоя удаляет старые релизы (оставляет KEEP_RELEASES=2) и неиспользуемые Docker-образы.
#
# Требуемые переменные:
#   HOST, PORT, DEPLOY_USER, BUILD_NUMBER, REGISTRY, IMAGE_TAG,
#   MYSQL_PASSWORD, MYSQL_ROOT_PASSWORD, APP_DEBUG, APP_ENV
# Опционально (для docker login на сервере):
#   REGISTRY_HOST, REGISTRY_USER, TOKEN_GIT_HUB
#
# Пример:
#   HOST=1.2.3.4 PORT=22 DEPLOY_USER=deploy BUILD_NUMBER=42 \
#   REGISTRY=ghcr.io/rebit-pro IMAGE_TAG=abc12345 \
#   MYSQL_PASSWORD=secret MYSQL_ROOT_PASSWORD=rootsecret \
#   APP_DEBUG=0 APP_ENV=production \
#   REGISTRY_HOST=ghcr.io REGISTRY_USER=user TOKEN_GIT_HUB=ghp_xxx \
#   make deploy
deploy:
	scp -P $(PORT) $(COMPOSE_SRC) api/deploy/bitrix-settings-extra.php $(REMOTE):~/
	ssh $(REMOTE) -p $(PORT) ' \
		docker network create --driver=overlay traefik-public 2>/dev/null || true \
		&& rm -rf $(RELEASE_DIR) && mkdir $(RELEASE_DIR) \
		&& mv ~/docker-compose-production.yml $(RELEASE_DIR)/$(COMPOSE_DST) \
		&& mkdir -p $(BITRIX_HOST_DIR) \
		&& mv ~/bitrix-settings-extra.php $(BITRIX_HOST_DIR)/.settings_extra.php \
		&& mkdir -p $(LOGS_HOST_DIR)/logstash \
		&& cd $(RELEASE_DIR) \
		&& printf "REGISTRY=%s\nIMAGE_TAG=%s\nMYSQL_PASSWORD=%s\nMYSQL_ROOT_PASSWORD=%s\nAPP_DEBUG=%s\nAPP_ENV=%s\n" \
			"$(REGISTRY)" "$(IMAGE_TAG)" "$(MYSQL_PASSWORD)" "$(MYSQL_ROOT_PASSWORD)" "$(APP_DEBUG)" "$(APP_ENV)" > .env \
		&& cd ~ && ln -sfn $(RELEASE_DIR) $(LINK_DIR) \
		&& if [ -n "$(TOKEN_GIT_HUB)" ]; then echo "$(TOKEN_GIT_HUB)" | docker login $(REGISTRY_HOST) -u $(REGISTRY_USER) --password-stdin; fi \
		&& docker pull $(REGISTRY)/rebit-p2p-frontend:$(IMAGE_TAG) \
		&& docker pull $(REGISTRY)/rebit-p2p-api:$(IMAGE_TAG) \
		&& docker pull $(REGISTRY)/rebit-p2p-api-php-fpm:$(IMAGE_TAG) \
		&& docker pull $(REGISTRY)/rebit-p2p-api-php-cli:$(IMAGE_TAG) \
		&& cd $(LINK_DIR) && set -a && . ./.env && set +a \
		&& docker stack deploy --with-registry-auth --prune --resolve-image=never -c $(COMPOSE_DST) $(STACK_NAME)'
	ssh $(REMOTE) -p $(PORT) ' \
		cd ~ && ls -d site_* 2>/dev/null | sort -t_ -k2 -n | head -n -$(KEEP_RELEASES) | xargs -r rm -rf \
		&& docker image prune --all --force'
#	@echo "Waiting for services to start..."
#	sleep 15
#	$(MAKE) api-migrate-deploy

# --- Migrate (production) ---
api-migrate-deploy:
	ssh $(REMOTE) -p $(PORT) 'docker run --rm \
		-v /srv/rebit-p2p/bitrix:/app/public/bitrix \
		-v /srv/rebit-p2p/upload:/app/public/upload \
		--network $(STACK_NAME)_default \
		$(REGISTRY)/rebit-p2p-api-php-cli:$(IMAGE_TAG) \
		php /app/public/local/modules/sprint.migration/tools/migrate.php up'

# --- Rollback ---
# Откатывает на указанный билд
#
# Требуемые переменные: HOST, PORT, ROLLBACK_BUILD_NUMBER
#
# Пример:
#   HOST=1.2.3.4 ROLLBACK_BUILD_NUMBER=41 make rollback
rollback:
	@if [ -z "$(ROLLBACK_BUILD_NUMBER)" ]; then echo "Set ROLLBACK_BUILD_NUMBER"; exit 1; fi
	ssh $(REMOTE) -p $(PORT) 'test -d site_$(ROLLBACK_BUILD_NUMBER)'
	ssh $(REMOTE) -p $(PORT) 'ln -sfn site_$(ROLLBACK_BUILD_NUMBER) $(LINK_DIR)'
	ssh $(REMOTE) -p $(PORT) 'cd $(LINK_DIR) && set -a && . ./.env && set +a && docker stack deploy --with-registry-auth --prune --resolve-image=never -c $(COMPOSE_DST) $(STACK_NAME)'

php-cli:
	docker compose run --rm api-php-cli bash

php-fpm:
	docker compose exec api-php-fpm bash

tunnel:
	ssh -L 0.0.0.0:3306:10.128.0.5:3306 bitrix@dev12.orteka.ru -N

test-benchmark:
	docker compose run --rm benchmark ab -n 1 -c 1 -k https://orteka.loc/
	docker compose run --rm benchmark ab -n 100 -c 10 -k https://orteka.loc/
