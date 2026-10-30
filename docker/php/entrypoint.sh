#!/bin/sh
# Arranque de la imagen de produccion: permisos, caches y migraciones.
set -e

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
    echo "Migrando la base..."
    intento=1
    migrado=0
    while [ "$intento" -le 30 ]; do
        if php artisan migrate --force --no-interaction; then
            echo "Migraciones al dia."
            migrado=1
            break
        fi
        echo "Base no disponible (intento $intento/30), reintentando en 3s..."
        intento=$((intento + 1))
        sleep 3
    done
    if [ "$migrado" = "0" ]; then
        echo "No se pudo migrar: la base no respondio en 90s." >&2
        exit 1
    fi
fi

if [ "${RUN_SEED:-0}" = "1" ]; then
    php artisan db:seed --force --no-interaction
fi

exec "$@"
