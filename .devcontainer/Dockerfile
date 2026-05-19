FROM mcr.microsoft.com/devcontainers/php:1-8.4-bookworm

RUN rm -f /etc/apt/sources.list.d/yarn.list \
    && docker-php-ext-install pdo_mysql mysqli
