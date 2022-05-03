#!/bin/sh

# update application cache
php artisan optimize

# run only migrations
php artisan migrate --seed --force
while [ $? -ne 0 ]; do
    sleep 5
    php artisan migrate --seed --force
done

php-fpm -D &&  nginx -g "daemon off;"
