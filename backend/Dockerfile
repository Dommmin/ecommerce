#FROM unit:php8.2
#FROM dunglas/frankenphp:1.0.3-php8.2 as base
#ENV FRANKENPHP_CONFIG="worker ./public/index.php"
#FROM openswoole/swoole:php8.2


# Use an official PHP runtime as a parent image
FROM php:8.2-fpm

# Set working directory
WORKDIR /app/backend

# Copy php.ini to the docker image
COPY .docker/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    zlib1g-dev \
    default-mysql-client \
    curl \
    gnupg \
    procps \
    vim \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libmcrypt-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    curl \
    gnupg

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# Install PHP extensions
RUN docker-php-ext-install zip pdo_mysql pdo_pgsql pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip bcmath pcntl sockets

# Install PECL extensions
RUN pecl install xdebug pcov redis swoole \
    && docker-php-ext-enable xdebug pcov redis swoole

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Change ownership of our applications
RUN chown -R www-data:www-data /app

# Copy composer.json and composer.lock
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Install npm dependencies
RUN npm install --save-dev chokidar

# Copy the rest of the application code
COPY .. .

# Expose port 8000 for the application
EXPOSE 8000

# Copy scripts
ADD .tools/scripts /srv/

RUN chmod +x /srv/startup.sh

# Set the entrypoint script
ENTRYPOINT ["/bin/sh", "/srv/startup.sh"]
