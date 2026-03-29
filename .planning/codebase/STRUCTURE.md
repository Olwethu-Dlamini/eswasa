# Codebase Structure

**Analysis Date:** 2026-03-27

## Directory Layout

```
eswasa/
├── admin/                  # Admin panel (CMS)
│   ├── css/                # Admin-specific styles
│   │   └── style.css
│   ├── includes/           # Admin layout fragments
│   │   ├── header.php      # Admin navbar + HTML head
│   │   ├── sidebar.php     # Admin navigation sidebar
│   │   └── footer.php      # Admin closing tags + JS
│   ├── js/                 # Admin-specific JavaScript
│   │   └── main.js         # Sidebar toggle, UI behavior
│   ├── pages/              # Admin page modules (loaded by index.php)
│   │   ├── dashboard.php         # Banners + statistics management
│   │   ├── about_edit.php        # About Us content editor
│   │   ├── services_edit.php     # Services page content editor
│   │   ├── standards_edit.php    # Standards page content editor
│   │   ├── certification_edit.php # Certification content editor
│   │   ├── calibration_edit.php  # Calibration content editor
│   │   ├── training_edit.php     # Training content editor
│   │   ├── events_edit.php       # Events CRUD
│   │   ├── vacancies_edit.php    # Vacancies CRUD
│   │   ├── announcements_edit.php # Announcements CRUD
│   │   ├── publications_edit.php # Publications CRUD
│   │   ├── faq_edit.php          # FAQ CRUD
│   │   ├── contact_edit.php      # View/delete contact messages
│   │   ├── login.php             # (Legacy/unused - login is at admin/login.php)
│   │   ├── posts.php             # (Legacy posts page)
│   │   └── users.php             # (Legacy users page)
│   ├── uploads/            # User-uploaded files (images, PDFs)
│   │   └── announcements/  # Announcement attachments subdirectory
│   ├── config.php          # DB connection + session + auth helpers
│   ├── db_connect.php      # Duplicate DB connection (likely unused)
│   ├── index.php           # Admin front controller
│   ├── layout.php          # Alternative layout file (may be unused)
│   ├── login.php           # Admin login page
│   ├── logout.php          # Session destroy + redirect
│   └── error.log           # PHP error log for admin
├── assets/                 # Public site static assets
│   ├── css/                # Stylesheets (Bootstrap, plugins, main.css)
│   ├── js/                 # JavaScript (jQuery, Bootstrap, plugins, main.js)
│   ├── fonts/              # Font files
│   └── img/                # Images (logos, backgrounds, blog thumbs, etc.)
├── includes/               # Public site shared includes
│   ├── header.php          # Public site HTML head + navigation
│   ├── footer.php          # Public site footer
│   ├── db_connect.php      # Database connection (MySQLi)
│   └── main.css            # Additional public CSS
├── rs-plugin/              # Revolution Slider plugin assets
│   ├── assets/
│   ├── css/
│   ├── font/
│   ├── images/
│   └── js/
├── index.php               # Homepage
├── about-us.php            # About Us page
├── services.php            # Services overview
├── Standards.php           # Standards information
├── Certification.php       # Certification information
├── Calibration.php         # Calibration information
├── training-about.php      # Training overview
├── training-calendar.php   # Training calendar
├── events.php              # Events listing
├── event-details.php       # Single event detail (uses ?id= param)
├── vacancies.php           # Job vacancies listing
├── announcements.php       # Announcements listing
├── publications.php        # Publications listing
├── news.php                # News page
├── faq.php                 # FAQ page
├── contact.php             # Contact form (handles POST + sends email)
├── contactcalibration.php  # Calibration-specific contact form
├── board.php               # Board members page
├── Meetourteam.php         # Team members page
├── product.php             # Product certification page
├── managementsystems.php   # Management systems certification
├── ingelo.php              # Ingelo certification page
├── work.php                # Work with us page
├── purchase.php            # Purchase standards page
├── qoute.php               # General quotation request form
├── qoute_certification.php # Certification quotation request
├── qoute_training.php      # Training quotation request
├── tcp.php                 # Technical committee participation
├── disclaimer.php          # Legal disclaimer
├── terms.php               # Terms and conditions
└── privacy.php             # Privacy policy
```

## Directory Purposes

**`admin/`:**
- Purpose: Complete admin CMS for managing all dynamic site content
- Contains: Front controller, page modules, layout includes, CSS/JS, uploads
- Key files: `admin/index.php` (entry point), `admin/config.php` (DB + auth)

**`admin/pages/`:**
- Purpose: Individual admin page modules loaded by the front controller
- Contains: PHP files that handle both GET (display) and POST (save) for each content section
- Key files: `dashboard.php` (banners + stats), `about_edit.php`, `events_edit.php`, `vacancies_edit.php`

**`admin/includes/`:**
- Purpose: Admin layout fragments assembled by `admin/index.php`
- Contains: `header.php` (navbar), `sidebar.php` (navigation), `footer.php` (scripts)

**`admin/uploads/`:**
- Purpose: Storage for uploaded files (images, PDFs, documents)
- Contains: Mixed file types; flat structure with `announcements/` subdirectory
- Generated: Yes (runtime uploads)
- Committed: Should be in `.gitignore` but no `.gitignore` exists

