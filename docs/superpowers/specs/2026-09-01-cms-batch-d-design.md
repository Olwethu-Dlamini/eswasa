# CMS Batch D — design

Date: 2026-09-01

Ten changes requested in one batch. Two were dropped after clarification and
are recorded here so they are not re-litigated later.

## Scope

| # | Request | Outcome |
|---|---------|---------|
| 1 | Affiliations slider fixed at 10 slots | New `index_affiliations` table + admin CRUD |
| 2 | Colours on the training chooser | Per-training colour picker |
| 3 | Calendar phone mandatory | `required` on the apply-modal phone field |
| 4 | Temporary emails | **Dropped** — user: "leave them as is" |
| 5 | Suspended organisations | Folded into #7 |
| 6 | Add/remove certified product producers | DB-driven via `certified_organisations` |
| 7 | 3 pages for the marks | Three per-scheme registers |
| 8 | Display Ingelo certified producers | DB-driven via `certified_organisations` |
| 9 | Remove Available Standards for the logos | Ingelo's list replaced by the logo grid |
| 10 | Standards link to tc.swasa | **Dropped** — user: "stay as is" |
| 11 | Vacancies title and PDF | `pdf_path` on `eswasa_vacancies` |
| 12 | Remove Certification/Standards from header | Grey top strip only, navbar untouched |

## 1. The three certification registers (#5, #7)

Today all three home-page marks Verify to one `certification-status.php`, whose
data lives in three hardcoded arrays (`certification-status.php:16,21,25`).

Rejected: one page with `?scheme=` (three marks share one URL — weak
breadcrumbs and SEO); three fully separate files (triplicates 550 lines of
markup and CSS).

Chosen: three thin page files over one shared include.

New table `certification_register`:

- `scheme` — `ms` / `product` / `ingelo`
- `status` — `suspended` / `withdrawn` / `reduced`
- `client_name`, `logo_path` — logo shown in the Client cell, name as a
  wordmark fallback when there is no logo
- `cert_no`, `scope`, `effective_date`
- `reason_note` — serves both the Suspended "Reason" and Reduced "Note" columns
- `sort_order`, `is_active`, timestamps — mirrors `certified_organisations`

Files:

- `includes/cert_register_page.php` — the page body, parameterised by `$scheme`
- `certification-status-management-systems.php`, `-product.php`, `-ingelo.php`
- `certification-status.php` stays as a hub listing the three registers, so the
  existing links from `managementsystems.php` and `sitemap.xml` keep working
- `index.php` — each `index_mark_N_verify_url` default points at its own register

CMS copy: column headers, empty states and the appeals/complaints/info footer
stay as one shared `cert_status_*` set. Hero title, subtitle and intro become
per-scheme keys. All three seed with the current CER_PR_026 wording; Product and
Ingelo are edited in admin once their own procedure references are known.

Admin: `cert_status_edit.php` gains a register manager — pick scheme and status,
then add/edit/remove rows with a logo upload, following the Certified
Organisations tab in `admin/pages/managementsystems.php`.

## 2. Repeatable logo lists (#1, #6, #8, #9)

Two structurally different things, so two storage decisions.

**Affiliations** are logo + link + alt. `index.php` hardcodes ten slots
(`index_affiliation_1..10_*`). New table `index_affiliations`
(`logo_path`, `url`, `alt`, `sort_order`, `is_active`); a migration copies the
ten existing rows out of `page_content` so nothing disappears on deploy. The
`index_affiliations_heading` key is unchanged.

**The three certified-company grids** are logo + company + what they are
certified for. Rather than three near-identical tables, `certified_organisations`
gains `scheme` (`ms` / `product` / `ingelo`, defaulting to `ms` so existing rows
are untouched) and `product` (nullable — product tiles show a product name, MS
tiles do not). Each grid filters by its own scheme.

- `managementsystems.php:682` — already DB-driven, unchanged
- `product.php:625` — hardcoded array of three producers whose logos are guessed
  by filename from `assets/img/clients/`; becomes DB-driven, the three seeded
- `ingelo.php:390` — the "Available Standards" list is replaced by an Ingelo
  Certified Producers logo grid

Admin: one shared partial included by three pages, so each scheme is managed
where it is displayed.

`ingelo_standards_title` and `ingelo_standards_list` stay in the database but
stop rendering, and their fields come out of the Ingelo admin form so it does
not offer an editor for a section that no longer appears.

## 3. Small changes (#2, #3, #11, #12)

**Training colour.** `training_sessions` gains `colour` (hex). The nine-family
palette in `includes/training_families.php` becomes the default a training
starts with rather than the only source of colour; admin gets a colour input,
and the calendar, day pills and legend read the per-training value.

**Calendar phone.** `training-calendar.php:753` gains `required`, and its label
gains the `.required` class the other mandatory fields on the site use.

**Vacancies PDF.** `eswasa_vacancies` gains `pdf_path`. The admin add/edit forms
upload through the existing `pc_upload_document()`; `vacancies.php` shows a
download link on any vacancy that has one.

**Header.** The two icon links at `includes/header.php:36-37` are removed. The
main-nav Certification and Standards dropdowns are untouched.

## Migration

One idempotent file, `admin/sql/upgrade_2026_09_01.sql`, in the style of
`upgrade_2026_08_18.sql`: additive only, safe to run twice, drops nothing.
