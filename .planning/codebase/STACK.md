# Technology Stack

**Analysis Date:** 2026-03-27

## Languages

**Primary:**
- PHP (procedural, no framework) - All application logic, frontend pages, and admin panel
- HTML5 - Page markup embedded in PHP files

**Secondary:**
- CSS3 - Styling via pre-built libraries and custom `main.css`
- JavaScript (ES5/jQuery) - Client-side interactivity

## Runtime

**Environment:**
- PHP (version not pinned; runs on Laragon local dev server with Apache/Nginx)
- No `.php-version` or `composer.json` present — no dependency management

**Package Manager:**
- None. No `composer.json`, `package.json`, or any package manifest exists.
- All dependencies are vendored as static files in `assets/css/`, `assets/js/`, and `rs-plugin/`.

## Frameworks

**Core:**
- None. This is a vanilla PHP application with no MVC framework (no Laravel, Symfony, CodeIgniter, etc.)

**CSS Framework:**
- Bootstrap (version not pinned in local file; `assets/css/bootstrap.min.css` for public site)
- Bootstrap 5.3.0 via CDN for admin panel (`https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css`)
- Bootstrap 5.1.3 via CDN for admin login page (`https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css`)

**JavaScript:**
- jQuery 3.6.0 - `assets/js/vendor/jquery-3.6.0.min.js`
- Bootstrap 5 JS Bundle (CDN in admin)

**Testing:**
- None. No test framework, no test files detected.

**Build/Dev:**
- None. No build tools (no Webpack, Vite, Gulp, or similar). All assets are pre-compiled static files.

## Key Dependencies (Vendored Libraries)

**Frontend Public Site (`assets/js/`):**
- `jquery-3.6.0.min.js` - DOM manipulation and plugin base
- `bootstrap.min.js` - UI components (modals, dropdowns, collapse)
- `slick.min.js` + `slick-animation.min.js` - Carousel/slider
- `aos.js` - Animate On Scroll library
- `wow.min.js` - Scroll-triggered animations
- `jquery.magnific-popup.min.js` - Lightbox popups
- `jquery.odometer.min.js` - Animated number counters
- `isotope.pkgd.min.js` - Masonry/filtering grid layouts
- `imagesloaded.pkgd.min.js` - Image load detection (pairs with Isotope)
- `select2.min.js` - Enhanced select dropdowns
- `tween-max.min.js` - GSAP TweenMax animation engine
- `tg-cursor.min.js` - Custom cursor effects
- `jquery.appear.js` - Element visibility detection
- `form-contact.js` - Contact form handling
- `main.js` - Site-wide custom JavaScript

**Frontend Public Site (`assets/css/`):**
- `bootstrap.min.css` - Grid and component styles
- `animate.min.css` - CSS animation library
- `aos.css` - AOS animation styles
- `fontawesome-all.min.css` - Font Awesome icons (self-hosted fonts in `assets/fonts/`)
- `magnific-popup.css` - Lightbox styles
- `select2.min.css` - Select2 dropdown styles
- `odometer.css` - Odometer counter styles
- `slick.css` - Slick carousel styles
- `spacing.css` - Utility spacing classes
- `tg-cursor.css` - Cursor effect styles
- `extralayers.css` - Revolution Slider extra layers
- `main.css` - Primary custom stylesheet

**Revolution Slider (`rs-plugin/`):**
- `jquery.themepunch.revolution.min.js` - Revolution Slider core
- `jquery.themepunch.plugins.min.js` - Revolution Slider plugins
- `rs-plugin/css/settings.css` - Slider settings stylesheet

**Admin Panel:**
- Bootstrap 5.3.0 (CDN) - `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css`
- Font Awesome 6.4.0 (CDN) - `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css`
- `admin/css/style.css` - Custom admin styles
- `admin/js/main.js` - Custom admin JavaScript

**Fonts:**
- Font Awesome (self-hosted: `.ttf`, `.woff2` in `assets/fonts/`)
- VAG Rounded Std Thin (custom font: `.eot`, `.ttf` in `assets/fonts/`)

## Data Storage

