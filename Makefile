COMPOSER_CMD=composer
PHIVE_CMD=phive

PHPSPEC_CMD=tools/phpspec
README_TESTER_CMD=readme-tester
PHPSTAN_CMD=tools/phpstan
PHPCS_CMD=tools/phpcs

.DEFAULT_GOAL=all

DESCPARSER_GRAMMAR=descparser/src/Grammar.php

PHPEG_AVAILABLE:=$(shell command -v phpeg 2> /dev/null)

$(DESCPARSER_GRAMMAR): descparser/src/Grammar.peg composer.lock
ifndef PHPEG_AVAILABLE
    $(error "phpeg is not available, install scato/phpeg ver 1 to continue")
endif
	phpeg generate $<

.PHONY: all
all: build test

.PHONY: build
build: $(DESCPARSER_GRAMMAR)

.PHONY: clean
clean:
	rm -f $(DESCPARSER_GRAMMAR)
	rm -rf vendor
	rm -f composer.lock
	rm -rf tools

.PHONY: test
test: phpspec examples phpstan phpcs

.PHONY: phpspec
phpspec: composer.lock $(PHPSPEC_CMD)
	$(PHPSPEC_CMD) run

.PHONY: examples
examples: composer.lock
	$(README_TESTER_CMD) decisionmaker/README.md descparser/README.md matchmaker/README.md receiptanalyzer/README.md

.PHONY: phpstan
phpstan: composer.lock $(PHPSTAN_CMD)
	$(PHPSTAN_CMD) analyze -c phpstan.neon -l 7 decisionmaker/src descparser/src matchmaker/src receiptanalyzer/src

.PHONY: phpcs
phpcs: composer.lock $(PHPCS_CMD)
	$(PHPCS_CMD)

composer.lock: composer.json
	$(COMPOSER_CMD) install

$(PHPSPEC_CMD):
	$(PHIVE_CMD) install phpspec/phpspec:6 --force-accept-unsigned

$(PHPSTAN_CMD):
	$(PHIVE_CMD) install phpstan

$(PHPCS_CMD):
	$(PHIVE_CMD) install phpcs
