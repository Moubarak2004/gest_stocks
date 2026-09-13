#!/bin/bash
set -e

# Render fournit dynamiquement le port à écouter via $PORT.
# En local (docker run sans PORT défini), on retombe sur 10000.
PORT="${PORT:-10000}"
sed "s/__PORT__/$PORT/" /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

cd /var/www/html

# Met en cache la config, les routes et les vues pour de meilleures perfs en prod.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Applique les migrations sur la base PostgreSQL à chaque démarrage/déploiement.
# --force est nécessaire car APP_ENV=production sinon Laravel demande une confirmation interactive.
php artisan migrate --force

# Recrée (ou remet à jour) les comptes de démonstration admin/gérant/vendeur à chaque déploiement.
# Comme le seeder utilise updateOrCreate(), ça ne duplique jamais les comptes.
# Attention : si un jour tu changes le mot de passe d'un de ces 3 comptes depuis l'app,
# il sera réécrit avec la valeur du seeder au prochain déploiement.
php artisan db:seed --force

# Crée le lien storage -> public si besoin (pour les images uploadées).
php artisan storage:link || true

# Démarre php-fpm en arrière-plan, puis Nginx au premier plan (process principal du conteneur).
php-fpm -D
nginx -g "daemon off;"