**Database:**
- MySQL (via Laragon) — database name: `eswasa`
- Connection: `mysqli` extension (procedural and OOP styles mixed)
- Character set: `utf8mb4` (set in `admin/config.php`)
- No ORM, no query builder — raw SQL throughout

**Database Tables Detected (from SQL queries):**
- `banners` - Homepage slider banners (columns: `id`, `file`, `caption`, `url`, `updated_by`, `date_updated`)
- `site_statistics` - Homepage stat counters (columns: `id`, `stat_key`, `stat_label`, `stat_value`)
- `events` / `eswasa_events` - Events listing (columns: `id`, `title`, `description`, `location`, `event_date`, `category`, `image`)
- `page_content` - Generic CMS content (columns: `page_key`, `content`, `updated_at`)
- `users` - Admin users (columns: `id`, `username`, `email`, `password`, `role`)
- `eswasa_contact_messages` - Contact form submissions (columns: `name`, `email`, `phone`, `subject`, `message`)
- `eswasa_announcements` - Announcements (columns: `id`, `title`, `description`, `announcement_type`, `published_date`, `external_link`, `file_path`)
- `eswasa_vacancies` - Job vacancies (columns: `id`, `title`, `location`, `closing_date`, `description`, `responsibilities`)
- `eswasa_faq` - FAQ items (columns: `id`, `question`, `answer`, `category`, `sort_order`)
- `eswasa_publications` - Publications (referenced in `admin/pages/publications_edit.php`)

**File Storage:**
- Local filesystem only. Uploaded files go to `admin/uploads/` directory.
- Banner images, announcement files, event images stored as files with paths in DB.

**Caching:**
- None.

## Database Connection Files

There are two duplicate database connection files:
- `includes/db_connect.php` - Used by public-facing pages
- `admin/db_connect.php` - Identical copy, used by some admin code
- `admin/config.php` - Third connection with added auth helper functions, sets `utf8mb4` charset

All three connect to `localhost` / `root` / no password / database `eswasa`.

## Email

**Method:** PHP `mail()` function (no SMTP library, no PHPMailer)
- Used in `contact.php` line 46 to send contact form submissions
- Sends HTML-formatted email to `info@swasa.co.sz`

## Authentication

**Admin Auth:**
- Session-based (`$_SESSION['admin_logged_in']`, `$_SESSION['user_id']`, `$_SESSION['user_role']`)
- Password hashing via `password_verify()` (bcrypt) — `admin/login.php`
- Auth check via `requireLogin()` function in `admin/config.php`
- Page whitelist in `admin/index.php` (only allowed pages can be loaded)
- Direct access protection via `ESWASA_ADMIN` constant check in admin includes

**Public Site Auth:**
- None. Public pages are unauthenticated.

## Configuration

**Environment:**
- No `.env` file. Database credentials are hardcoded in PHP files.
- `includes/db_connect.php` — public site DB connection
- `admin/config.php` — admin DB connection + auth functions
- `admin/db_connect.php` — duplicate DB connection

**Error Handling:**
- Public site: `error_reporting(E_ALL)` with `display_errors = 1` in `index.php` (errors shown to users)
- Admin panel: `display_errors = 0`, `log_errors = 1`, logs to `admin/error.log`

## Platform Requirements

**Development:**
- Laragon (Windows) with Apache/Nginx + PHP + MySQL
- No specific PHP version requirement detected
- No `.htaccess` file present

**Production:**
- Any LAMP/LEMP stack (Apache or Nginx + PHP + MySQL)
- PHP `mail()` function must be configured for contact form
- `mysqli` extension required
- File upload support required (for `admin/uploads/`)
- No CI/CD pipeline detected
- No deployment configuration files present

## Key Observations

- **No package management at all** — every dependency is a vendored static file
- **No build pipeline** — CSS and JS are pre-compiled/minified vendor files plus hand-written custom files
- **No version pinning** — library versions are unknown for most vendored assets (only jQuery version identifiable from filename)
- **Bootstrap version mismatch** — admin login uses 5.1.3, admin panel uses 5.3.0, public site uses unknown version (local file)
- **Template approach** — ThemeGhost-style HTML template converted to PHP with `includes/header.php` and `includes/footer.php` shared partials

---

*Stack analysis: 2026-03-27*
