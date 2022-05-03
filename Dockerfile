FROM php:8.0-fpm

# Install dockerize so we can wait for containers to be ready
ENV DOCKERIZE_VERSION 0.6.1

RUN curl -s -f -L -o /tmp/dockerize.tar.gz https://github.com/jwilder/dockerize/releases/download/v$DOCKERIZE_VERSION/dockerize-linux-amd64-v$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf /tmp/dockerize.tar.gz \
    && rm /tmp/dockerize.tar.gz

# Install Composer
ENV COMPOSER_VERSION 2.1.5

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=$COMPOSER_VERSION

# Install nodejs
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        build-essential \
        openssl \
        nginx \
        libz-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libssl-dev \
        libzip-dev \
        unzip \
        zip \
        nodejs \
    && apt-get clean \
    && pecl install redis \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure zip \
    && docker-php-ext-install \
        gd \
        exif \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        pcntl \
        zip \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*;

COPY ./docker/php/laravel.ini /etc/php/conf.d/laravel.ini

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

COPY . /var/www

RUN chmod +rwx /var/www

RUN chmod -R 777 /var/www

RUN composer install --working-dir="/var/www"

RUN composer dump-autoload --working-dir="/var/www"

WORKDIR /var/www

EXPOSE 80

RUN ["chmod", "+x", "./docker/php/deploy_scripts.sh"]

CMD [ "sh", "./docker/php/deploy_scripts.sh" ]
