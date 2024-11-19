FROM php:7-fpm-buster

#COPY ./ /app/

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

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install gettext

RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl