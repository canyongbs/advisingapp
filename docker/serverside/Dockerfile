FROM serversideup/php:8.2-fpm-nginx-v2.2.1 AS base

LABEL authors="CanyonGBS"
LABEL maintainer="CanyonGBS"

RUN apt-get update \
    && apt-get install -y --no-install-recommends zip unzip php8.2-pgsql php8.2-imagick php8.2-redis php8.2-pcov php8.2-xdebug \
    && apt-get clean \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

FROM base AS development

# Fix permission issues in development by setting the "www-data"
# user to the same user and group that is running docker.
ARG USER_ID
ARG GROUP_ID
RUN docker-php-serversideup-set-id www-data ${USER_ID} ${GROUP_ID}

FROM base AS deploy
COPY --chown=www-data:www-data . /var/www/html