# Architecture

**Analysis Date:** 2026-03-27

## Pattern Overview

**Overall:** Traditional PHP page-per-route website with a separate admin panel using a front-controller pattern.

**Key Characteristics:**
- No framework; vanilla PHP with MySQLi for database access
- Public site: each `.php` file in the root is a standalone page (file-based routing)
- Admin panel: single entry point (`admin/index.php`) loads pages via `?page=` query parameter from a whitelist
- Shared layout fragments via `include`/`require` (header, footer, sidebar)
- Bootstrap 5 for UI on both public and admin sides
- No ORM, no templating engine, no dependency manager (no Composer)

## Layers

**Public Frontend (root `/`):**
- Purpose: Serve public-facing pages for the Eswatini Standards Authority website
- Location: `/*.php` (root-level PHP files)
- Contains: Full HTML pages that each include `includes/header.php` and `includes/footer.php`
- Depends on: `includes/db_connect.php` for database, `assets/` for CSS/JS/images
- Used by: End users / site visitors

**Admin Panel (`admin/`):**
- Purpose: Content management system for editing site content, managing events, vacancies, publications, FAQs, announcements, banners, and contact messages
- Location: `admin/`
- Contains: Front controller (`admin/index.php`), page modules (`admin/pages/*.php`), layout includes (`admin/includes/`)
- Depends on: `admin/config.php` for DB connection + auth helpers, Bootstrap 5 CDN
- Used by: Authenticated admin users

**Shared Includes (`includes/`):**
- Purpose: Reusable layout fragments and database connection for the public site
- Location: `includes/`
- Contains: `header.php`, `footer.php`, `db_connect.php`, `main.css`
- Depends on: Nothing (leaf dependency)
- Used by: All public-facing pages

**Static Assets (`assets/`):**
- Purpose: Frontend CSS, JS, fonts, and images for the public site
- Location: `assets/`
- Contains: Bootstrap, FontAwesome, jQuery plugins, AOS, Slick, Magnific Popup, custom CSS/JS
- Depends on: Nothing
- Used by: Public frontend pages via `includes/header.php`

**Revolution Slider (`rs-plugin/`):**
- Purpose: Slider/carousel plugin assets for the homepage
- Location: `rs-plugin/`
- Contains: CSS, JS, fonts, images for the slider component
- Used by: `index.php` homepage

## Data Flow

**Public Page Request (e.g., `about-us.php`):**

1. Browser requests `/about-us.php`
2. Page includes `includes/db_connect.php` to get `$conn` (MySQLi)
3. Page queries database directly (e.g., `page_content` table) using prepared statements
4. Page outputs full HTML document, including header/footer via `include`

**Admin Page Request (e.g., `index.php?page=events_edit.php`):**

1. Browser requests `admin/index.php?page=events_edit.php`
2. `admin/index.php` loads `admin/config.php` (DB + session + auth helpers)
3. `requireLogin()` checks session; redirects to `login.php` if unauthenticated
4. Page name is validated against `$allowed_pages` whitelist array
5. Target page file (`admin/pages/events_edit.php`) is loaded via `ob_start()` / `include` / `ob_get_clean()`
6. Layout is assembled: `header.php` -> `sidebar.php` -> `$content` -> `footer.php`

**Form Submission (Admin CRUD):**

1. Admin page PHP file checks `$_SERVER['REQUEST_METHOD'] === 'POST'`
2. Validates input, handles file uploads to `admin/uploads/`
3. Executes INSERT/UPDATE/DELETE via MySQLi prepared statements
4. Calls `set_flash('success', '...')` to set session flash message
5. Calls `redirect_self()` (POST-Redirect-GET pattern) to prevent resubmission

**Contact Form Submission (Public):**

1. `contact.php` handles POST at the top of the file
2. Validates inputs, inserts into `eswasa_contact_messages` table
3. Sends email via PHP `mail()` function to `info@swasa.co.sz`
4. Redirects to `contact.php?success=1`

**State Management:**
- Server-side sessions (`$_SESSION`) for authentication and flash messages
- No client-side state management beyond standard form inputs
- Session keys: `user_id`, `admin_logged_in`, `user_role`, `username`, `flash`

## Key Abstractions

**Page Content (Key-Value CMS):**
- Purpose: Stores editable text/image paths for static pages (about, services, certification, etc.)
- Database table: `page_content` with columns `page_key` and `content`
- Pattern: Pages query by key prefix (e.g., `about_*`), admin pages update by key
- Used by: `about-us.php`, `admin/pages/about_edit.php`, `admin/pages/certification_edit.php`, `admin/pages/services_edit.php`, `admin/pages/calibration_edit.php`, `admin/pages/training_edit.php`, `admin/pages/standards_edit.php`

**CRUD Entities (Dedicated Tables):**
- Purpose: Structured content with full CRUD operations
- Tables: `eswasa_events`, `eswasa_vacancies`, `eswasa_announcements`, `eswasa_publications`, `eswasa_faq`, `eswasa_contact_messages`
- Pattern: Each entity has an `admin/pages/{name}_edit.php` file handling list + create + update + delete

**Banners/Slides:**
- Purpose: Homepage carousel images with captions and URLs
- Table: `banners` (columns: `id`, `file`, `caption`, `url`, `updated_by`, `date_updated`)
- Managed in: `admin/pages/dashboard.php`

