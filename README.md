## Mriduukriti

Modern Laravel 12 + Vite storefront for Mriduukriti. This repo now contains everything required to collaborate through GitHub and deploy to a Hostinger shared server over SSH, while keeping secrets outside of version control.

### Tech Stack
- PHP 8.2+, Laravel 12, MySQL or MariaDB
- Vite, TailwindCSS, Alpine.js, Font Awesome for iconography
- Spatie Permissions, Laravel Socialite, Atom (NTT Data) payment integration

### Requirements
- PHP 8.2 with required extensions (`bcmath`, `ctype`, `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`)
- Composer 2.7+
- Node.js 20 LTS + npm 10 (only needed where assets are built)
- MySQL 8+ or MariaDB 10.6+
- Git 2.34+

### Getting Started
```bash
git clone <repo-url>
cd mriduukriti
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev           # or npm run build for production assets
php artisan serve
```

### Quality & Maintenance
- `composer test` – runs feature/unit tests
- `vendor/bin/pint` – auto-formats PHP
- `npm run build` – produces versioned Vite assets in `public/build`
- `php artisan optimize` – cache config/routes/views for production

### Deployment Checklist
1. Copy `.env.example` and fill in production secrets.
2. `composer install --no-dev --optimize-autoloader`
3. `npm ci && npm run build` (or build locally and upload `public/build`)
4. `php artisan migrate --force`
5. `php artisan storage:link`
6. `php artisan config:cache route:cache view:cache`

Full GitHub + Hostinger SSH deployment steps live in `docs/DEPLOYMENT.md`.

### Support
Create a GitHub issue or reach out to the engineering team with logs from `storage/logs/laravel.log`. Never commit `.env` or other secrets—use the template and Hostinger SSH keys instead.
