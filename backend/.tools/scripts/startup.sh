#!/bin/sh

#dockerize -wait tcp://db:3306 -timeout 60s

#php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
