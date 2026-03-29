# External Integrations

**Analysis Date:** 2026-03-27

## APIs & External Services

**None.** This application does not consume any external APIs, SDKs, or third-party services. All functionality is self-contained.

## Data Storage

**Database:**
- MySQL (via Laragon locally)
  - Connection: Hardcoded credentials in `includes/db_connect.php`, `admin/db_connect.php`, `admin/config.php`
  - Client: PHP `mysqli` extension (no ORM)
  - Database name: `eswasa`
  - Host: `localhost`

**File Storage:**
- Local filesystem only
  - Upload directory: `admin/uploads/`
  - Contains: banner images, announcement files, event images, team photos
  - No cloud storage (no S3, no Azure Blob, no CDN for uploaded assets)

**Caching:**
- None

## CDN Dependencies

**Admin Panel loads from CDNs:**
- Bootstrap 5.3.0 CSS/JS: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/`
  - Used in: `admin/includes/header.php`, `admin/includes/footer.php`
- Bootstrap 5.1.3 CSS: `https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/`
  - Used in: `admin/login.php`
- Font Awesome 6.4.0: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/`
  - Used in: `admin/includes/header.php`

**Public Site:**
- No CDN dependencies. All assets served locally from `assets/` and `rs-plugin/` directories.

## Authentication & Identity

**Auth Provider:** Custom (session-based)
- Implementation: PHP native sessions with `password_verify()` for bcrypt password checking
- Session variables: `$_SESSION['admin_logged_in']`, `$_SESSION['user_id']`, `$_SESSION['user_role']`, `$_SESSION['username']`
- Login endpoint: `admin/login.php`
- Auth guard: `requireLogin()` in `admin/config.php`
- No OAuth, no SSO, no third-party identity provider

## Email

**Outbound Email:**
- PHP `mail()` function — native, no external service
  - Used in: `contact.php` (line 46)
  - Recipient: `info@swasa.co.sz`
  - Format: HTML email with contact form data
  - No SMTP configuration, no PHPMailer, no SendGrid/Mailgun/SES
  - Depends on server-level mail configuration (sendmail/postfix)

## Monitoring & Observability

**Error Tracking:**
- None. No Sentry, Bugsnag, or similar.

**Logs:**
- Admin panel logs PHP errors to `admin/error.log`
- Public site displays errors directly (not recommended for production)
- No structured logging, no log aggregation

**Analytics:**
- None detected. No Google Analytics, no Matomo, no tracking scripts in header/footer.

## CI/CD & Deployment

**Hosting:**
- Not determined. No deployment configuration files present.
- Development runs on Laragon (Windows local LAMP stack).

**CI Pipeline:**
- None. No `.github/workflows/`, no `.gitlab-ci.yml`, no `Jenkinsfile`, no deployment scripts.

**Version Control:**
- No `.git` directory detected in the project root.

## Environment Configuration

**Required configuration (all hardcoded, no env vars):**
- MySQL database `eswasa` must exist with all required tables
- MySQL user `root` with empty password (development default)
- PHP `mail()` must be functional for contact form
- `admin/uploads/` directory must be writable by web server

**Secrets location:**
- Hardcoded directly in PHP files (not in env vars or secret stores):
  - `includes/db_connect.php` — DB credentials
  - `admin/db_connect.php` — DB credentials (duplicate)
  - `admin/config.php` — DB credentials

## Webhooks & Callbacks

**Incoming:**
- None

**Outgoing:**
- None

## Third-Party Frontend Libraries (Vendored, Not Integrations)

These are static files bundled with the project, not live integrations:
- jQuery 3.6.0 — `assets/js/vendor/jquery-3.6.0.min.js`
- Bootstrap (unknown version) — `assets/css/bootstrap.min.css`, `assets/js/bootstrap.min.js`
- Revolution Slider — `rs-plugin/js/jquery.themepunch.revolution.min.js`
- Slick Carousel — `assets/js/slick.min.js`
- AOS (Animate On Scroll) — `assets/js/aos.js`
- Magnific Popup — `assets/js/jquery.magnific-popup.min.js`
- Isotope — `assets/js/isotope.pkgd.min.js`
- Select2 — `assets/js/select2.min.js`
- GSAP TweenMax — `assets/js/tween-max.min.js`
- Font Awesome — `assets/css/fontawesome-all.min.css` + `assets/fonts/`

---

*Integration audit: 2026-03-27*
