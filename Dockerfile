FROM composer:2 AS deps

WORKDIR /srv

COPY ["./composer.json", "./composer.lock", "/srv/"]

RUN composer install --no-scripts --no-interaction

# Second stage: Set up the final application image
FROM php:8.3-cli as server

WORKDIR /srv

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    && docker-php-ext-install intl

COPY --from=deps /srv/vendor /srv/vendor
COPY . /srv

CMD ["php", "bin/console"]