**`assets/`:**
- Purpose: All public-facing static assets
- Contains: CSS frameworks/plugins, JS libraries/plugins, fonts, images
- Key files: `assets/css/main.css` (custom styles), `assets/js/main.js` (custom JS)

**`includes/`:**
- Purpose: Shared includes for public pages
- Contains: Header, footer, DB connection, main CSS
- Key files: `includes/db_connect.php` (database connection), `includes/header.php` (site navigation)

**`rs-plugin/`:**
- Purpose: Revolution Slider third-party plugin for homepage carousel
- Contains: Plugin CSS, JS, fonts, and image assets
- Generated: No (third-party vendor code)

## Key File Locations

**Entry Points:**
- `index.php`: Public homepage
- `admin/index.php`: Admin front controller (all admin pages route through here)
- `admin/login.php`: Admin authentication page

**Configuration:**
- `admin/config.php`: Database connection, session management, auth helper functions (`isLoggedIn()`, `requireLogin()`, `getCurrentUser()`, `set_flash()`, `redirect_self()`)
- `includes/db_connect.php`: Public site database connection

**Core Logic:**
- `admin/pages/dashboard.php`: Banner slides and site statistics management
- `admin/pages/about_edit.php`: About page content editor with image uploads
- `admin/pages/events_edit.php`: Events CRUD with image uploads
- `admin/pages/vacancies_edit.php`: Vacancies CRUD
- `admin/pages/announcements_edit.php`: Announcements CRUD with file attachments
- `admin/pages/publications_edit.php`: Publications CRUD with file uploads
- `admin/pages/faq_edit.php`: FAQ CRUD with categories and sort ordering
- `admin/pages/contact_edit.php`: View and delete contact form submissions

**Layout / Templates:**
- `includes/header.php`: Public site header (HTML head + top bar + main navigation)
- `includes/footer.php`: Public site footer
- `admin/includes/header.php`: Admin HTML head + top navbar
- `admin/includes/sidebar.php`: Admin sidebar navigation with collapsible sub-menus
- `admin/includes/footer.php`: Admin footer scripts

**Styling:**
- `assets/css/main.css`: Primary public site stylesheet
- `includes/main.css`: Additional public CSS (210KB, likely the main theme CSS)
- `admin/css/style.css`: Admin panel custom styles

## Naming Conventions

**Files:**
- Public pages: lowercase with hyphens (`about-us.php`, `event-details.php`, `training-calendar.php`)
- Exception: Some use PascalCase (`Standards.php`, `Certification.php`, `Calibration.php`, `Meetourteam.php`)
- Admin pages: lowercase with underscores, suffixed `_edit.php` (`events_edit.php`, `vacancies_edit.php`)
- Includes: lowercase (`header.php`, `footer.php`, `db_connect.php`)

**Directories:**
- All lowercase (`admin/`, `assets/`, `includes/`, `pages/`, `uploads/`)

**Database Tables:**
- Prefixed with `eswasa_` for entity tables (`eswasa_events`, `eswasa_vacancies`, `eswasa_faq`)
- Unprefixed for system/config tables (`banners`, `site_statistics`, `page_content`, `users`)

## Where to Add New Code

**New Public Page:**
- Create a new `.php` file in the project root (e.g., `new-page.php`)
- Use lowercase-hyphenated naming
- Include `includes/db_connect.php` at the top if DB access is needed
- Include `includes/header.php` and `includes/footer.php` for consistent layout
- Follow the pattern in `about-us.php` or `events.php`

**New Admin CRUD Section:**
1. Create `admin/pages/{section}_edit.php` with POST handler + list/form views
2. Add the filename to `$allowed_pages` array in `admin/index.php`
3. Add the page title to `$page_titles` array in `admin/index.php`
4. Add navigation entry in `admin/includes/sidebar.php`
5. Use `defined('ESWASA_ADMIN')` guard at the top of the file
6. Use `$conn` (available from `config.php`), `set_flash()`, and `redirect_self()` for CRUD operations

**New Database Table:**
- Prefix entity tables with `eswasa_` (e.g., `eswasa_new_entity`)
- Create the table directly in MySQL (no migration system exists)
- Use prepared statements with `$conn->prepare()` for all queries

**New Static Asset:**
- CSS: `assets/css/`
- JS: `assets/js/`
- Images: `assets/img/`
- Admin-specific: `admin/css/` or `admin/js/`

**Uploaded Files:**
- All uploads go to `admin/uploads/`
- Subdirectories can be created for organization (e.g., `admin/uploads/announcements/`)
- Reference paths in the database as `admin/uploads/filename.ext`

## Special Directories

**`admin/uploads/`:**
- Purpose: Runtime file uploads (images, PDFs)
- Generated: Yes
- Committed: Should NOT be committed (contains user data); no `.gitignore` exists

**`rs-plugin/`:**
- Purpose: Third-party Revolution Slider plugin
- Generated: No
- Committed: Yes (vendor code, no package manager)

**`assets/js/vendor/`:**
- Purpose: Third-party JS libraries
- Generated: No
- Committed: Yes (no package manager)

---

*Structure analysis: 2026-03-27*
