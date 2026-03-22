#!/bin/sh
set -e

# Создать и выставить права на директории, куда FPM должен писать
for dir in /app/public/local/routes /var/lib/php/sessions; do
    mkdir -p "$dir"
    chown -R www-data:www-data "$dir"
done

exec "$@"
