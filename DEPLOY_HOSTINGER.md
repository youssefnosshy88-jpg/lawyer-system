# Hostinger deployment — Lawyer System

## Requirements
- PHP 8.2+ (8.3 recommended)
- Composer 2
- MySQL 8 / compatible MariaDB
- PHP extensions: DOM/XML, mbstring, PDO MySQL, fileinfo, intl, openssl, tokenizer, ctype, curl
- Cron access for Laravel scheduler

## 1. Deploy source
Clone or upload the repository and point the domain document root to the Laravel `public/` directory. If Hostinger does not allow changing document root, keep the application outside `public_html` and place only the contents of `public/` inside `public_html`, updating the two bootstrap paths in `index.php` accordingly.

Do **not** upload `.env`, database dumps, `vendor/`, local logs, cached Blade files, or session files from a workstation.

## 2. Install dependencies
```bash
composer install --no-dev --prefer-dist --optimize-autoloader
```
The Composer scripts run Filament package discovery/upgrade. If published Filament assets are absent, run:
```bash
php artisan filament:assets
```

## 3. Environment
```bash
cp .env.example .env
php artisan key:generate
```
Set at minimum:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain`
- MySQL `DB_*` values
- mail settings
- `ADMIN_EMAIL`, `ADMIN_PASSWORD`, and optionally `ADMIN_NAME`

Use a strong unique `ADMIN_PASSWORD`. Never use the demo password in production.

## 4. Database
For a new installation:
```bash
php artisan migrate --force
php artisan db:seed --force
```
The production seeder creates the admin account only when `ADMIN_EMAIL` and `ADMIN_PASSWORD` are defined. Demo data runs only in the local environment.

The historical `lawfirm.sql` dump is intentionally **not** stored in the public GitHub repository because it contains private application data. Import it only through a private transfer if a real data migration is required, after reviewing compatibility with the migrations.

## 5. Storage and caches
```bash
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```
Use route caching only after confirming no environment-specific route issue:
```bash
php artisan route:cache
```
Ensure `storage/` and `bootstrap/cache/` are writable by PHP.

## 6. Scheduler
Add one cron job running every minute:
```bash
* * * * * cd /home/USER/path/to/app && php artisan schedule:run >> /dev/null 2>&1
```
The application schedules hearing reminders, deadline reminders, and overdue-invoice updates.

## 7. Verification
Check:
- `/up` returns healthy status
- `/admin` redirects guests to `/admin/login`
- admin login works
- `/portal` is restricted to client users
- invoice and payment print pages work when authenticated
- uploaded/private documents are not publicly exposed
- `APP_DEBUG=false`

Run when dev dependencies are installed in staging/CI:
```bash
php artisan test
```