**Site Statistics:**
- Purpose: Numeric counters displayed on the homepage (e.g., standards count)
- Table: `site_statistics` (columns: `id`, `stat_key`, `stat_label`, `stat_value`)
- Managed in: `admin/pages/dashboard.php`

## Database Tables

Identified from SQL queries across the codebase:

| Table | Purpose | Managed By |
|-------|---------|-----------|
| `page_content` | Key-value CMS content | `admin/pages/about_edit.php`, `*_edit.php` for static pages |
| `banners` | Homepage slider images | `admin/pages/dashboard.php` |
| `site_statistics` | Homepage numeric counters | `admin/pages/dashboard.php` |
| `events` | Events (old table, used by `index.php`) | - |
| `eswasa_events` | Events (primary table) | `admin/pages/events_edit.php` |
| `eswasa_vacancies` | Job vacancies | `admin/pages/vacancies_edit.php` |
| `eswasa_announcements` | News/announcements | `admin/pages/announcements_edit.php` |
| `eswasa_publications` | Downloadable publications | `admin/pages/publications_edit.php` |
| `eswasa_faq` | FAQ entries | `admin/pages/faq_edit.php` |
| `eswasa_contact_messages` | Contact form submissions | `admin/pages/contact_edit.php` |
| `users` | Admin user accounts | `admin/login.php`, `admin/config.php` |

## Entry Points

**Public Homepage:**
- Location: `index.php`
- Triggers: Root URL request
- Responsibilities: Loads banners, statistics, and recent events; renders the homepage

**Admin Front Controller:**
- Location: `admin/index.php`
- Triggers: Any admin URL request
- Responsibilities: Auth check, page whitelist validation, layout assembly, content rendering

**Admin Login:**
- Location: `admin/login.php`
- Triggers: Unauthenticated admin access
- Responsibilities: Username/password authentication against `users` table using `password_verify()`

**Individual Public Pages:**
- Location: `about-us.php`, `events.php`, `contact.php`, `vacancies.php`, `announcements.php`, `publications.php`, `faq.php`, `services.php`, `Standards.php`, `Certification.php`, `Calibration.php`, `training-about.php`, `training-calendar.php`, `news.php`, `board.php`, `Meetourteam.php`, `product.php`, `qoute.php`, `qoute_certification.php`, `qoute_training.php`, etc.
- Triggers: Direct URL access (file-based routing)
- Responsibilities: Each page is self-contained; queries DB, outputs full HTML

## Routing

**Public Site:** File-based routing. Each `.php` file in the root directory maps directly to a URL path:
- `/index.php` -> Homepage
- `/about-us.php` -> About page
- `/events.php` -> Events listing
- `/event-details.php?id=N` -> Single event detail
- `/contact.php` -> Contact form
- No `.htaccess` or URL rewriting detected

**Admin Panel:** Query-parameter routing via front controller:
- `admin/index.php?page=dashboard.php` -> Dashboard
- `admin/index.php?page=events_edit.php` -> Events management
- `admin/index.php?page=events_edit.php&edit=5` -> Edit event #5
- `admin/index.php?page=events_edit.php&delete=5` -> Delete event #5
- Page whitelist enforced in `admin/index.php` via `$allowed_pages` array

## Authentication & Authorization

**Strategy:** Session-based authentication with no role-based access control enforcement

**Login Flow:**
1. `admin/login.php` accepts POST with `username` and `password`
2. Queries `users` table for matching username
3. Verifies password using `password_verify()` (bcrypt hashed passwords)
4. Sets session variables: `admin_logged_in`, `user_id`, `user_role`, `username`
5. Redirects to `admin/index.php?page=dashboard.php`

**Auth Guard:**
- `admin/config.php` defines `requireLogin()` which checks `$_SESSION['user_id']`
- `admin/index.php` calls `requireLogin()` before loading any page
- All admin include files check `defined('ESWASA_ADMIN')` to prevent direct access

**Logout:**
- `admin/logout.php` calls `session_destroy()` and redirects to `login.php`

## Error Handling

**Strategy:** Mixed approach; no centralized error handler

**Patterns:**
- Public pages use `die()` on query failures (e.g., `die("Banner query failed: " . mysqli_error($conn))`)
- Admin `index.php` logs errors to `admin/error.log` and hides display errors from users
- Flash messages (`set_flash()`) communicate success/failure for admin CRUD operations
- Form validation is inline per-page; no shared validation library

## Cross-Cutting Concerns

**Logging:** Error logging only in admin via `ini_set('error_log', __DIR__ . '/error.log')`. No application-level logging.

**Validation:** Inline per-page. No shared validation functions. Each form handler validates its own inputs with basic checks (empty, email format, file type).

**Authentication:** Session-based via `admin/config.php` helpers (`isLoggedIn()`, `requireLogin()`, `getCurrentUser()`). Public pages have no auth.

**File Uploads:** Handled inline in each admin page. Files saved to `admin/uploads/`. Image validation checks extension and MIME type. No shared upload utility (except `handle_image_upload()` in `admin/pages/about_edit.php` which is local to that file).

**CSRF Protection:** None detected. Forms do not include CSRF tokens.

**Database Connection:** Two separate connection files with identical credentials:
- `includes/db_connect.php` (public site, procedural MySQLi)
- `admin/config.php` (admin panel, procedural MySQLi with `set_charset("utf8mb4")`)
- `admin/db_connect.php` (duplicate of `includes/db_connect.php`, appears unused since `config.php` handles admin DB)

---

*Architecture analysis: 2026-03-27*
