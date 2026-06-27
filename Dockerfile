ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libsqlite3-dev \
    && docker-php-ext-install \
        intl \
        pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./

ARG LARAVEL_VERSION=^13.0
ARG PHPUNIT_VERSION=^12.5.8

RUN composer config audit.block-insecure false \
    && composer require "laravel/framework:${LARAVEL_VERSION}" --dev --no-interaction --no-update \
    && composer require "phpunit/phpunit:${PHPUNIT_VERSION}" --dev --no-interaction --no-update \
    && (composer install --no-interaction --prefer-dist --no-scripts \
        || composer install --no-interaction --prefer-source --no-scripts)

COPY . .

COPY docker/entrypoint.sh /usr/local/bin/docker-test-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-test-entrypoint.sh && chmod +x /usr/local/bin/docker-test-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-test-entrypoint.sh"]
CMD ["vendor/bin/phpunit"]
