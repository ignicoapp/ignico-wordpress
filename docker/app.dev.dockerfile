FROM php:7.3-fpm

# Install MariaDB extension
RUN apt-get update \
	&& apt-get install -y mariadb-client \
    && docker-php-ext-install mysqli

# Install ImageMagick extension
RUN apt-get update \
	&& apt-get install -y libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Install utils
RUN apt-get update \
	&& apt-get install -y git subversion zip unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - \
	&& apt-get install -y nodejs

# Use the default development configuration
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
COPY ./docker/app/php.dev.ini /usr/local/etc/php/conf.d/php.ini

# Move pool files to make some order
RUN mv /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/10-www.conf
RUN mv /usr/local/etc/php-fpm.d/docker.conf /usr/local/etc/php-fpm.d/20-docker.conf

# Copy pool configuration
COPY ./docker/app/pool.dev.conf /usr/local/etc/php-fpm.d/99-www.conf
