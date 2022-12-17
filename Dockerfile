# =================================================================
# Build PHP:8-Apache Base Image. Ref: https://hub.docker.com/_/php
FROM php:8-apache as base

# Apply maintainer label
LABEL maintainer="Richard Soares"

# Set envvar `ENVRIONMENT`
ARG ENVIRONMENT

# =================================================================
# Using the base image, create an image containing all of our files
# and dependencies installed, devDeps and test directory included
FROM base AS dependencies

# Update apt once
RUN apt-get update

# Install more deps
RUN apt-get install -y --no-install-recommends \
    dumb-init \
    libcurl4-openssl-dev \
    libfreetype6-dev \
    libgmp-dev \
    libjpeg62-turbo-dev \
    libldap2-dev \
    libonig-dev \
    libpng-dev \
    libpq-dev \
    libssl-dev \
    libtidy-dev \
    libwebp-dev \
    libzip-dev \
    nano \
    openssl \
    sudo \
    unzip \
    vim \
    && rm -rf /var/lib/apt/lists/*

# =================================================================
# Create an image containing PHP configurations
FROM dependencies AS configure-php

# Append config (required for PHP AWS SDK)
RUN echo "memory_limit = 2048M;" >> /usr/local/etc/php/conf.d/memory.ini

# Append config
RUN echo "phar.readonly = Off;" >> /usr/local/etc/php/conf.d/phar.ini

# Append config
RUN echo "date.timezone ='America/New_York'" >> /usr/local/etc/php/conf.d/timezone.ini

# =================================================================
# Create an image containing Docker PHP extension configurations
FROM configure-php AS configure-docker-php

# Ref: https://github.com/mlocati/docker-php-extension-installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions gd \
    && install-php-extensions json \
    && install-php-extensions pdo \
    && install-php-extensions pdo_mysql \
    && install-php-extensions tidy \
    && install-php-extensions zip

# =================================================================
# Create an image containing PHP extension configurations
FROM configure-docker-php AS install-app

# All of our code will live in `/srv/app`
WORKDIR /srv/app

# Add our project files
COPY .apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY .apache/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./app ./app
COPY ./public ./public

# =================================================================
# Create an image containing PHP Composer library
FROM install-app AS dependencies-composer

# Install composer
COPY composer.json composer.json
COPY composer.lock composer.lock
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install composer packages
RUN composer install

# =================================================================
# Create an image for release
FROM dependencies-composer as release

# CD to /srv/app
WORKDIR /srv/app

# Set permissions
RUN chown -R www-data:www-data /srv/app

# Add user `docker`
RUN useradd -m docker
RUN echo "docker:docker" | chpasswd
RUN adduser docker sudo

# Enable Apache mod, restart Apache
RUN a2enmod rewrite
RUN service apache2 restart
