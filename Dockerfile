FROM php:fpm-alpine

COPY ./*.php /app/
COPY ./favicon.* /app/
COPY ./.htaccess /app/

COPY ./css /app/css
COPY ./fonts /app/fonts
COPY ./img /app/img
COPY ./js /app/js
COPY ./languages /app/languages

COPY ./smartyfolders /app/smartyfolders

COPY ./support /app/support
# COPY docker specific dba.php
COPY ./support/dba-docker.php /app/support/dba.php
COPY ./templates /app/templates
COPY ./vendor /app/vendor
COPY ./welcome_lang /app/welcome_lang
RUN chown www-data:www-data -R /app/

VOLUME /app

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions
RUN /usr/local/bin/install-php-extensions mysqli gettext gd intl pdo_mysql
