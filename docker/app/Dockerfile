FROM php:8-fpm-alpine3.13 AS base

ENV TZ UTC
ARG DOCKER_DIR=docker/app
ENV COMPOSER_HOME=/app
ENV PATH "$PATH:/app/vendor/bin"

RUN rm -fr /usr/local/etc/php-fpm.d/* && \
    addgroup app && \
    adduser -D -h /app -s /bin/bash -G app app

RUN apk upgrade --no-cache --available && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev && \
    apk add --no-cache icu-libs && \
    docker-php-ext-configure intl && \
    docker-php-ext-install mysqli pdo pdo_mysql intl && \
    docker-php-ext-enable intl && \
    docker-php-source delete && \
    apk del .build-deps && \
    rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apk/* /usr/src/php.tar.xz

USER app
WORKDIR /app
EXPOSE 9000
CMD ["php-fpm", "-R"]


FROM base as composer

USER root
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
USER app
WORKDIR /app
COPY --chown=app:app composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-dev


FROM base as release

USER app
WORKDIR /app
COPY --chown=app:app . .
COPY --from=composer --chown=app:app /app/vendor /app/vendor


FROM base as dev

USER root
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-source delete && \
    apk del .build-deps && \
    rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apk/* /usr/src/php.tar.xz


USER app
WORKDIR /app
COPY --chown=app:app composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist