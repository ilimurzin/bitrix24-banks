FROM ghcr.io/roadrunner-server/roadrunner:2024.3 AS roadrunner
FROM php:8.3-cli-alpine3.21

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions opcache intl bcmath sockets zip

WORKDIR /usr/src/app

CMD rr serve -c .rr.dev.yaml --debug
