FROM php:7.4-cli
ADD ./ /app
WORKDIR /app
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev
ENTRYPOINT [ "php", "./index.php" ]
