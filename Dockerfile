FROM php:8.2-fpm-alpine3.18

ENV DATABASE_URL=pgsql://postgres:postgres@db:5432/postgres
ENV APP_ENV=dev
ENV APP_SECRET=
ENV ADAPTER_USERNAME=
ENV ADAPTER_PASSWORDHASH=

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

EXPOSE 9000

CMD ["php-fpm"]
