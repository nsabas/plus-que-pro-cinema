FROM php:8.2-fpm-buster as symfony_php_dev
ARG TIMEZONE

COPY docker/php/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN apt-get update && apt-get install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev \
    libpq-dev \
    acl \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc


RUN docker-php-ext-configure gd --with-jpeg --with-freetype

RUN docker-php-ext-install \
    pdo pgsql pdo_pgsql zip xsl gd intl opcache exif mbstring

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# get install script and pass it to execute:
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash
# and install node
RUN apt-get install nodejs -y


RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt update && apt install -y yarn

USER www-data
WORKDIR /var/www/symfony

COPY --chown=www-data:www-data .env ./
COPY --chown=www-data:www-data bin/ ./bin/
COPY --chown=www-data:www-data src/ ./src/
#COPY --chown=www-data:www-data assets/ ./assets/
COPY --chown=www-data:www-data public/ ./public/
COPY --chown=www-data:www-data config/ ./config/
COPY --chown=www-data:www-data templates/ ./templates/
COPY --chown=www-data:www-data migrations/ ./migrations/
COPY --chown=www-data:www-data translations/ ./translations/
COPY --chown=www-data:www-data composer.lock ./
COPY --chown=www-data:www-data composer.json ./

#COPY --chown=www-data:www-data webpack.config.js package.json yarn.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction

FROM symfony_php_dev AS symfony_php

CMD php -d memory_limit=-1 bin/console doctrine:migration:migrate --no-interaction  && \
    php  -d memory_limit=-1 bin/console app:trending:movie:load
