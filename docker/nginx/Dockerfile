FROM nginx:stable-alpine

COPY ./docker/nginx/rootfs /

COPY ./public /srv/public
WORKDIR /srv

ENTRYPOINT [ "nginx" , "-g" , "daemon off;" ]
