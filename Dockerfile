FROM ghcr.io/roadrunner-server/roadrunner:2024.2.1 AS roadrunner
FROM serversideup/php:8.2-fpm-nginx-v2.2.1 AS base

LABEL authors="CanyonGBS"
LABEL maintainer="CanyonGBS"

ARG POSTGRES_VERSION=15

RUN apt-get update \
    && apt-get install -y --no-install-recommends git gnupg php8.2-imagick php8.2-pcov php8.2-pgsql php8.2-redis php8.2-xdebug s6 unzip zip \
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

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/site-opts.d /etc/nginx/site-opts.d

RUN rm /etc/s6-overlay/s6-rc.d/user/contents.d/php-fpm
RUN rm -rf /etc/s6-overlay/s6-rc.d/php-fpm

COPY --from=roadrunner --chown=$PUID:$PGID --chmod=0755 /usr/bin/rr /usr/local/bin/rr

RUN apt-get update \
    && apt-get upgrade -y

FROM base AS development

# Fix permission issues in development by setting the "webuser"
# user to the same user and group that is running docker.
COPY ./docker/set-id /set-id

ARG USER_ID
ARG GROUP_ID
RUN set-id webuser ${USER_ID} ${GROUP_ID} ; \
    rm /set-id

ARG MULTIPLE_DEVELOPMENT_QUEUES=false

RUN if [[ -z "$MULTIPLE_DEVELOPMENT_QUEUES" ]] ; then \
    /generate-queues.sh "default" "\$SQS_QUEUE" \
    && /generate-queues.sh "landlord" "\$LANDLORD_SQS_QUEUE" \
    && /generate-queues.sh "outbound-communication" "\$OUTBOUND_COMMUNICATION_QUEUE" \
    && /generate-queues.sh "audit" "\$AUDIT_QUEUE_QUEUE" \
    && /generate-queues.sh "meeting-center" "\$MEETING_CENTER_QUEUE" \
    && /generate-queues.sh "import-export" "\$IMPORT_EXPORT_QUEUE" \
    ; else \
    /generate-queues.sh "default" "\$SQS_QUEUE" \
    ; fi

RUN rm /generate-queues.sh

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chmod g+s -R /var/www/html

FROM base AS deploy

RUN /generate-queues.sh "default" "\$SQS_QUEUE" \
    && /generate-queues.sh "landlord" "\$LANDLORD_SQS_QUEUE" \
    && /generate-queues.sh "outbound-communication" "\$OUTBOUND_COMMUNICATION_QUEUE" \
    && /generate-queues.sh "audit" "\$AUDIT_QUEUE_QUEUE" \
    && /generate-queues.sh "meeting-center" "\$MEETING_CENTER_QUEUE" \
    && /generate-queues.sh "import-export" "\$IMPORT_EXPORT_QUEUE" 

RUN rm /generate-queues.sh

COPY --chown=$PUID:$PGID . /var/www/html

RUN npm ci --ignore-scripts \
    && rm -rf /var/www/html/vendor \
    && composer install --no-dev --no-interaction --no-progress --no-suggest --classmap-authoritative \
    && npm run build \
    && npm ci --ignore-scripts --omit=dev

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chgrp "$PGID" /var/www/html/storage/logs \
    && chmod g+s /var/www/html/storage/logs \
    && find /var/www/html -type d -print0 | xargs -0 chmod 755 \
    && find /var/www/html \( -path /var/www/html/docker -o -path /var/www/html/node_modules -o -path /var/www/html/vendor \) -prune -o -type f -print0 | xargs -0 chmod 644 \
    && chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache
