ARG ALPINE_VERSION=3.16

FROM php:7.4-cli-alpine$ALPINE_VERSION AS php-cli

RUN apk add php git make perl icu-dev --no-cache 
RUN docker-php-ext-install intl

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json ./
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --prefer-dist --no-progress --no-interaction --no-plugins --ignore-platform-reqs

COPY dev/docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php"]
