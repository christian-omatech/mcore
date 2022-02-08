ARG PHP_VERSION=8.0

FROM php:${PHP_VERSION}-cli-alpine

ARG UID
ARG GID

COPY php/zzz-fpm.conf /usr/local/etc/php-fpm.d/zzz-fpm.conf

RUN apk --update --no-cache add \
    curl \
    git

RUN apk --update --no-cache add --virtual deps $PHPIZE_DEPS
RUN pecl install -f xdebug
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN docker-php-ext-enable xdebug
RUN apk del --purge deps

RUN echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.start_with_request = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.discover_client_host = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN addgroup -S -g ${GID} app \
 && adduser -S -D -u ${UID} -s /sbin/nologin -h /home/app -G app app \
 && chown -R app:app /home/app \
 && chmod -R g+w /home/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN mkdir -p /home/app/.composer \
 && chmod -R g+w /home/app/.composer

USER app
WORKDIR /app

EXPOSE 9003