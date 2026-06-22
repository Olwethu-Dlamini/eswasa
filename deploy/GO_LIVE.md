# ESWASA — First-Time Production Go-Live Guide

This is the end-to-end runbook for putting the site live on a production host
for the first time. The site itself is unchanged in look and behaviour — this
only covers configuration, the database, file upload, and trimming dead weight.

Date prepared: 2026-06-22.

---

## 0. What's already been done in the code (this round)

You don't need to touch these — they ship in the bundle:

- **Errors no longer leak to visitors.** All 13 public pages used to force
  `display_errors = 1`. They now route through `includes/env.php`, which hides
  errors in production and logs them instead. Your local machine still shows
  errors automatically (it auto-detects `localhost` / `*.test` / LAN IPs as
  development).
- **DB credentials are in one place** — `includes/env.php`. The three old
  connection files (`includes/db_connect.php`, `admin/db_connect.php`,
  `admin/config.php`) all read from it now.
- **Front-end connection now uses `utf8mb4`** (matches admin), reducing the
  character-mangling risk you've hit before.
- **Banner links** that pointed at the old staging domain
  (`http://demo.swasa.co.sz/...`) are now domain-relative. Patch SQL:
  `deploy/patch_banner_urls_2026_06_22.sql`.

---

## 1. Create the production database + user

On the host (cPanel/phpMyAdmin or shell):

1. Create a database, e.g. `eswasa`.
2. Create a **dedicated MySQL user** (NOT root) with a strong password and grant
   it all privileges on that database only.

> Keep the user/password handy for step 3.

---

## 2. Export the local DB **as UTF-8** and import it

⚠️ **Critical — this is the bug that has bitten this project twice.** A
`mysqldump` run from a Windows console using the CP850 code page mangles every
special character (`—`, `≤`, `½`, …). Export as `utf8mb4`:

**Option A — phpMyAdmin (simplest):** Export tab → format SQL → ensure charset
is `utf8mb4` → Go.

**Option B — shell:**
```
mysqldump --default-character-set=utf8mb4 -u root eswasa > eswasa_prod.sql
```

Then import into the production DB (phpMyAdmin Import, or
`mysql -u <produser> -p <db> < eswasa_prod.sql`).

**After import, run these idempotent patches** (safe to re-run):
```
deploy/patch_fix_mojibake_2026_06_17.sql       # repairs any legacy mojibake
deploy/patch_banner_urls_2026_06_22.sql        # staging-domain banner links
deploy/migration_activity_log_2026_06_22.sql   # creates the audit-trail table
```
Quick check (both should return 0):
```sql
SELECT COUNT(*) FROM page_content WHERE content LIKE BINARY CONCAT('%', 0xC394, '%');
SELECT COUNT(*) FROM banners WHERE url LIKE '%demo.swasa.co.sz%';
```

---

## 3. Point the app at the production database

**Preferred — environment variables** (set in cPanel "Environment Variables",
or your host panel). Nothing in code to edit:
```
ESWASA_DB_HOST = localhost          # or the host's DB server
ESWASA_DB_USER = <your prod user>
ESWASA_DB_PASS = <your prod password>
ESWASA_DB_NAME = <your prod db name>
APP_ENV        = production          # optional; auto-detected anyway
```

**If your host can't set env vars** — edit `includes/env.php` after upload and
replace the fallback values (the right-hand side of each `getenv(...) ?: '...'`
line) with your production credentials. One file, four lines.

---

## 4. Build and upload the file bundle

