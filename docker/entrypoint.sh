#!/bin/bash
set -e

PORT="${PORT:-10000}"
sed "s/__PORT__/$PORT/" /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

cd /var/www/html

# Nettoie les anciens caches (utile en cas de redéploiement)
php artisan config:clear || true

# Caches de prod
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrations
php artisan migrate --force

# Seed (non-bloquant : si les données existent déjà, on continue)
php artisan db:seed --force || true

# Lien storage
php artisan storage:link || true

# Démarrage
php-fpm -D
nginx -g "daemon off;"