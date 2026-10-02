#!/bin/sh
set -eu

if [ ! -f .env ]; then
  echo "ERROR: .env does not exist. Copy .env.example to .env and configure production values first."
  exit 1
fi

if command -v composer2 >/dev/null 2>&1; then
  COMPOSER=composer2
elif command -v composer >/dev/null 2>&1; then
  COMPOSER=composer
else
  echo "ERROR: Composer was not found."
  exit 1
fi

$COMPOSER install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan storage:link || true
php artisan optimize

echo "Deployment complete. Verify /up, /admin and /portal."
