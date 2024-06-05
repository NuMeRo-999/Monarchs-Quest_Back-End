#!/bin/sh
set -e

# Ejecutar composer install si el directorio vendor no existe
if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader
fi

exec "$@"