# Deploy — 2026-05-25 session

This bundles 9 commits since `ebd822c` into one drag-and-drop deploy.

## 1. Upload these files (overwrite if exists)

Preserve folder structure. All paths relative to web root (where `index.php` lives).

### New files
```
includes/cms_helpers.php
process_quote.php
admin/CMS_AGENT_TEMPLATE.md
admin/pages/_quote_inbox.php
admin/pages/board_edit.php
admin/pages/cert_status_edit.php
admin/pages/index_edit.php
admin/uploads/eswasa_standards_catalogue_latest.pdf
admin/uploads/haccp.png
admin/uploads/ingelo_application_form.pdf
admin/uploads/iso9001.png
admin/uploads/iso14001.png
admin/uploads/iso22000.png
admin/uploads/iso45001.png
admin/uploads/whycertify.webp
```

### Modified files (replace server copy)
```
admin/css/style.css
admin/js/main.js
admin/index.php
admin/login.php
admin/includes/header.php
admin/includes/sidebar.php
admin/pages/about_edit.php
admin/pages/about_team.php
admin/pages/announcements_edit.php
admin/pages/breadcrumbs_edit.php
admin/pages/calibration_edit.php
admin/pages/certification_edit.php
admin/pages/contact_edit.php
admin/pages/events_edit.php
admin/pages/faq_edit.php
admin/pages/ingelo.php
admin/pages/login.php
admin/pages/managementsystems.php
admin/pages/product.php
admin/pages/publications_edit.php
admin/pages/purchase.php
admin/pages/qoute_calibration.php
admin/pages/qoute_certification.php
admin/pages/qoute_training.php
admin/pages/services_edit.php
admin/pages/standards_edit.php
admin/pages/tcp.php
admin/pages/training_about.php
admin/pages/training_calendar.php
admin/pages/vacancies_edit.php
admin/pages/work.php
board.php
Calibration.php
Certification.php
certification-status.php
contact.php
index.php
ingelo.php
managementsystems.php
Meetourteam.php
product.php
purchase.php
services.php
Standards.php
tcp.php
training-about.php
training-calendar.php
work.php
```

## 2. Delete these files from the server
```
news.php
board.php
admin/pages/dashboard.php
admin/pages/posts.php
admin/pages/training_edit.php
admin/pages/board_edit.php
```

## 3. Make sure this directory exists and is writable
```
admin/uploads/quotes/
```
(Will be auto-created on first quote upload, but pre-create with `chmod 755` if your host is fussy.)

## 4. Run the SQL migration
Apply `deploy/migration_2026_05_25.sql` to the production database. Either:
- phpMyAdmin → Import tab → choose the file
- Or shell: `mysql -u <user> -p <db> < migration_2026_05_25.sql`

Safe to re-run — uses `CREATE TABLE IF NOT EXISTS` and `REPLACE INTO`.

The migration does three things:
1. Creates `eswasa_quote_requests` table (new — backs the quote inbox)
2. Deletes obsolete `page_content` rows (news_*, breadcrumb_bg_news, implementation_info)
3. Replaces / inserts ~944 `page_content` rows so the front-end and admin reflect the same content

## 5. Smoke test after deploy
- Visit the live home page — should render exactly as before
- Sign in to `admin/login.php`
- Land on **Home Page** editor — should show banners + Discover/Marks/Affiliations with prefilled values
- Edit one field, save, refresh public page — change should appear
- Submit the public Training quote form — should land in admin Training Quote Requests

## What changed in 9 commits (newest first)

```
959fcda feat(quotes): build quote-request pipeline + delete posts stub
5b9fa7a fix(admin): backend audit cleanup
bb0c2bc fix(uploads): copy missing scheme/cert images into admin/uploads/
ff44376 refactor(admin): merge Home Sections + Banner Slider, drop Statistics
f4dd24d chore(admin): remove dashboard, land on Home Page editor after login
dd035b7 chore: retire news.php front-end + wipe news_* keys
c8a647a chore(admin): remove News editor — no news section on the site
ffb1d07 fix(admin): strip HTML on save across 8 editors — text-only inputs
1aa5371 feat(cms): wire 18 front-end pages through page_content + admin editors
```
