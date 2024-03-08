FROM 289208114389.dkr.ecr.us-east-1.amazonaws.com/picpay-dev/hyperf:php8.0-swoole4.8 as prod

ARG COMPOSER_AUTH
ENV COMPOSER_AUTH $COMPOSER_AUTH

COPY ./composer.* /opt/www/

RUN composer install --prefer-dist --no-dev

COPY . /opt/www/

ENTRYPOINT [ "sh" ]

FROM prod as dev
RUN apk add --update boost-dev ${PHPIZE_DEPS} \
    && pecl install pcov \
    && wget -c https://github.com/swoole/yasd/archive/refs/heads/master.tar.gz -O - | tar -xz \
    && docker-php-source extract \
    && mv yasd-master /usr/src/php/ext/yasd \
    && docker-php-ext-install yasd \
    && docker-php-ext-enable pcov \
    \
    && echo "yasd.debug_mode=remote" >> /usr/local/etc/php/conf.d/yasd.ini \
    && echo "yasd.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/yasd.ini \
    && echo "yasd.remote_port=9000" >> /usr/local/etc/php/conf.d/yasd.ini \
    && echo "opcache.enable=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    \
    && composer install
CMD [ "server:watch" ]