#
# php/Dockerfile
# it-vends
#

FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# small helper packages (bash) — keep image minimal
RUN apk add --no-cache bash

# Copy application code into the container
COPY . /var/www/html

# Ensure correct permissions for php-fpm www user
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
