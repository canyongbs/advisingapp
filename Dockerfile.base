ARG BASE_IMAGE="public.ecr.aws/lts/ubuntu:24.04"

FROM ${BASE_IMAGE} AS setup

ARG S6_DIR=/opt/s6/
ARG S6_SRC_DEP="xz-utils wget"
ARG S6_SRC_URL="https://github.com/just-containers/s6-overlay/releases/download"
# TODO: Update this to the latest version
ARG S6_VERSION="v3.1.5.0"
ARG OTHER_DEP="gnupg ca-certificates software-properties-common"

ENV DEBIAN_FRONTEND="noninteractive" \
    S6_KEEP_ENV=1

RUN mkdir -p $S6_DIR; \
    apt-get update; \
    apt-get install -yq ${S6_SRC_DEP} ${OTHER_DEP} --no-install-recommends --no-install-suggests; \
    export SYS_ARCH=$(uname -m); \
    case "$SYS_ARCH" in \
    aarch64 ) export S6_ARCH='aarch64' ;; \
    arm64   ) export S6_ARCH='aarch64' ;; \
    armhf   ) export S6_ARCH='armhf'   ;; \
    arm*    ) export S6_ARCH='arm'     ;; \
    i4*     ) export S6_ARCH='i486'    ;; \
    i6*     ) export S6_ARCH='i686'    ;; \
    s390*   ) export S6_ARCH='s390x'   ;; \
    *       ) export S6_ARCH='x86_64'  ;; \
    esac; \
    untar (){ \
    wget --no-check-certificate -O- $1 | tar Jxp -C $S6_DIR; \
    }; \
    \
    untar ${S6_SRC_URL}/${S6_VERSION}/s6-overlay-noarch.tar.xz \
    && untar ${S6_SRC_URL}/${S6_VERSION}/s6-overlay-${S6_ARCH}.tar.xz \
    && add-apt-repository -y ppa:ondrej/php

FROM ${BASE_IMAGE} AS base

LABEL authors="Canyon GBS"
LABEL maintainer="Canyon GBS"

ARG PHP_VERSION='8.4'
ARG POSTGRES_VERSION=15

ENV BUILD_PHP_VERSION=$PHP_VERSION \
    DEBIAN_FRONTEND=noninteractive \
    LOG_OUTPUT_LEVEL=warn \
    S6_BEHAVIOUR_IF_STAGE2_FAILS=2 \
    S6_CMD_WAIT_FOR_SERVICES_MAXTIME=0 \
    S6_VERBOSITY=1 \
    S6_KEEP_ENV=1 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer \
    COMPOSER_MAX_PARALLEL_HTTP=24 \
    WEBUSER_HOME="/var/www/html" \
    PHP_DATE_TIMEZONE="UTC" \
    PHP_DISPLAY_ERRORS=Off \
    PHP_DISPLAY_STARTUP_ERRORS=Off \
    PHP_ERROR_LOG="/dev/stderr" \
    PHP_ERROR_REPORTING="22527" \
    PHP_MAX_EXECUTION_TIME="99" \
    PHP_MAX_INPUT_TIME="-1" \
    PHP_MEMORY_LIMIT="256M" \
    PHP_OPCACHE_ENABLE="0" \
    PHP_OPCACHE_INTERNED_STRINGS_BUFFER="8" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="10000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="128" \
    PHP_OPCACHE_REVALIDATE_FREQ="2" \
    PHP_OPEN_BASEDIR="" \
    PHP_POST_MAX_SIZE="100M" \
    PHP_SESSION_COOKIE_SECURE=false \
    PHP_UPLOAD_MAX_FILE_SIZE="100M" \
    PUID=9999 \
    PGID=9999

# Bring over the s6 overlay setup
COPY --from=setup /opt/s6/ /

# Bring over the ondrej sources
COPY --from=setup /etc/apt/sources.list.d/ /etc/apt/sources.list.d/

RUN apt-get update \
    \
    # configure web user and group \
    && groupadd -r -g $PGID webgroup \
    && useradd --no-log-init -r -s /usr/bin/bash -d $WEBUSER_HOME -u $PUID -g $PGID webuser \
    \
    # install dependencies \
    && apt-get -y --no-install-recommends install \
    ca-certificates \
    curl \
    git \
    gnupg \
    s6 \
    unzip \
    zip \
    \
    # install PHP packages, TODO: why update and install seperate? \
    && apt-get update \
    && apt-get -y --no-install-recommends install \
    php8.4-apcu \
    php8.4-bcmath \
    php8.4-cli \
    php8.4-common \
    php8.4-curl \
    php8.4-gd \
    php8.4-imagick \
    php8.4-intl \
    php8.4-mailparse \
    php8.4-mbstring \
    php8.4-pgsql \
    php8.4-redis \
    php8.4-soap \
    php8.4-sqlite3 \
    php8.4-xml \
    php8.4-zip \
    \
    # set symlink to version number for script management \
    && ln -sf /etc/php/${BUILD_PHP_VERSION}/ /etc/php/current_version \
    \
    # install postgres client \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] https://apt.postgresql.org/pub/repos/apt noble-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends postgresql-client-"$POSTGRES_VERSION" \
    # Upgrade \
    && apt-get update \
    && apt-get upgrade -y \
    # cleanup \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Bring in the shared s6 overlay tasks
COPY --chmod=755 docker/etc/s6-overlay/ /etc/s6-overlay/

WORKDIR /var/www/html

# TODO: Ensure this is run be an unprivileged user and change it to www-data

