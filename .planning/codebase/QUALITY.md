# Codebase Quality & Concerns

**Analysis Date:** 2026-03-27

---

## Executive Summary

This is a traditional PHP website (no framework) for the Eswatini Standards Authority (ESWASA). It has a public-facing frontend and a custom admin panel. The codebase has **critical security vulnerabilities**, **zero test coverage**, **duplicated database connections**, and **inconsistent coding patterns**. The admin panel is reasonably structured but lacks CSRF protection and uses GET-based deletions.

---

## Critical Security Issues

### 1. Hardcoded Database Credentials in Multiple Files

**Severity:** CRITICAL

Database credentials (`root` with empty password) are hardcoded in at least 8 locations instead of using a single config:

- `admin/config.php` (lines 8-11): Uses `define()` constants
- `admin/db_connect.php` (lines 2-5): Raw variables
- `includes/db_connect.php` (lines 2-5): Raw variables (duplicate of above)
- `event-details.php` (line 3): Inline `new mysqli('localhost', 'root', '', 'eswasa')`
- `events.php` (line 135): Inline connection
- `vacancies.php` (line 224): Inline connection
- `publications.php` (line 146): Inline connection
- `announcements.php` (line 212): Inline connection

**Impact:** If credentials change, 8+ files must be updated. On production, `root` with no password is a severe security risk.

**Fix approach:** Create a single `includes/config.php` with environment-based credentials. All files should `require_once` this one file. Use `.env` or server-level config for production credentials.

### 2. No CSRF Protection on Any Form

**Severity:** CRITICAL

Zero CSRF tokens exist anywhere in the codebase. Every admin form (create, update, delete) and every public form (contact, quotes) is vulnerable to cross-site request forgery attacks.

**Files affected:** Every file in `admin/pages/*.php`, `contact.php`, `qoute.php`, `qoute_certification.php`, `qoute_training.php`

**Fix approach:** Generate a CSRF token in `admin/config.php` (store in `$_SESSION`), embed as hidden field in all forms, validate on POST handlers. For public forms, use the same pattern via `includes/db_connect.php`.

### 3. GET-Based Delete Operations (No CSRF, No POST)

**Severity:** HIGH

All delete operations use GET requests with no confirmation beyond JavaScript `confirm()`:

- `admin/pages/events_edit.php` (line 87): `$_GET['delete']`
- `admin/pages/vacancies_edit.php` (line 44): `$_GET['delete']`
- `admin/pages/publications_edit.php` (line 80): `$_GET['delete']`
- `admin/pages/faq_edit.php` (line 43): `$_GET['delete']`
- `admin/pages/announcements_edit.php` (line 76): `$_GET['delete']`
- `admin/pages/dashboard.php` (line 153): `$_GET['delete_slide']`
- `admin/pages/contact_edit.php` (line 21): `$_GET['delete']`

**Impact:** A malicious link or image tag can delete records. Browser prefetch, crawlers, or bookmarking can trigger deletions.

**Fix approach:** Change all deletes to POST forms with CSRF tokens. Never use GET for state-changing operations.

### 4. Email Header Injection in Contact Form

**Severity:** HIGH

In `contact.php` (lines 42-46), user-supplied `$name` and `$email` are placed directly into email headers:

```php
$headers .= "From: " . htmlspecialchars($name) . " <" . htmlspecialchars($email) . ">\r\n";
```

`htmlspecialchars()` does NOT prevent email header injection. A `\r\n` in the name or email field can inject additional headers (BCC, CC) for spam relay.

**Fix approach:** Use a proper mailer library (PHPMailer, Symfony Mailer) that sanitizes headers. At minimum, strip `\r\n` from all header values.

### 5. File Upload Vulnerabilities (Partial)

**Severity:** MEDIUM-HIGH

Two different upload patterns exist:

**Safer pattern** (in `admin/pages/about_edit.php` and `admin/pages/dashboard.php`):
- Generates unique filenames via `uniqid()`
- Validates MIME type with `finfo`
- Checks file size

**Unsafe pattern** (in `admin/pages/events_edit.php`, `announcements_edit.php`, `publications_edit.php`):
- Uses the **original filename** via `basename($_FILES['image']['name'])` (lines 32-33 in events_edit.php)
- No MIME type validation
- No file size limit enforcement
- Allows filename collisions and potential overwrites

**Impact:** File overwrites, potential directory traversal on certain OS configurations, and inconsistent security posture.

**Fix approach:** Standardize all uploads to use the `handle_image_upload()` pattern from `about_edit.php`: unique names, MIME checks, size limits.

