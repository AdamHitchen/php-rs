FROM php:8.1-rc-alpine
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync
RUN install-php-extensions xdebug
RUN mkdir -p /opt/phpstorm-coverage
WORKDIR /mnt
RUN addgroup -g 1000 -S hitch && adduser -u 1000 -S hitch -G hitch
USER hitch
ENTRYPOINT ["php", "vendor/bin/phpunit"]