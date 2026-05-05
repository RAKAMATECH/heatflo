#!/usr/bin/env bash
set -e

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf

if [ -n "${APP_KEY:-}" ]; then
  php artisan config:clear --no-interaction || true
  php artisan route:clear --no-interaction || true
  php artisan view:clear --no-interaction || true
  php artisan storage:link --no-interaction || true

  if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
  fi

  if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction
  fi

  php artisan config:cache --no-interaction || true
fi

apache2-foreground
