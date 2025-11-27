## Deployment Guide

This document explains how to keep the repository production-ready, push it to GitHub, and deploy it to a Hostinger shared server over SSH.

---

### 1. Prerequisites
- Hostinger shared account with SSH access enabled and PHP 8.2 selected in hPanel.
- Domain or sub-domain pointing to `public_html`.
- Local workstation with Git, PHP 8.2, Composer 2.7+, Node.js 20 LTS, npm 10.
- A GitHub repository (public or private) in which you control deploy keys.

---

### 2. Environment & Secrets
1. Copy the template and fill in the values that map to your infrastructure and payment gateway credentials.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
2. Update database, mail, Google OAuth, Slack, AWS, and Atom/NTT Data settings.
3. Never commit `.env`; the `.gitignore` already protects it. To remove a previously tracked `.env`, run `git rm --cached .env`.

---

### 3. Prepare the Repository for GitHub
```bash
git init               # if not already initialized
git branch -M main
git remote add origin git@github.com:<org>/<repo>.git
git add .
git commit -m "Initial production-ready version"
git push -u origin main
```
Before every push:
- `composer install`
- `npm run build`
- `php artisan test`

---

### 4. Configure SSH Keys
#### Local key (push/pull to GitHub)
```bash
ssh-keygen -t ed25519 -C "you@example.com"
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519
```
Add the public key to **GitHub → Settings → SSH and GPG keys**.

#### Hostinger deploy key (server → GitHub)
1. Log in to Hostinger hPanel → SSH Access → generate key pair (or upload your own).
2. Copy the public key from Hostinger and add it as a **Deploy key** with read-only access in your GitHub repository.
3. Test from Hostinger:
   ```bash
   ssh -T git@github.com
   ```

---

### 5. Deploy to Hostinger over SSH
1. **SSH into the server**
   ```bash
   ssh <user>@<hostinger-server>
   cd ~/domains/<domain>/public_html
   ```
2. **Clone & checkout**
   ```bash
   git clone git@github.com:<org>/<repo>.git app
   cd app
   ```
3. **Install backend dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with production DB credentials (Hostinger provides them in hPanel).
4. **Database & storage**
   ```bash
   php artisan migrate --force
   php artisan storage:link
   ```
5. **Build assets**
   - If Node.js 20 is available on Hostinger:
     ```bash
     npm ci
     npm run build
     ```
   - If Node is unavailable, run `npm run build` locally and copy the `public/build` directory via `scp` or Hostinger’s file manager.
6. **Optimize Laravel**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```
7. **Permissions**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```
8. **Cron / Scheduler**
   In hPanel → Advanced → Cron Jobs, add:
   ```
   * * * * * php /home/<user>/domains/<domain>/public_html/app/artisan schedule:run >> /home/<user>/logs/cron.log 2>&1
   ```
   For queues on shared hosting preferably use `database` driver and run:
   ```
   php artisan queue:work --tries=3
   ```
   via a second cron job.

---

### 6. Useful Commands
| Command | Purpose |
| --- | --- |
| `php artisan down --render="errors::503"` | Maintenance mode |
| `php artisan up` | Exit maintenance |
| `php artisan migrate --force` | Run pending migrations |
| `php artisan config:clear` | Clear config cache |

---

### 7. Troubleshooting
- **White screen / 500** → check `storage/logs/laravel.log`.
- **Permission denied** → ensure the PHP user owns `storage` & `bootstrap/cache`.
- **Git pull prompts for password** → the deploy key is missing or not added as read-only in GitHub.
- **Payment gateway errors** → verify the `ATOM_*` keys in `.env` match the environment selected in `ATOM_ENV`.

Your Hostinger server now mirrors GitHub. To release new code:
```bash
ssh <user>@<hostinger-server>
cd ~/domains/<domain>/public_html/app
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build   # or upload new public/build
php artisan optimize
```

Keep GitHub as the single source of truth; never edit production files manually without committing.

