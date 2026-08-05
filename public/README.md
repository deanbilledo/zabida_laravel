# ZABIDA Laravel Application Layer (Overlay)

This is **not** a runnable Laravel project by itself — it's the custom
application code for ZABIDA, meant to be copied on top of a fresh Laravel
skeleton. Full instructions: **SETUP_AND_DEPLOYMENT_GUIDE.md** (in the
parent folder of this zip).

Quick version:

```bash
composer create-project laravel/laravel zabida "^10.0"
cd zabida
# then copy this overlay's routes/, app/, database/, resources/, public/assets/,
# and .env.example over the fresh project (merge, don't wholesale replace)
cp .env.example .env
php artisan key:generate
# edit .env: DB + FACEBOOK_PAGE_ID + FACEBOOK_PAGE_TOKEN
php artisan migrate
php artisan db:seed --class=AdminUserSeeder   # change the seeder's password first!
php artisan storage:link
php artisan serve
```

## What's implemented

- **Public site** — home, programs, activities/journal, contact (with
  honeypot spam guard + visible loading/success/error states), single post
  view.
- **PeaceWorks and Knowledge Products** — the PDF archive. Admin uploads
  PDFs (`storage/app/publications`, a *private* disk — never directly
  linkable). Public visitors click "Read" for an inline popup viewer, or
  download.
- **Facebook Graph API sync** (`app/Services/FacebookGraphService.php`) —
  pulls Page posts with full `attachments{subattachments{...}}` expansion,
  so every photo in a multi-image album/repost is imported (not just the
  first), plus native video via `media.source`. Photos are downloaded and
  stored locally (Facebook's CDN URLs expire; ours don't). Runs hourly via
  `app/Console/Kernel.php`, or on demand from `/admin/facebook-sync`.
- **Admin panel** — journal posts and publications CRUD, sign-in/sign-out
  (the original's broken sign-out is fixed as a real CSRF-protected POST
  route with session invalidation — see `AdminLoginController::logout()`),
  all with visible loading and error states on every form.
- **No visible `index.php`/`.php` in any URL** — automatic, standard
  Laravel routing (see the guide for why nothing extra was needed for this).
- **Security notes**: PDF storage is on a private disk, not public/;
  uploads are validated by MIME type and size; login and contact-form
  routes are rate-limited (`throttle:5,1`); CSRF protection is Laravel's
  default on every POST route; `.env` (DB credentials, Facebook token)
  never ships in the repo — only `.env.example` does.

## What still needs your input before going live

1. `database/seeders/AdminUserSeeder.php` — change the placeholder
   email/password before seeding.
2. `.env` — real Facebook Page ID/token, database credentials, mail
   settings (contact form currently logs submissions; wire up a real
   Mailable + Bluehost SMTP when ready — `ContactController::submit()`).
3. Real images already copied into `public/assets/images/` from the
   original site — double check nothing is missing after you merge.
