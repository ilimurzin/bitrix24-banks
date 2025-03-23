FROM ghcr.io/roadrunner-server/roadrunner:2024.3 AS roadrunner
FROM php:8.3-cli-alpine3.21

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

WORKDIR /usr/src/app

CMD rr serve -c .rr.dev.yaml --debug
