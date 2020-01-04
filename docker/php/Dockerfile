FROM php:7-fpm-alpine
RUN apk add --no-cache --update \
        gettext-dev \
        icu-dev \
    && docker-php-ext-install -j$(nproc) mysqli \
    && docker-php-ext-install -j$(nproc) gettext \
    && docker-php-ext-install -j$(nproc) intl \
    && rm -rf /var/cache/apk/* /tmp/* /var/tmp/*