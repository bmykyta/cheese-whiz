FROM php:8.1-fpm

RUN apt-get update && apt-get install --no-install-recommends -y \
    librabbitmq-dev \
    zlib1g-dev \
    libssl-dev \
    openssl \
    libmcrypt-dev \
    libpng-dev \
    libgd-dev \
    git \
    libicu-dev \
    libxml2-dev \
    libzip-dev


RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get -y install nodejs
RUN npm install --global yarn

RUN docker-php-ext-install \
       pdo_mysql \
       zip \
       iconv \
       gd \
       opcache \
       bcmath \
       intl \
       soap \
       sockets

RUN pecl install xdebug  && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=192.168.200.1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN chmod 777 /var --recursive

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer