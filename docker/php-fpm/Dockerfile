FROM timonier/php:fpm

COPY ./docker/php-fpm/rootfs /

COPY ./ /srv
WORKDIR /srv

RUN set -ex \
\
    # Create assets, cache, files and logs folders
\
    && mkdir -p \
           var/cache \
           var/files \
           var/logs \
           web/bundles
