FROM serversideup/php:8.2-fpm-nginx-v2.2.1 AS base

LABEL authors="CanyonGBS"
LABEL maintainer="CanyonGBS"

ARG POSTGRES_VERSION=15

RUN apt-get update \
    && apt-get install -y --no-install-recommends git gnupg zip unzip php8.2-pgsql php8.2-imagick php8.2-redis php8.2-pcov php8.2-xdebug \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] https://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends postgresql-client-"$POSTGRES_VERSION" \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

ARG NODE_VERSION=21.6.2

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash \
    && export NVM_DIR="$HOME/.nvm" && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" \
    && nvm install "$NODE_VERSION" \
    && nvm alias default "$NODE_VERSION" \
    && nvm use default \
    && nvm install-latest-npm \
    && npm install -g ip@2.0.1

ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

COPY ./docker/s6-overlay/scripts/ /etc/s6-overlay/scripts/
COPY docker/s6-overlay/s6-rc.d/ /etc/s6-overlay/s6-rc.d/
COPY ./docker/s6-overlay/user/ /etc/s6-overlay/s6-rc.d/user/contents.d/

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/site-opts.d /etc/nginx/site-opts.d

RUN rm /etc/s6-overlay/s6-rc.d/user/contents.d/php-fpm
RUN rm -rf /etc/s6-overlay/s6-rc.d/php-fpm

RUN apt-get update \
    && apt-get upgrade -y

FROM base AS development

# Fix permission issues in development by setting the "www-data"
# user to the same user and group that is running docker.
#ARG USER_ID
#ARG GROUP_ID
#RUN docker-php-serversideup-set-id www-data ${USER_ID} ${GROUP_ID}

FROM base AS deploy

COPY --chown=$PUID:$PGID . /var/www/html
