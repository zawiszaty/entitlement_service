
FROM php:8.0.6-fpm-alpine3.13

COPY . /var/www/html

RUN apk update && \
		apk add --no-cache $PHPIZE_DEPS \
		git \
        libzip-dev \
        unzip \
        zip \
        nginx \
        bash \
        libpng-dev \
        postgresql-dev \
        rabbitmq-c rabbitmq-c-dev \
        && pecl install -o -f xdebug-3.0.0 \
  		&& docker-php-ext-install zip \
  		&& docker-php-ext-install pdo pdo_pgsql \
  		&& docker-php-ext-install bcmath sockets pcntl gd \
  		&& docker-php-ext-enable xdebug \
  		&& mkdir -p /run/nginx

COPY ./.docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

ENTRYPOINT ["sh", "/var/www/html/.docker/entrypoint.sh"]