# Install JS package management
ENV NVM_VERSION=v0.40.2
ENV NODE_VERSION=23.11.0
ENV NPM_VERSION=^11.0.0
ENV NVM_DIR=/usr/local/nvm
RUN mkdir "$NVM_DIR"

RUN curl -o- "https://raw.githubusercontent.com/nvm-sh/nvm/${NVM_VERSION}/install.sh" | bash

ENV NODE_PATH=$NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH=$NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

RUN echo "source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default \
    && npm install -g npm@$NPM_VERSION" | bash

ENTRYPOINT ["/init"]

FROM base AS web-base

ENV NGINX_FASTCGI_BUFFERS="8 8k" \
    NGINX_FASTCGI_BUFFER_SIZE="8k" \
    NGINX_SERVER_TOKENS=off \
    NGINX_WEBROOT=/var/www/html/public

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    nginx \
    \
    # ensure web permissions are correct \
    && chown -R webuser:webgroup /var/www/html/ \
    \
    # cleanup \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/www/html/* \
    && rm -f /etc/nginx/sites-enabled/default

COPY docker/etc/nginx/ /etc/nginx/

COPY --from=ghcr.io/roadrunner-server/roadrunner:2024.3.5 --chown=$PUID:$PGID --chmod=0755 /usr/bin/rr /usr/local/bin/rr

COPY --chmod=755 ./docker/web/s6-overlay/ /etc/s6-overlay/

EXPOSE 80 443

FROM web-base AS web-development

# Fix permission issues in development by setting the "webuser"
# user to the same user and group that is running docker.
COPY ./docker/set-id /set-id

ARG PUID
ARG PGID
RUN set-id webuser ${PUID} ${PGID} ; \
    rm /set-id

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chmod g+s -R /var/www/html

FROM web-base AS web-deploy

COPY --chown=$PUID:$PGID . /var/www/html

RUN npm ci --ignore-scripts \
    && rm -rf /var/www/html/vendor \
    && composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader --apcu-autoloader \
    && npm run build \
    && npm ci --ignore-scripts --omit=dev

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chgrp "$PGID" /var/www/html/storage/logs \
    && chmod g+s /var/www/html/storage/logs \
    && find /var/www/html -type d -print0 | xargs -0 chmod 755 \
    && find /var/www/html \( -path /var/www/html/docker -o -path /var/www/html/node_modules -o -path /var/www/html/vendor \) -prune -o -type f -print0 | xargs -0 chmod 644 \
    && chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

FROM base AS worker-base

ARG TOTAL_QUEUE_WORKERS=3

COPY ./docker/worker/generate-queues.sh /generate-queues.sh
COPY ./docker/worker/templates/ /tmp/s6-overlay-templates
RUN chmod +x /generate-queues.sh

FROM worker-base AS worker-development

# Fix permission issues in development by setting the "webuser"
# user to the same user and group that is running docker.
COPY ./docker/set-id /set-id

ARG PUID
ARG PGID
RUN set-id webuser ${PUID} ${PGID} ; \
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

FROM worker-base AS worker-deploy

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
    && composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader --apcu-autoloader \
    && npm run build:vite \
    && npm ci --ignore-scripts --omit=dev

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chgrp "$PGID" /var/www/html/storage/logs \
    && chmod g+s /var/www/html/storage/logs \
    && find /var/www/html -type d -print0 | xargs -0 chmod 755 \
    && find /var/www/html \( -path /var/www/html/docker -o -path /var/www/html/node_modules -o -path /var/www/html/vendor \) -prune -o -type f -print0 | xargs -0 chmod 644 \
    && chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

FROM base AS scheduler-base

COPY --chmod=644 ./docker/cron.d/ /etc/cron.d/
COPY --chmod=755 ./docker/scheduler/s6-overlay/ /etc/s6-overlay/

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    cron \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

FROM scheduler-base AS scheduler-development

# Fix permission issues in development by setting the "webuser"
# user to the same user and group that is running docker.
COPY ./docker/set-id /set-id

ARG PUID
ARG PGID
RUN set-id webuser ${PUID} ${PGID} ; \
    rm /set-id

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chmod g+s -R /var/www/html

FROM scheduler-base AS scheduler-deploy

COPY --chown=$PUID:$PGID . /var/www/html

RUN npm ci --ignore-scripts \
    && rm -rf /var/www/html/vendor \
    && composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader --apcu-autoloader \
    && npm run build:vite \
    && npm ci --ignore-scripts --omit=dev

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chgrp "$PGID" /var/www/html/storage/logs \
    && chmod g+s /var/www/html/storage/logs \
    && find /var/www/html -type d -print0 | xargs -0 chmod 755 \
    && find /var/www/html \( -path /var/www/html/docker -o -path /var/www/html/node_modules -o -path /var/www/html/vendor \) -prune -o -type f -print0 | xargs -0 chmod 644 \
    && chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

FROM base AS release-automation

COPY --chmod=755 ./docker/release-automation/s6-overlay/ /etc/s6-overlay/

COPY --chown=$PUID:$PGID . /var/www/html

RUN rm -rf /var/www/html/vendor \
    && composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader --apcu-autoloader

RUN chown -R "$PUID":"$PGID" /var/www/html \
    && chgrp "$PGID" /var/www/html/storage/logs \
    && chmod g+s /var/www/html/storage/logs \
    && find /var/www/html -type d -print0 | xargs -0 chmod 755 \
    && find /var/www/html \( -path /var/www/html/docker -o -path /var/www/html/vendor \) -prune -o -type f -print0 | xargs -0 chmod 644 \
    && chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

FROM base AS cli-local-tooling