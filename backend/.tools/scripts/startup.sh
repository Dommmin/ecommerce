#!/bin/sh

#composer install --no-dev --optimize-autoloader --no-scripts

php artisan key:generate
php artisan migrate
php artisan config:cache
#php artisan serve --host=0.0.0.0 --port=8000
php artisan octane:frankenphp --host=0.0.0.0 --port=8000 --max-requests=1
