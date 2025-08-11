### ——————————————————————————————————————————————————————————————————
### —— Local Makefile
### ——————————————————————————————————————————————————————————————————

include vendor/badpixxel/php-sdk/make/sdk.mk

COMMAND ?= echo "Aucune commande spécifiée"
COLOR_CYAN := $(shell tput setaf 6)
COLOR_RESET := $(shell tput sgr0)

start: 	## Execute Functional Test
	symfony serve --no-tls

.PHONY: upgrade
upgrade:
	$(MAKE) up
	$(MAKE) all COMMAND="composer update -q"

.PHONY: verify
verify:	# Verify Code in All Containers
	$(MAKE) up
	$(MAKE) all COMMAND="composer update -q"
	$(MAKE) all COMMAND="php vendor/bin/grumphp run --testsuite=travis"
	$(MAKE) all COMMAND="php vendor/bin/grumphp run --testsuite=csfixer"
	$(MAKE) all COMMAND="php vendor/bin/grumphp run --testsuite=phpstan"
	$(MAKE) all COMMAND="php vendor/bin/phpunit"

.PHONY: phpstan
phpstan:	# Execute Php Stan in All Containers
	$(MAKE) all COMMAND="php vendor/bin/grumphp run --testsuite=phpstan"

.PHONY: test
test: 	## Execute Functional Test in All Containers
	$(MAKE) up
	$(MAKE) all COMMAND="php vendor/bin/phpunit --testdox"

.PHONY: all
all: # Execute a Command in All Containers
	@$(foreach service,$(shell docker compose config --services | sort), \
		set -e; \
		echo "$(COLOR_CYAN) >> Executing '$(COMMAND)' in container: $(service) $(COLOR_RESET)"; \
		docker compose exec $(service) bash -c "$(COMMAND)"; \
	)