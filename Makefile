.DEFAULT_GOAL := help

DOCKER ?= docker
PROJECT ?= laravel-counters

PHP_VERSION ?= 8.5
LARAVEL_VERSION ?= ^13.0
PHPUNIT_VERSION ?= ^12.5.8

LARAVEL_11 := ^11.0
LARAVEL_12 := ^12.0
LARAVEL_13 := ^13.0

PHPUNIT_L11 := ^10.0
PHPUNIT_L12 := ^11.5.50
PHPUNIT_L13 := ^12.5.8

IMAGE = $(PROJECT)-test:php$(1)
VENDOR_VOLUME = $(PROJECT)_vendor_php$(1)_laravel$(2)

define run_pint
	$(DOCKER) run --rm \
		-v "$(CURDIR):/app" \
		-v $(PROJECT)_vendor_data:/app/vendor \
		-e LARAVEL_VERSION=$(LARAVEL_VERSION) \
		-e PHPUNIT_VERSION=$(PHPUNIT_VERSION) \
		-w /app \
		$(call IMAGE,$(PHP_VERSION)) \
		vendor/bin/pint $(1)
endef

define run_test
	@echo ==> PHP $(1) ^| Laravel $(2)
	$(DOCKER) build -f Dockerfile \
		--build-arg PHP_VERSION=$(1) \
		--build-arg LARAVEL_VERSION=$(3) \
		--build-arg PHPUNIT_VERSION=$(4) \
		-t $(call IMAGE,$(1)) .
	$(DOCKER) run --rm \
		-v "$(CURDIR):/app" \
		-v $(call VENDOR_VOLUME,$(1),$(2)):/app/vendor \
		-e LARAVEL_VERSION=$(3) \
		-e PHPUNIT_VERSION=$(4) \
		-w /app \
		$(call IMAGE,$(1)) \
		vendor/bin/phpunit
endef

.PHONY: help test test-all test-php84 test-php85 \
	test-php84-laravel11 test-php84-laravel12 test-php84-laravel13 \
	test-php85-laravel12 test-php85-laravel13 \
	pint pint-test build clean clean-volumes

help: ## Show available targets
	@echo Usage: make [target]
	@echo Targets:
	@echo   help                     Show this help
	@echo   test                     Run tests with default PHP/Laravel versions
	@echo   test-all                 Run tests for every supported PHP/Laravel combination
	@echo   test-php84               Run all Laravel versions on PHP 8.4
	@echo   test-php85               Run all Laravel versions on PHP 8.5
	@echo   test-php84-laravel11     PHP 8.4 + Laravel 11
	@echo   test-php84-laravel12     PHP 8.4 + Laravel 12
	@echo   test-php84-laravel13     PHP 8.4 + Laravel 13
	@echo   test-php85-laravel12     PHP 8.5 + Laravel 12
	@echo   test-php85-laravel13     PHP 8.5 + Laravel 13
	@echo   pint                     Fix code style with Laravel Pint
	@echo   pint-test                Check code style without making changes
	@echo   build                    Build the test image for selected versions
	@echo   clean-volumes            Remove cached vendor volumes
	@echo   clean                    Remove vendor volumes and test images
	@echo Defaults: PHP $(PHP_VERSION), Laravel $(LARAVEL_VERSION), PHPUnit $(PHPUNIT_VERSION)
	@echo Examples:
	@echo   make test
	@echo   make test-all
	@echo   make test-php84-laravel12
	@echo   make pint
	@echo   make pint-test
	@echo   make test PHP_VERSION=8.4 LARAVEL_VERSION=$(LARAVEL_12) PHPUNIT_VERSION=$(PHPUNIT_L12)

test: build ## Run tests with default PHP/Laravel versions
	$(DOCKER) run --rm \
		-v "$(CURDIR):/app" \
		-v $(PROJECT)_vendor_data:/app/vendor \
		-e LARAVEL_VERSION=$(LARAVEL_VERSION) \
		-e PHPUNIT_VERSION=$(PHPUNIT_VERSION) \
		-w /app \
		$(call IMAGE,$(PHP_VERSION)) \
		vendor/bin/phpunit

test-all: ## Run tests for every supported PHP/Laravel combination
	$(call run_test,8.4,11,$(LARAVEL_11),$(PHPUNIT_L11))
	$(call run_test,8.4,12,$(LARAVEL_12),$(PHPUNIT_L12))
	$(call run_test,8.4,13,$(LARAVEL_13),$(PHPUNIT_L13))
	$(call run_test,8.5,12,$(LARAVEL_12),$(PHPUNIT_L12))
	$(call run_test,8.5,13,$(LARAVEL_13),$(PHPUNIT_L13))
	@echo All matrix tests passed.

test-php84: test-php84-laravel11 test-php84-laravel12 test-php84-laravel13 ## Run all Laravel versions on PHP 8.4

test-php85: test-php85-laravel12 test-php85-laravel13 ## Run all Laravel versions on PHP 8.5

test-php84-laravel11: ## PHP 8.4 + Laravel 11
	$(call run_test,8.4,11,$(LARAVEL_11),$(PHPUNIT_L11))

test-php84-laravel12: ## PHP 8.4 + Laravel 12
	$(call run_test,8.4,12,$(LARAVEL_12),$(PHPUNIT_L12))

test-php84-laravel13: ## PHP 8.4 + Laravel 13
	$(call run_test,8.4,13,$(LARAVEL_13),$(PHPUNIT_L13))

test-php85-laravel12: ## PHP 8.5 + Laravel 12
	$(call run_test,8.5,12,$(LARAVEL_12),$(PHPUNIT_L12))

test-php85-laravel13: ## PHP 8.5 + Laravel 13
	$(call run_test,8.5,13,$(LARAVEL_13),$(PHPUNIT_L13))

pint: build ## Fix code style with Laravel Pint
	$(call run_pint,)

pint-test: build ## Check code style with Laravel Pint (dry run)
	$(call run_pint,--test)

build: ## Build the test image for the selected PHP/Laravel versions
	$(DOCKER) build -f Dockerfile \
		--build-arg PHP_VERSION=$(PHP_VERSION) \
		--build-arg LARAVEL_VERSION=$(LARAVEL_VERSION) \
		--build-arg PHPUNIT_VERSION=$(PHPUNIT_VERSION) \
		-t $(call IMAGE,$(PHP_VERSION)) .

clean-volumes: ## Remove cached vendor volumes for all matrix combinations
	-$(DOCKER) volume rm \
		$(call VENDOR_VOLUME,8.4,11) \
		$(call VENDOR_VOLUME,8.4,12) \
		$(call VENDOR_VOLUME,8.4,13) \
		$(call VENDOR_VOLUME,8.5,12) \
		$(call VENDOR_VOLUME,8.5,13) \
		$(PROJECT)_vendor_data
	@echo Vendor volumes removed.

clean: clean-volumes ## Remove vendor volumes and test images
	-$(DOCKER) rmi \
		$(call IMAGE,8.4) \
		$(call IMAGE,8.5)
	@echo Cleanup complete.
