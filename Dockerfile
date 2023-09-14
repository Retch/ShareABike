FROM php:8.2-fpm-alpine3.18
LABEL org.opencontainers.image.source="https://github.com/retch/shareabike"

ENV DATABASE_URL=pgsql://postgres:postgres@db:5432/postgres
ENV APP_ENV=dev
ENV APP_SECRET=
ENV ADAPTER_USERNAME=
ENV ADAPTER_PASSWORDHASH=
ENV JWT_TTL=300
ENV JWT_REFRESH_COOKIE_SECURE=true
ENV JWT_REFRESH_TOKEN_TTL=2592000
ENV JWT_REFRESH_COOKIE_DOMAIN=
ENV OMNI_ADAPTER_URL=http://omni-adapter:8079
ENV OMNI_LOCK_RING_AMOUNT=8

WORKDIR /app

RUN apk update && apk add --no-cache \
    postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY bin/ ./bin/
COPY public/ ./public/
COPY config/ ./config/
COPY migrations/ ./migrations/
COPY src/ ./src/
COPY .env composer.lock composer.json symfony.lock ./

RUN composer install --no-interaction

RUN php bin/console cache:clear

EXPOSE 9000

CMD ["php bin/console cache:clear && php-fpm"]
