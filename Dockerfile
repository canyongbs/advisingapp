FROM ghcr.io/roadrunner-server/roadrunner:2023.3.12 AS roadrunner
FROM serversideup/php:8.2-fpm-nginx-v2.2.1 AS base

LABEL authors="CanyonGBS"
LABEL maintainer="CanyonGBS"

ARG POSTGRES_VERSION=15

RUN apt-get update \
    && apt-get install -y --no-install-recommends git s6 gnupg zip unzip php8.2-pgsql php8.2-imagick php8.2-redis php8.2-pcov php8.2-xdebug \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] https://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends postgresql-client-"$POSTGRES_VERSION" \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

ENV NVM_VERSION v0.39.7
ENV NODE_VERSION 21.6.0
ENV NVM_DIR /usr/local/nvm
RUN mkdir "$NVM_DIR"

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash

ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

RUN echo "source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default \
    && nvm install-latest-npm" | bash

COPY ./docker/s6-overlay/scripts/ /etc/s6-overlay/scripts/
COPY docker/s6-overlay/s6-rc.d/ /etc/s6-overlay/s6-rc.d/
COPY ./docker/s6-overlay/user/ /etc/s6-overlay/s6-rc.d/user/contents.d/
COPY ./docker/s6-overlay/templates/ /tmp/s6-overlay-templates

ARG TOTAL_QUEUE_WORKERS=3

COPY ./docker/generate-queues.sh /generate-queues.sh
RUN chmod +x /generate-queues.sh
RUN /generate-queues.sh
RUN rm /generate-queues.sh

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/site-opts.d /etc/nginx/site-opts.d

RUN rm /etc/s6-overlay/s6-rc.d/user/contents.d/php-fpm
RUN rm -rf /etc/s6-overlay/s6-rc.d/php-fpm

COPY --from=roadrunner /usr/bin/rr /var/www/html/rr
RUN chmod 0755 /var/www/html/rr

RUN apt-get update \
    && apt-get upgrade -y

FROM base AS development

# Fix permission issues in development by setting the "www-data"
# user to the same user and group that is running docker.
#ARG USER_ID
#ARG GROUP_ID
#RUN docker-php-serversideup-set-id www-data ${USER_ID} ${GROUP_ID}

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && if [[ -d /var/www/html/storage/logs ]] ; then \
    chgrp "$PGID" /var/www/html/storage/logs \
    && chmod g+s /var/www/html/storage/logs \
    ; fi

FROM base AS deploy

COPY --chown=$PUID:$PGID . /var/www/html

RUN npm ci --ignore-scripts \
    && rm -rf /var/www/html/vendor \
    && composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader --no-scripts \
    && npm run build \
    && npm ci --ignore-scripts --omit=dev

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chgrp "$PGID" /var/www/html/storage/logs \
    && chmod g+s /var/www/html/storage/logs