From the repo root on your machine:
```
powershell -ExecutionPolicy Bypass -File deploy\build_deploy_bundle.ps1
```
This creates `..\eswasa_deploy_bundle\` — a clean copy containing only what the
live site needs (it does **not** touch your working folder). It excludes dev
folders (`.git`, `node_modules`, `scripts`, `docs`, `deploy`, audit dirs) and 51
orphaned root files (old design mockups, screenshots, presentations, brochures).

**Result: ~110 MB vs ~519 MB — about 80% smaller / faster to upload.**

Upload the **contents** of `eswasa_deploy_bundle\` to your web root (the folder
where `index.php` should live), preserving the folder structure.

> The SQL migrations in `deploy/` are intentionally NOT uploaded (they aren't
> web-served). Run them from your local repo against the production DB.

---

## 5. File permissions

Make the uploads area writable by the web server (so the CMS can save media):
```
admin/uploads/                 chmod 755 (dirs) / 644 (files)
admin/uploads/quotes/          # auto-created on first quote; pre-create if fussy
admin/uploads/tenders/
admin/uploads/announcements/
admin/uploads/publications/
```

---

## 6. Secure the admin account

The seeded login is `admin` / `admin@example.com` with a known password —
change it. Generate a bcrypt hash and update the row:

```
php -r "echo password_hash('YOUR-NEW-STRONG-PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
```
```sql
UPDATE users
SET password = '<paste the hash>', email = 'real-admin@eswasa.co.sz'
WHERE username = 'admin';
```

---

## 7. Recommended hardening (optional but advised)

- **HTTPS**: install a certificate (Let's Encrypt / host-provided) and force
  redirect HTTP → HTTPS.
- **Protect sensitive files** (Apache): a root `.htaccess` to block direct web
  access to includes and SQL:
  ```apache
  <FilesMatch "\.(sql|md|log)$">
      Require all denied
  </FilesMatch>
  RedirectMatch 404 ^/includes/
  ```
  (Skip if your host isn't Apache or `AllowOverride` is off.)

---

## 7b. Post-launch config (new admin/SEO features)

- **Analytics**: Admin → **Site Settings** → paste your Google Analytics 4 ID
  (`G-XXXXXXXXXX`). Tracking turns on site-wide immediately; blank = off.
- **Audit trail**: Admin → **Activity Log** now records logins, user changes and
  content edits (needs the `activity_log` migration from §2).
- **Domain in SEO files**: `robots.txt`, `sitemap.xml`, and the homepage
  canonical/Open Graph URLs use `https://www.eswasa.co.sz/`. If the live domain
  differs, find-and-replace that host in those three places.
- **Submit the sitemap**: in Google Search Console, add the property and submit
  `https://<your-domain>/sitemap.xml`.
- **.htaccess**: ships in the bundle and works automatically on Apache (gzip,
  caching, file protection). No action needed; remove a block only if your host
  errors on it.

## 8. Smoke test (do this right after go-live)

1. Home page renders exactly as before, banners click through correctly.
2. No PHP errors/warnings visible anywhere on the public site.
3. Special characters display correctly (em dashes, `≤`, durations) — confirms
   the UTF-8 export worked.
4. Sign in at `admin/login.php` with the new password.
5. Edit one CMS field, save, refresh the public page — change appears.
6. Submit a public quote form — it lands in the admin quote inbox.
7. On managementsystems.php, the certification document PDFs open.

---

## Appendix A — What was excluded and why

| Bucket | Examples | Size |
|--------|----------|------|
| Dev/build dirs | `.git`, `node_modules`, `scripts`, `docs`, audit dirs, `deploy` | ~343 MB |
| Orphaned root mockups | `ingelo land.PNG`, `certification breadcrumb.PNG`, `prod land.PNG` … (replaced by CMS uploads — 0 DB references) | ~20 MB |
| Dev screenshots | `screenshot_*.png` (×10) | ~9 MB |
| Presentations / brochures | `*.pptx` (×2), `*.pub`, `Booklet final.pdf`, `standards catalogue price.pdf` | ~38 MB |
| Stray notes / dev js / npm | `*_text.txt`, `prompt for cms.txt`, `inspect.js`, `measure.js`, `package*.json` | small |

**Kept at root** (verified referenced by code or the live DB): all `*.php`,
`about core.jpg`, the five `iso*.png`, `haccp.png`, `whycertify.webp`, and the
11 certification PDFs in the `certification_documents` table.

## Appendix B — Possible follow-up cleanup (not done here)

`admin/uploads/` is ~85 MB, ~134 loose files. Some may be superseded CMS
uploads (old banner/breadcrumb/about versions). Trimming these needs a careful
cross-check against every table that stores upload paths (team photos, board,
publications, events, tenders, announcements). Worth a dedicated pass later if
you want to reclaim more — it was left untouched to avoid breaking live media.
