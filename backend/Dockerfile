FROM php:8.2-fpm

COPY php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

# Install system dependencies
RUN apt-get update \
  && apt-get install -y build-essential zlib1g-dev default-mysql-client curl gnupg procps vim git unzip libzip-dev libpq-dev  \
  libpng-dev libonig-dev libxml2-dev libmcrypt-dev libjpeg-dev libfreetype6-dev \
  && docker-php-ext-install zip pdo_mysql pdo_pgsql pgsql

RUN apt-get install -y libicu-dev \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl

# Install xdebug for code coverage
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug


# pcov
RUN pecl install pcov && docker-php-ext-enable pcov

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip bcmath pcntl

# Install PECL extensions
RUN pecl install redis \
    && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app/backend

RUN chown -R www-data:www-data /app

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

COPY . .

EXPOSE 8000

ADD .tools/scripts/ /srv/

ENTRYPOINT ["/bin/sh", "/srv/startup.sh"]
