FROM php:8.3.20-fpm-alpine3.20

#RUN apk add --no-cache make=4.3-r0 yamllint=1.26.3-r0

ADD https://github.com/mlocati/docker-php-extension-installer/releases/download/2.7.31/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    apk add --no-cache libxml2-dev && \
    docker-php-ext-install dom && \
    install-php-extensions xdebug ssh2 mysqli gd mongodb zip pcntl imagick bz2 xml pdo pdo_mysql

RUN apk add dos2unix --update-cache --repository http://dl-3.alpinelinux.org/alpine/edge/community/ --allow-untruste

WORKDIR /var/www/vhosts/web

COPY docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


RUN dos2unix /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY install_composer.sh /var/www/vhosts/web/install.sh
RUN dos2unix /var/www/vhosts/web/install.sh && \
    chmod +x ./install.sh && \
    /var/www/vhosts/web/install.sh && \
    mv composer.phar /usr/local/bin/composer && \
    rm /var/www/vhosts/web/install.sh


COPY init.sql /docker-entrypoint-initdb.d/
