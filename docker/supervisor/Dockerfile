FROM php:7.4-fpm

# install main dependencies
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    libzip-dev \
    curl \
  && rm -rf /var/lib/apt/lists/* \
  # install sub dependencies
  && rm /etc/apt/preferences.d/no-debian-php \
  && apt-get update -yqq && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install zip && \
  # install Composer
   (curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer)

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
        install-php-extensions amqp

RUN apt-get update && apt-get install -y supervisor
RUN mkdir -p /var/log/supervisor
COPY newsparser.conf /etc/supervisor/conf.d/supervisord.conf



WORKDIR ../symf_project

CMD ["/usr/bin/supervisord"]


EXPOSE 9000
