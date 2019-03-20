FROM php:7.3-apache

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV AUTH0_DOMAIN=avaazz.auth0.com
ENV AUTH0_CLIENT_ID=sGRnbt7rujtTyhG4C7Sv0F3vDkinVjI3
ENV AUTH0_CLIENT_SECRET=V9uiiL8V3TsfqSTwnbmc_6Hp0uKgKl-6qzVsUm0atc5eW4U5_ycHeCqUcQLFA2ix
ENV AUTH0_REDIRECT_URI=http://localhost/public/index.php/verify
ENV AUTH0_AUDIENCE=https://avaazz.auth0.com/userinfo
ENV AUTH0_SCOPE=openid profile

# Setting working directory
WORKDIR /var/www/html

RUN apt-get update -y && apt-get install -y libmcrypt-dev openssl git
# RUN pecl install mcrypt-1.0.2
# RUN docker-php-ext-install pdo mcrypt mbstring

RUN a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copying all data to directory
COPY ./www /var/www/html

# Installing composer files
RUN composer install

RUN mv ./public/temp.htaccess .htaccess