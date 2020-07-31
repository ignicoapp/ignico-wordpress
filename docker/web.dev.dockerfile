FROM nginx:1.10

COPY ./docker/web/virtualhost.dev.conf /etc/nginx/conf.d/default.conf
