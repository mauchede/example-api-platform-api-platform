FROM timonier/php:fpm

COPY ./docker/php-fpm/rootfs /

COPY ./ /srv
WORKDIR /srv

RUN set -ex \
\
    # Create assets, cache, files, jwt and logs folders
\
    && mkdir -p \
           var/cache \
           var/files \
           var/jwt \
           var/logs \
           web/bundles
