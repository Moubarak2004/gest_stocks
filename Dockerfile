FROM php:8.1-fpm-alpine

# --- Dépendances système + extensions PHP nécessaires ---
# postgresql-dev -> permet de compiler pdo_pgsql/pgsql
# libzip-dev, libpng-dev, oniguruma-dev, freetype-dev, libjpeg-turbo-dev -> requis par zip/gd/mbstring
RUN apk add --no-cache \
        nginx \
        bash \
        postgresql-dev \
        libzip-dev \
        libpng-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip gd bcmath

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copie du code de l'application
COPY . .

# Installation des dépendances PHP en mode production (pas de dev-dependencies)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Droits d'écriture nécessaires à Laravel (logs, cache, sessions)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Config Nginx (template : le port sera injecté au démarrage par entrypoint.sh)
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000

CMD ["/entrypoint.sh"]
