# Hostinger deployment

This repository is prepared for Hostinger Web/Cloud hosting where the domain root is usually `public_html`.

## Requirements

- PHP 8.2 or newer (PHP 8.3 recommended)
- Extensions: mbstring, XML/DOM, PDO MySQL, OpenSSL, Fileinfo, Ctype, JSON, Tokenizer
- MySQL 8 / compatible MariaDB
- SSH access is strongly recommended

## First deployment

1. In hPanel, set the website PHP version to 8.3 (or at least 8.2).
2. Deploy/clone this repository into the domain `public_html` directory.
3. Create the production `.env` from `.env.example` and fill in the real domain/database/mail values. Never commit `.env`.
4. From SSH, in the project root, run:

```bash
composer2 install --no-dev --prefer-dist --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

If the database was restored from the supplied SQL dump instead of created from migrations, do not re-import the dump on every deployment. Run `php artisan migrate --force` only to apply newer migrations.

## Recommended production .env values

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://YOUR-DOMAIN.TLD
APP_TIMEZONE=Africa/Cairo
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
FIRM_DOCUMENTS_DISK=private
```

Set the actual `DB_*` and `MAIL_*` values in the server `.env` only.

## Scheduler (required)

The application schedules hearing reminders, company-deadline reminders and overdue invoice updates. Add one Hostinger Cron Job to run every minute:

```bash
/usr/bin/php /home/USERNAME/domains/YOUR-DOMAIN.TLD/public_html/artisan schedule:run
```

## Queue worker

The project uses `QUEUE_CONNECTION=database`. If persistent workers are not available on the hosting plan, a cron job can process queued jobs in bounded runs:

```bash
/usr/bin/php /home/USERNAME/domains/YOUR-DOMAIN.TLD/public_html/artisan queue:work --stop-when-empty --tries=3
```

## Subsequent deployments

```bash
git pull
composer2 install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

## Health check

Verify these URLs after deployment:

- `/up`
- `/admin`
- `/portal`
