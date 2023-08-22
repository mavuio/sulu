# Set our base image
FROM serversideup/php:8.2-fpm-nginx

ENV NODE_VERSION="16"

ENV PHP_MEMORY_LIMIT="1024M"
ENV SSL_MODE="off"
ENV PHP_SESSION_COOKIE_SECURE=0

ENV NPM_BINARY="/usr/bin/pnpm"
ENV DUMMY_API_KEY="loremipsum"
ENV MAILER_DSN="smtp://190548mail2:max.tost.drosten.maui@smtp.easyname.com:465"



# Install PHP Imagemagick using regular Ubuntu commands
RUN apt-get update \
    && apt-get install -y --no-install-recommends php8.2-imagick \
    && apt-get install -y --no-install-recommends php8.2-xml \
    && apt-get install -y --no-install-recommends php8.2-fileinfo \
    && apt-get install -y --no-install-recommends php8.2-gd \
    && apt-get install -y --no-install-recommends php8.2-xdebug \
    && apt-get install -y --no-install-recommends php8.2-pgsql \
    && apt-get install -y --no-install-recommends git curl ca-certificates gnupg \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*


# Adds the Node repository, installs Node from it, and cleans.
RUN curl -sL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
 && apt-get -y install nodejs \
 && apt-get clean \
 && node -v

RUN npm install -g pnpm

COPY docker/s6-overlay/s6-rc.d/pnpm_updates /etc/s6-overlay/s6-rc.d/pnpm_updates
COPY docker/s6-overlay/s6-rc.d/user/contents.d/pnpm_updates /etc/s6-overlay/s6-rc.d/user/contents.d/pnpm_updates
COPY docker/s6-overlay/scripts/pnpm_update.sh /etc/s6-overlay/scripts/pnpm_update.sh

COPY docker/s6-overlay/s6-rc.d/setup_datadir /etc/s6-overlay/s6-rc.d/setup_datadir
COPY docker/s6-overlay/s6-rc.d/user/contents.d/setup_datadir /etc/s6-overlay/s6-rc.d/user/contents.d/setup_datadir
COPY docker/s6-overlay/scripts/setup_datadir.sh /etc/s6-overlay/scripts/setup_datadir.sh


COPY docker/php.ini /etc/php/8.2/cli/conf.d/custom.ini


WORKDIR /var/www/html 
COPY . .



