#!/bin/sh
set -e

resolve_phpunit_version() {
    case "${LARAVEL_VERSION}" in
        13*|^13*)
            echo "^12.5.8"
            ;;
        12*|^12*)
            echo "^11.5.50"
            ;;
        *)
            echo "^10.0"
            ;;
    esac
}

if [ ! -f vendor/autoload.php ]; then
    LARAVEL_VERSION="${LARAVEL_VERSION:-^13.0}"
    PHPUNIT_VERSION="${PHPUNIT_VERSION:-$(resolve_phpunit_version)}"

    composer config audit.block-insecure false
    composer require "laravel/framework:${LARAVEL_VERSION}" --dev --no-interaction --no-update
    composer require "phpunit/phpunit:${PHPUNIT_VERSION}" --dev --no-interaction --no-update
    composer install --no-interaction --prefer-dist --no-scripts \
        || composer install --no-interaction --prefer-source --no-scripts
fi

exec "$@"
