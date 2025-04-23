FROM ghcr.io/roadrunner-server/roadrunner:2024.3 AS roadrunner
FROM ghcr.io/mlocati/php-extension-installer:2.7 AS php-extension-installer
FROM php:8.3-cli-alpine3.21

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr
COPY --from=php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/install-php-extensions

RUN install-php-extensions opcache intl bcmath sockets zip @composer

WORKDIR /usr/src/app

CMD rr serve -c .rr.dev.yaml --debug
