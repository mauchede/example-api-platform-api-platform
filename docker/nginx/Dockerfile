FROM nginx:stable-alpine

COPY ./docker/nginx/rootfs /

COPY ./web /srv/web
WORKDIR /srv

ENTRYPOINT [ "docker-entrypoint" ]
