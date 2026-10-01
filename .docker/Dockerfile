ARG PHP_VERSION=8.3
FROM dunglas/frankenphp:php${PHP_VERSION}

RUN install-php-extensions \
    intl \
    gd \
    mysqli \
    pdo_mysql \
    zip \
    bcmath \
    exif \
    pcov

RUN echo 'pcov.directory=/module/src' > /usr/local/etc/php/conf.d/99-pcov.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY app/composer.json /app/composer.json

COPY app/phpunit.xml.dist /app/phpunit.xml.dist
COPY app/phpstan.neon.dist /app/phpstan.neon.dist
COPY app/rector.php /app/rector.php
COPY app/infection.json5 /app/infection.json5

COPY Caddyfile /etc/caddy/Caddyfile
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
