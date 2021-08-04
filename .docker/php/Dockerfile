FROM php:8-cli-alpine
COPY .docker/php/zzz-fpm.conf /usr/local/etc/php-fpm.d/zzz-fpm.conf

RUN apk --update --no-cache add \
    curl \
    git

RUN apk --update --no-cache add autoconf g++ make && \
        pecl install -f xdebug && \
        docker-php-ext-enable xdebug && \
        apk del --purge autoconf g++ make

RUN echo "xdebug.mode=coverage" >> "$PHP_INI_DIR/php.ini-development"
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
WORKDIR /app
