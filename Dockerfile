FROM php:7-fpm-buster

COPY ./ /app/
RUN chown www-data:www-data -R /app/

VOLUME /app

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install gettext

RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
