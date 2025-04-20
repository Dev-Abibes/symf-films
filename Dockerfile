#syntax=docker/dockerfile:1

# Étape 1 : Build du conteneur PHP
FROM php:8.2-fpm-alpine AS builder

# Variables d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=prod

# Installation des dépendances système et extensions PHP
RUN apk add --no-cache \
        freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
        postgresql-dev icu-dev oniguruma-dev libzip-dev git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd pdo pdo_pgsql intl mbstring zip opcache \
    && apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copie des fichiers nécessaires pour Composer
WORKDIR /app
COPY composer.* symfony.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-scripts --optimize-autoloader

# Étape 2 : Conteneur final
FROM php:8.2-fpm-alpine

# Copie des extensions PHP depuis l'étape précédente
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer

# Configuration PHP
RUN docker-php-ext-enable opcache pdo pdo_pgsql intl mbstring zip gd

# Configuration OPCache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini

# Copie des fichiers de l'application
WORKDIR /app
COPY . .

# Nettoyage et permissions
RUN rm -rf var/cache/* var/log/* vendor/ \
    && mkdir -p var/cache var/log \
    && chmod -R 777 var

CMD ["php-fpm"]
