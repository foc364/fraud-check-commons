.DEFAULT_GOAL := help

args = $(filter-out $@,$(MAKECMDGOALS))

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

.SILENT: help
## Exibe as instruções de uso.
help:
	printf "${COLOR_COMMENT}Uso:${COLOR_RESET}\n"
	printf " make [comando]\n\n"
	printf "${COLOR_COMMENT}Comandos disponíveis:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Inicia a aplicação.
up:
	@echo : Iniciando a aplicação.
	@docker-compose up -d

# Instala dependências composer
install-dependencies:
	@docker-compose run --rm composer install

## restart na aplicação.
restart:
	@echo : restartando a aplicação.
	@docker-compose restart

## Desliga a aplicação.
down:
	@echo : Desligando a aplicação.
	docker-compose down

## Acessa o container da aplicação PHP via bash
ssh:
	@docker-compose exec app bash

## Builda a aplicação
install:
	@docker-compose build
	@make install-dependencies

## Atualiza o composer
composer-update:
	@echo : Atualizando composer
	@docker-compose run --rm composer update

## Atualiza o composer.lock
composer.lock:
	@echo : Atualizando composer lock
	docker-compose exec app composer update --lock

## pure access to composer
composer:
	@docker-compose run --rm composer $(args)

.PHONY: test
## Executa os testes da aplicação
test:
	@echo : Executando testes
	@docker-compose run --rm composer test

## Gera coverage
coverage:
	@echo : Executando testes coverage
	@docker-compose run --rm composer coverage

## Exibe os logs da aplicação.
logs:
	docker-compose logs -f -t