### 6. Debug Output Enabled on Public Pages

**Severity:** HIGH (for production)

`display_errors` is set to `1` on 13+ public-facing PHP files:

- `index.php` (lines 2-3)
- `Certification.php`, `Calibration.php`, `faq.php`, `ingelo.php`, `privacy.php`, `disclaimer.php`, `Standards.php`, `managementsystems.php`, `terms.php`, `product.php`, `qoute_certification.php`

**Impact:** Stack traces, file paths, and database errors visible to end users. Leaks internal server structure.

**Fix approach:** Remove all `ini_set('display_errors', 1)` from individual files. Set `display_errors = Off` in php.ini for production. The admin panel (`admin/index.php` line 3) correctly sets `display_errors` to `0`.

### 7. Database Error Messages Exposed to Admin Users

**Severity:** MEDIUM

Multiple admin pages expose raw MySQL error messages via `$conn->error`:

- `admin/pages/events_edit.php` (line 78)
- `admin/pages/publications_edit.php` (line 72)
- `admin/pages/vacancies_edit.php` (line 36)
- `admin/pages/announcements_edit.php` (line 68)
- `admin/pages/faq_edit.php` (line 35)

Additionally, `index.php` uses `die()` with `mysqli_error()` output (lines 9, 16, 29).

**Fix approach:** Log errors server-side; show generic "An error occurred" messages to users.

### 8. No Upload Directory Protection

**Severity:** MEDIUM

`admin/uploads/` directory has no `.htaccess` to prevent execution of uploaded files. While file type validation exists, a bypass could allow PHP execution.

**Fix approach:** Add `.htaccess` with `php_flag engine off` or equivalent. Serve uploads through a PHP script that forces `Content-Disposition: attachment`.

---

## Architecture & Code Quality Issues

### 9. Duplicate Database Connection Logic

Three separate connection files exist with identical logic:

- `admin/config.php` (line 14) -- uses `define()` constants
- `admin/db_connect.php` (lines 1-11) -- standalone, raw variables
- `includes/db_connect.php` (lines 1-11) -- standalone, identical to above

