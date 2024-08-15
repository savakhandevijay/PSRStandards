# 8.2.22-zts-alpine3.20
FROM php:8.2.22-fpm-alpine3.20

# add cli tools
RUN apk update && apk upgrade \
    && apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} linux-headers gcc make autoconf go pkgconf

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
COPY ./php/xdebug.ini /usr/local/etc/php/conf.d/

# RUN export MAKEFLAGS="-j $(nproc)" && pecl install xdebug && docker-php-ext-enable xdebug

# strip php extensions to decrease container size
RUN set -eux && find "$(php-config --extension-dir)" -name '*.so' -type f -exec strip --strip-all {} \;

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN export PATH="$PATH:$HOME/.composer/vendor/bin"