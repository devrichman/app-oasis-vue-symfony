FROM thecodingmachine/php:7.4-v3-apache

COPY --chown=docker:docker . .

ENV PHP_EXTENSION_GD=1 \
    APP_ENV="prod"

# Dummy values for allowing composer install.
ENV DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

RUN composer install
