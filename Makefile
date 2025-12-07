# Загрузить переменные из .env
ifneq (,$(wildcard .env))
    include .env
    export $(shell sed 's/=.*//' .env)
endif

init: api-clear docker-down-clear docker-pull docker-build docker-up api-init
up: docker-up
down: docker-down
restart: down up
lint: api-lint
fix: api-lint-fix
analyze: api-analyze
check: lint analyze test
test: api-test
test-unit: api-test-unit
test-unit-coverage: api-test-unit--coverage
test-functional: api-test-functional
test-functional-coverage: api-test-functional-coverage

update-deps: api-composer-update frontend-yarn-upgrade cucumber-yarn-upgrade restart

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

# api
api-clear: 
	docker run --rm -v ${PWD}/api:/app -w /app alpine:3.21 sh -c 'rm -rf var/*'

api-init: api-composer-install

api-composer-install:
	docker compose run --rm api-php-cli composer install

api-lint: 
	docker compose run --rm api-php-cli composer lint
	docker compose run --rm api-php-cli composer cs-check
	
api-lint-fix: 
	docker compose run --rm api-php-cli composer cs-fix
	
api-analyze: 
	docker compose run --rm api-php-cli composer psalm --show-info=true
	
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

# PRODUCTION build


# Makefile (Swarm deploy для приложения)
# Требуются переменные в .env: HOST, PORT, BUILD_NUMBER, REGISTRY, IMAGE_TAG
# Опционально для логина на сервере: REGISTRY_HOST, REGISTRY_USER, TOKEN
ifneq (,$(wildcard .env))
 include .env
 export $(shell sed 's/=.*//' .env)
endif

PORT ?= 22
STACK_NAME ?= site
REMOTE ?= deploy@$(HOST)
RELEASE_DIR ?= site_$(BUILD_NUMBER)
LINK_DIR ?= site
COMPOSE_SRC ?= docker-compose-production.yml
COMPOSE_DST ?= docker-compose.yml

# PRODUCTION build
build: build-frontend build-api

# makefile
build-frontend:
	docker --log-level=debug build --pull \
		--build-arg VITE_APP_AUTH_URL=https://api.rebit-pro.ru \
		--file=frontend/docker/production/nginx/Dockerfile \
		--tag=$(REGISTRY)/morephoto-frontend:$(IMAGE_TAG) frontend

build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=$(REGISTRY)/morephoto-api:$(IMAGE_TAG) api
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=$(REGISTRY)/morephoto-api-php-fpm:$(IMAGE_TAG) api
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=$(REGISTRY)/morephoto-api-php-cli:$(IMAGE_TAG) api

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

# PRODUCTION push
push: push-frontend push-api

push-frontend:
	docker push $(REGISTRY)/morephoto-frontend:$(IMAGE_TAG)

push-api:
	docker push $(REGISTRY)/morephoto-api:$(IMAGE_TAG)
	docker push $(REGISTRY)/morephoto-api-php-fpm:$(IMAGE_TAG)
	docker push $(REGISTRY)/morephoto-api-php-cli:$(IMAGE_TAG)

# Swarm deploy
# Makefile
deploy:
	ssh $(REMOTE) -p $(PORT)  'docker network create --driver=overlay traefik-public || true'
	ssh $(REMOTE) -p $(PORT)  'rm -rf site_${BUILD_NUMBER} && mkdir site_${BUILD_NUMBER}'

	ssh $(REMOTE) -p $(PORT) 'mkdir -p $(RELEASE_DIR)'
	scp -P $(PORT) $(COMPOSE_SRC) $(REMOTE):$(RELEASE_DIR)/$(COMPOSE_DST)
	ssh $(REMOTE) -p $(PORT) 'cd $(RELEASE_DIR) && printf "REGISTRY=%s\nIMAGE_TAG=%s\n" "$(REGISTRY)" "$(IMAGE_TAG)" > .env'
	@if [ -n "$(REGISTRY_HOST)" ] && [ -n "$(REGISTRY_USER)" ] && [ -n "$(TOKEN_GIT_HUB)" ]; then \
		ssh $(REMOTE) -p $(PORT) 'echo "$(TOKEN_GIT_HUB)" | docker login $(REGISTRY_HOST) -u $(REGISTRY_USER) --password-stdin'; \
	fi
	ssh $(REMOTE) -p $(PORT) 'ln -sfn $(RELEASE_DIR) $(LINK_DIR)'
	ssh $(REMOTE) -p $(PORT) 'cd $(LINK_DIR) && REGISTRY="$(REGISTRY)" IMAGE_TAG="$(IMAGE_TAG)" docker stack deploy --with-registry-auth --prune --resolve-image=always -c $(COMPOSE_DST) $(STACK_NAME)'
	#ssh $(REMOTE) -p $(PORT) 'cd $(LINK_DIR) && REGISTRY="$(REGISTRY)" IMAGE_TAG="$(IMAGE_TAG)"  docker stack deploy --compose-file docker-compose.yml $(STACK_NAME) --with-registry-auth --prune'

# Rollback на указанный билд: make rollback ROLLBACK_BUILD_NUMBER=123
rollback:
	@if [ -z "$(ROLLBACK_BUILD_NUMBER)" ]; then echo "Set ROLLBACK_BUILD_NUMBER"; exit 1; fi
	ssh $(REMOTE) -p $(PORT) 'test -d site_$(ROLLBACK_BUILD_NUMBER)'
	ssh $(REMOTE) -p $(PORT) 'ln -sfn site_$(ROLLBACK_BUILD_NUMBER) $(LINK_DIR)'
	ssh $(REMOTE) -p $(PORT) 'cd $(LINK_DIR) && docker stack deploy --with-registry-auth --prune --resolve-image=always -c $(COMPOSE_DST) $(STACK_NAME)'

php-cli:
	docker compose run --rm api-php-cli bash

php-fpm:
	docker compose exec api-php-fpm bash

tunnel:
	ssh -L 0.0.0.0:3306:10.128.0.5:3306 bitrix@dev12.orteka.ru -N

test-benchmark:
	docker compose run --rm benchmark ab -n 1 -c 1 -k https://orteka.loc/
	docker compose run --rm benchmark ab -n 100 -c 10 -k https://orteka.loc/