Plus 5 files with inline `new mysqli()` calls (see issue #1).

**Fix approach:** Single `includes/config.php` required everywhere. Delete `admin/db_connect.php` and `includes/db_connect.php`. Replace all inline connections with `require_once`.

### 10. Inconsistent File Naming Conventions

PHP files use at least 4 naming conventions:

- **PascalCase:** `Calibration.php`, `Certification.php`, `Meetourteam.php`, `Standards.php`
- **lowercase-hyphen:** `about-us.php`, `event-details.php`, `training-about.php`, `training-calendar.php`
- **lowercase:** `board.php`, `contact.php`, `services.php`, `vacancies.php`
- **Misspelled:** `qoute.php`, `qoute_certification.php`, `qoute_training.php` (should be "quote")

**Impact:** Confusing for developers, broken links if server is case-sensitive (Linux deployment).

### 11. Massive Single-File Pages

Several files mix HTML, CSS, PHP logic, and JavaScript in 300-900+ line monoliths:

- `training-about.php`: 969 lines (almost entirely static HTML)
- `index.php`: 624 lines
- `news.php`: 507 lines
- `managementsystems.php`: 467 lines
- `ingelo.php`: 463 lines

**Fix approach:** Extract shared layout (header CSS, footer scripts) into includes. Keep page-specific content focused.

### 12. Duplicated HTML Boilerplate

Every public PHP page independently includes the full `<head>` tag with all CSS files. The `includes/header.php` already contains a `<head>` section, creating duplicate `<head>` tags on pages like `contact.php` that also define their own.

**Files affected:** All public-facing `.php` files in the root directory.

**Fix approach:** Use a single layout template. Pages should only define their unique title, description, and body content.

### 13. Users Page is Static HTML (Non-Functional)

`admin/pages/users.php` contains hardcoded HTML table data (lines 33-60) with no database queries. The "Add New User", "Edit", and "Delete" buttons have no functionality.

**Impact:** User management does not work. No way to add admin users through the interface.

**Fix approach:** Implement actual CRUD operations backed by the `users` database table.

### 14. HTML Nesting Errors in Header

`includes/header.php` (lines 37-50) has malformed HTML with a `<ul>` nested improperly inside another `<ul>`:

```html
<ul class="tg-header__top-social list-wrap">
    <ul class="tg-header__top-info list-wrap">  <!-- Invalid nesting -->
        ...
    </ul>
    <li>...</li>  <!-- These <li> belong to the outer ul -->
</ul>
```

### 15. Dead/Placeholder Links

`includes/header.php` contains multiple `href="#"` placeholder links (lines 39-46) for social media and Certification/Standards quick links that go nowhere.

`includes/footer.php` (line 13) links to `index.html` instead of `index.php`.

---

## Testing Gaps

### 16. Zero Test Coverage

**Severity:** HIGH

No test files, no test framework, no test configuration exists anywhere in the project. Zero automated testing of any kind.

- No PHPUnit or equivalent
- No integration tests
- No browser/E2E tests
- No CI/CD pipeline

**Priority areas for testing:**
1. Authentication flow (`admin/login.php`, `admin/config.php`)
2. File upload validation logic
3. Contact form submission and validation
4. Admin CRUD operations (events, vacancies, announcements, FAQ, publications)

---

## Session & Authentication Concerns

### 17. Inconsistent Session Checking

`admin/login.php` (line 3) checks `$_SESSION['admin_logged_in']` before calling `require_once 'config.php'` (which calls `session_start()`). Session is not started yet at that point.

`admin/config.php` function `isLoggedIn()` checks `$_SESSION['user_id']`, while the login page sets both `$_SESSION['admin_logged_in']` and `$_SESSION['user_id']`.

**Impact:** The session check at the top of `login.php` may silently fail since `session_start()` has not been called yet.

**Fix approach:** Always call `session_start()` before accessing `$_SESSION`. Consolidate session keys.

### 18. No Session Regeneration After Login

`admin/login.php` does not call `session_regenerate_id(true)` after successful authentication (line 25). This leaves the application vulnerable to session fixation attacks.

### 19. No Rate Limiting or Brute Force Protection

The login form (`admin/login.php`) has no:
- Rate limiting
- Account lockout after failed attempts
- CAPTCHA
- Delay between attempts

**Impact:** Automated brute force attacks can enumerate usernames (error messages distinguish "User not found" from "Invalid password" on lines 33-36) and try unlimited passwords.

### 20. No Spam Protection on Public Forms

`contact.php` has no CAPTCHA, honeypot, or rate limiting. The `qoute*.php` files (quote request forms) similarly have no bot protection.

---

## Performance Concerns

### 21. No Pagination on Admin List Views

All admin list pages (`events_edit.php`, `vacancies_edit.php`, `announcements_edit.php`, `publications_edit.php`, `contact_edit.php`, `faq_edit.php`) fetch ALL records with no pagination:

```php
$conn->query("SELECT * FROM eswasa_events ORDER BY event_date DESC");
```

**Impact:** As data grows, page load times increase linearly. Contact messages especially can accumulate rapidly.

### 22. No Caching Headers or Strategy

No cache-control headers are set. No server-side caching exists. Every page request hits the database.

---

## Missing Features

### 23. No Content Security Policy (CSP) Headers

No security headers set anywhere:
- No `Content-Security-Policy`
- No `X-Frame-Options`
- No `X-Content-Type-Options`
- No `Strict-Transport-Security`

### 24. No Input Length Limits on Database Fields

POST handlers trim inputs but never enforce maximum lengths before database insertion. Excessively long inputs may be silently truncated by MySQL.

### 25. No Audit Logging

Admin actions (create, update, delete) are not logged. No way to track who changed what and when (except for banners which track `updated_by`).

---

## Summary by Priority

| Priority | Issue | Files |
|----------|-------|-------|
| CRITICAL | Hardcoded DB credentials in 8+ files | See #1 |
| CRITICAL | No CSRF protection anywhere | All form files |
| HIGH | GET-based deletions | All `admin/pages/*_edit.php` |
| HIGH | Email header injection | `contact.php` |
| HIGH | Debug output on production pages | 13 public PHP files |
| HIGH | Zero test coverage | Entire project |
| HIGH | No brute force protection | `admin/login.php` |
| MEDIUM-HIGH | Unsafe file uploads (original filenames) | `events_edit.php`, `announcements_edit.php`, `publications_edit.php` |
| MEDIUM | DB errors exposed to users | 5 admin pages, `index.php` |
| MEDIUM | No upload directory protection | `admin/uploads/` |
| MEDIUM | No session regeneration | `admin/login.php` |
| MEDIUM | No spam protection on public forms | `contact.php`, `qoute*.php` |
| LOW | Inconsistent file naming | Root directory PHP files |
| LOW | Static users page | `admin/pages/users.php` |
| LOW | HTML nesting errors | `includes/header.php` |
| LOW | No pagination | All admin list pages |

---

*Quality and concerns audit: 2026-03-27*
