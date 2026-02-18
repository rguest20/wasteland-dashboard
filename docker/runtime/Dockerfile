FROM php:8.4-fpm

RUN set -eux; \
  apt-get update -y; \
  apt-get install -y --no-install-recommends \
    git unzip zip openssl \
    supervisor \
    libxml2-dev \
    libzip-dev zlib1g-dev \
    libpng-dev libjpeg62-turbo-dev libwebp-dev \
    libicu-dev \
    libonig-dev \
    mariadb-client \
  ; \
  docker-php-ext-install opcache; \
  docker-php-ext-configure soap --enable-soap; \
  docker-php-ext-configure gd --with-jpeg --with-webp; \
  docker-php-ext-install -j"$(nproc)" \
    soap pdo_mysql gd zip pcntl intl mbstring \
  ; \
  pecl install xdebug-3.5.0; \
  docker-php-ext-enable xdebug; \
  rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ADD docker/runtime/config/supervisord.conf /etc/supervisord.conf
ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

WORKDIR /var/www
