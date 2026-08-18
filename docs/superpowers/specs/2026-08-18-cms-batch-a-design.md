# ESWASA CMS — Batch A ("Broken things") design

**Date:** 2026-08-18
**Status:** approved, ready for implementation planning
**Source:** UAT punch list of 17 items raised against the CMS. This spec covers
Batch A only. Batches B and C are scoped at the end.

---

## 1. Context

The site is flat PHP in the webroot. Editable copy lives in a single
key-value table, `page_content (page_key UNIQUE, content TEXT)` — 905 rows.
Repeating content (events, tenders, publications, policies, team members,
training sessions, banners, quote requests, certified organisations,
certification documents) lives in its own table.

The admin is a front controller: `admin/index.php` checks the session,
whitelists the page against `$allowed_pages`, buffers
`admin/pages/{page}.php`, then wraps it in `includes/header.php`,
`sidebar.php` and `footer.php`. Editors POST to themselves and finish with
`set_flash()` + `redirect_self()`. Images go through a shared Cropper.js
modal that posts base64 in a hidden `{field}_cropped` input.

Conventions are documented in `admin/CMS_AGENT_TEMPLATE.md` and this spec
follows them.

### Verification environment

Every root cause below was reproduced against a local replica of the
production dump, not inferred from reading. To rebuild it:

```bash
docker run -d --name eswasa-uat --security-opt systempaths=unconfined \
  -e MARIADB_ROOT_PASSWORD=eswasa -e MARIADB_DATABASE=admin_eswasa \
  -e MARIADB_USER=eswasa1 -e MARIADB_PASSWORD='wu7K!983k' \
  -p 33061:3306 mariadb:11

docker exec -i eswasa-uat mariadb -ueswasa1 -p'wu7K!983k' admin_eswasa \
  < admin_eswasa_2026-08-18_16-12-52.sql

ESWASA_DB_HOST=127.0.0.1 ESWASA_DB_USER=eswasa1 \
ESWASA_DB_PASS='wu7K!983k' ESWASA_DB_NAME=admin_eswasa APP_ENV=development \
  php -d mysqli.default_port=33061 -S 127.0.0.1:8080
```

Local admin password was reset in the container only. Production credentials
are untouched.

---

## 2. Scope

Batch A fixes things that are broken. It does not restructure the CMS menu,
reconcile CMS forms against live page content, or add new editors.

| ID | Item | Summary |
|----|------|---------|
| A1 | 16 | Calibration contact form posts to a third-party server |
| A2 | 16 | Contact form silently discards valid submissions |
| A3 | 8  | Quote inbox shows no requester name; source tagging is fragile |
| A4 | 8  | Quote attachments: PDF-only, enforced limits, visible rejections |
| A5 | 5  | "Enroll Now" button does nothing |
| A6 | 6b | Prospectus download 404s |
| A7 | 17a | Deleting a user hangs the browser; super-user is deletable |
| A8 | 9  | Cannot add a certification document through the UI |
| A9 | —  | Two team photos 404 on Meet Our Team, no fallback |
| X1 | —  | `set_flash('error', …)` renders an invisible alert on 8 pages |
| X2 | —  | `display_errors` is on in production |

A9, X1 and X2 are not on the UAT list. A9 was found while checking the
board-members question and is visible breakage on a public page. X1 and X2
sit in files Batch A already opens, and X1 actively hides the error messages
that several Batch A fixes rely on being visible.

### Out of scope

- **Batch B — structure and parity:** items 3 (CMS menu order to match the
  live nav), 2 (About Us breadcrumb entry + content parity), 1 (stale CMS
  pages), 12 (Standards page parity), 15 (Service Charter parity), 4 (remove
  the LinkedIn field from Meet Our Team).
- **Batch C — new capability:** items 10 (calibration brands CRUD), 11
  (upload-instead-of-link across the CMS), 13 (event image previews), 6a
  (calendar day-pills for multiple same-day intakes), 17b (CMS UI polish).

Item 14 (FAQ add/remove) is **closed as not-a-bug** — see §5.

---

## 3. Decisions taken

Recorded so implementation does not relitigate them.

| Decision | Choice |
|---|---|
| Sequencing | Batch A first, ship, then spec B, then C |
| Item 5 target | Fix the dead "Enroll Now" button only; ISO image keeps opening its course modal |
| Item 11 pattern | File upload **plus** an optional URL fallback. Existing URL values keep working; no migration, no broken live links |
| Item 6a rendering | Stacked pills, one per training per day (Batch C) |
| Calibration form | Retire `contactcalibration.php`, point the nav at `qoute_calibration.php` |
| Contact notification | CMS inbox **and** a best-effort email with corrected headers |
| Quote attachments | PDF only, 10 MB per file, 5 files max |
| Super user | Protect user id 1 |

---

## 4. The fixes

### A1 — Calibration RFQ consolidation

**Symptom.** Calibration quote requests never arrive.

**Root cause.** `contactcalibration.php:264` submits to
`https://bazardeal.com.bd/biz/biztek-preview-3/biztek/form-process` — a URL
left over from the purchased template. Every submission is posted to a
third party and never reaches ESWASA. Meanwhile `qoute_calibration.php` is
a correct RFQ form posting to `process_quote.php`, and nothing links to it:
`includes/header.php:95` points Calibration ▸ Request for Quotation at the
broken page.

**Fix.**
1. Replace the body of `contactcalibration.php` with a 301 redirect to
   `qoute_calibration.php`, so existing bookmarks and indexed links survive.
2. `includes/header.php:95` → `qoute_calibration.php`.
3. `Calibration.php:140` — change the `cal_cta_btn2_url` default, and update
   the stored `page_content` value if it points at the retired page.
4. Remove the now-dead `contactcalibration` entry from the `$pages` list in
   `admin/pages/breadcrumbs_edit.php`.

The page carries a breadcrumb, a generic "Get In Touch" block and the form.
Nothing on it is unique, so nothing is lost.

**Priority.** Apply first. This is the only item actively sending customer
data to an external host.

**Verification.** `curl -I contactcalibration.php` returns 301 to
`qoute_calibration.php`; no occurrence of `bazardeal` remains in the repo.

---

### A2 — Contact form silently discards submissions

**Symptom.** "You can't fill the form."

**Reproduced.** Posting `phone=7612 3456` — an ordinary Eswatini mobile
number — returns 302 to `contact.php`, displays **no error**, and writes
**nothing** to the database. Posting `+268 7612 3456` succeeds and redirects
to `?success=1`.

**Root causes.** Three independent defects in `contact.php`:

1. **Phone validation rejects local numbers.** `contact.php:81` requires
   `^[0-9+\s\-\(\)]{10,}$`. An 8-digit national number written plainly is 9
   characters and fails.
2. **The error message cannot survive the redirect.** `session_start()` is
   called only at line 122, inside the error branch. The subsequent GET that
   renders the page never starts a session, so the read at line 141 always
   sees an empty `$_SESSION` — the message and the user's typed input are
   both lost.
3. **Notification email is dropped by receiving servers.** Line 107 sets
   `From:` to the visitor's own address, which fails SPF for the sending
   host. `eswasa_contact_messages` holds 4 rows, one of which reads
   *"messages are not sent nor received"* — the DB write worked, the email
   did not.

**Fix.**
1. `session_start()` (guarded by `session_status()`) at the top of the file,
   before any output.
2. Replace the regex with digit-count validation: strip non-digits, require
   7–15 digits. Accepts `7612 3456`, `+268 7612 3456`, `(+268) 7612-3456`.
3. Rework the mail step: `From:` a real `eswasa.co.sz` address,
   `Reply-To:` the visitor, so replies still work and SPF passes. Wrap so a
   mail failure never blocks the DB write or shows the visitor an error.
4. Add `site_contact_notify_email` to Site Settings, defaulting to
   `info@eswasa.co.sz`, validated as an email address.
5. Add an unread-count badge to the Contact Us sidebar item
   (`COUNT(*) FROM eswasa_contact_messages WHERE status='new'`) so messages
   are visible without opening the page.

`contact_edit.php` already has a working inbox with status transitions and
delete — no new admin UI is required beyond the badge.

**Verification.** `7612 3456` saves and shows the success state; a genuinely
invalid entry shows a visible error with the typed values preserved.

---

### A3 — Quote inbox shows no requester name

**Symptom.** "Training quote page not working and cms not working too."

**What the data shows.** The one real submission in
`eswasa_quote_requests` is tagged `source='training'` correctly, so
referrer-based source detection did work. But `contact_name` and
`organization` are both `NULL`, so the inbox renders a dash where
"Welile Mndzebele" should be.

**Root cause.** `process_quote.php:47` picks the name from
`['contact_person', 'contactName', 'contact_name', 'full_name', 'name']`.
The individual training form posts **`full_names`** (plural), which is not in
the list. The company form's organisation field has the same class of
mismatch.

**Fix.**
1. Add `full_names` and the remaining real field names to the alias lists,
   cross-checked against every input name in all five quote forms
   (`qoute_training.php` has two forms — company and individual —
   `qoute_certification.php` and `qoute_calibration.php` one each).
2. Backfill `contact_name` / `organization` on existing rows from their
   stored `raw_form` JSON, so the current submission stops showing a dash.
3. Harden source tagging rather than relying on the referrer: add
   `<input type="hidden" name="quote_source" value="…">` to all five forms.
   `process_quote.php` already prefers this field when present; the referrer
   becomes a fallback only.
4. Stop redirecting to raw `HTTP_REFERER` — resolve the return page from the
   known source instead.
5. Add an **Unsorted** bucket to the quote inbox for `source='other'`, so a
   mis-tagged submission can never be invisible again.

**Verification.** Submit each of the five forms; each appears in its own
inbox with the requester's name and organisation populated.

---

### A4 — Quote attachment handling

**Symptom.** Attachments accepted loosely; failures invisible.

**Root cause.** `process_quote.php` allows
`pdf, doc, docx, jpg, jpeg, png, webp, xls, xlsx` on extension alone, with
no MIME check, and silently `continue`s past any rejected file. The user sees
a success page and never learns the attachment was dropped.

**Fix.**
1. PDF only, verified with `finfo` MIME sniffing rather than the extension.
2. 10 MB per file, 5 files maximum.
3. Collect per-file rejection reasons and surface them on the returned form —
   never drop a file silently.
4. Front-end on all five forms: `accept="application/pdf"`, explicit helper
   text stating the limits, required-field marks, and a small script listing
   picked files with sizes plus client-side pre-validation.

**Verification.** A non-PDF and an oversized PDF are each rejected with a
named, visible reason; a 6th file is refused; valid PDFs land in
`admin/uploads/quotes/` and are listed in the inbox modal.

---

### A5 — "Enroll Now" does nothing

**Root cause.** `training-about.php:852` is
`<button type="button" class="btn btn-enroll">Enroll Now</button>` — no
`href`, no handler, present in all seven course modals.

**Fix.** Make it a link to `purchase.php`, styled identically. The ISO
infographic and "View Details" keep opening the course modal, per the
decision in §3.

**Verification.** All seven modals link to `purchase.php`, which returns 200.

---

### A6 — Prospectus download 404s

**Reproduced.** The rendered link is
`admin/downloads/ESWASA TRAINING PROSPECTUS 2025-26.pdf` → **404**. The file
exists at `admin/uploads/ESWASA TRAINING PROSPECTUS 2025-26.pdf` → **200**.
There is no `admin/downloads/` directory.

**Root cause.** Both the stored `train_cal_prospectus_url` value and the
code default at `training-calendar.php:62` point at a directory that has
never existed.

**Fix.**
1. Correct the code default and the stored value to the real path.
2. Add `download` to the anchor so it saves rather than navigating.
3. So it cannot recur: replace the free-text URL box in the Training Calendar
   editor with a **PDF upload** field, keeping the URL as an optional
   fallback. This is the first instance of the item-11 pattern from §3 and
   establishes the shared helper Batch C will reuse.

**Verification.** The link returns 200 and downloads; uploading a new PDF
through the editor updates the link.

---

### A7 — User deletion

**Symptom.** "Newly added users cannot be deleted."

**Reproduced.** Creating then deleting a user leaves curl still returning 302
after 15 redirects with `delete_user=3` still in the URL. The row *is*
deleted, and `activity_log` gains **16 `user.delete` entries within one
second**. Production shows the same signature: 80 `user.delete` rows for
`users#2` in two bursts at 12:55:57 and 12:56:26–27 on 2026-08-18.

**Root cause.** `redirect_self()` (`admin/config.php:68`) strips `delete`,
`delete_banner`, `delete_quote` and `quote_sent` from the query string — but
not `delete_user`. So `users.php` deletes the row, redirects to a URL that
still carries `delete_user=N`, deletes again, and loops until the browser
gives up. The delete succeeds; the page never returns.

**Fix.**
1. In `redirect_self()`, strip any parameter matching `/^delete/` plus the
   existing extras, instead of maintaining a hand-written list. This removes
   the whole class of bug rather than this one instance.
2. Protect user id 1: hide the Delete control in the UI **and** reject the
   request server-side, so a hand-crafted URL cannot remove it either.
3. Replace `str_contains()` at `users.php:61` with `strpos() !== false`. It is
   PHP 8-only and the duplicate-username path would fatal on a PHP 7.4 host.
4. Purge the 80 loop-generated `user.delete` rows from `activity_log` as part
   of the deploy, so the audit trail is readable.

**Verification.** Deleting a user lands back on the user list with one
success flash and exactly one log row. The Delete control is absent for
id 1, and requesting `delete_user=1` directly is refused.

---

### A8 — Cannot add a certification document

**Symptom.** "Management systems page cms have option to add or remove
documents" — you can remove, you cannot add.

**Root cause.** The editor is fully built: `admin/pages/managementsystems.php`
has three tabs with add / edit / toggle / delete for both
`certified_organisations` (6 rows) and `certification_documents` (11 rows,
already PDF uploads rather than links). But the **"Add document" button sits
in the page header gated on server-side `$active_tab === 'docs'`**
(line 342), while the tabs are Bootstrap client-side tabs that swap panes
without reloading or changing the URL. Land on the page (`$active_tab` is
`'orgs'`), click "Certification Documents", and the header button still reads
"Add organisation". The only route to the form is hand-typing `?new_doc=1`.

Checked against the other nine tabbed editors — none gate a header control on
`$active_tab`, so this is contained to one file.

**Fix.** Move each tab's Add button **inside its own tab pane**, so it is
governed by the same client-side state as the content it belongs to. Keep the
existing `?new_org=1` / `?new_doc=1` targets.

**Verification.** With no `?tab=` parameter, clicking each tab shows an Add
control appropriate to that tab, and both add forms are reachable by mouse
alone.

---

### X1 — Invisible error alerts

**Root cause.** 25 calls to `set_flash('error', …)` across
`publications_edit.php`, `policies_edit.php`, `announcements_edit.php`,
`events_edit.php`, `tenders_edit.php`, `vacancies_edit.php` and
`faq_edit.php`. The renderer emits `alert-<type>`, and `alert-error` is not a
Bootstrap 5 class, so these appear as unstyled text with no red box. Every
one is a validation or database-error message.

**Fix.** Change them all to `'danger'`. Optionally have `set_flash()` map
`error` → `danger` defensively so a future typo cannot reintroduce it.

---

### X2 — `display_errors` on in production

**Root cause.** `includes/env.php:65` sets `display_errors` to `'1'` in the
production branch. Only `admin/index.php` overrides it, so public pages leak
file paths and SQL errors to visitors.

**Fix.** `'0'` in the production branch, keeping `log_errors` on.

---

### A9 — Broken team photos on Meet Our Team

**Symptom.** Not on the UAT list; found while verifying the board-members
question. Two photos on the public Meet Our Team page return **404** and
render as broken-image icons, one of them the Chairperson's.

**Reproduced.** Against the live page:

| Person | File | Result |
|---|---|---|
| Mrs. Dumile Sibandze (Chairperson) | `admin/uploads/dumile.png` | **404** |
| Ms. Sipholesihle Sukati | `admin/uploads/sukati.png` | **404** |
| Other 6 team members | — | 200 |

**Root cause.** Two separate problems. The image files were never uploaded to
`admin/uploads/`, and `Meetourteam.php` renders `<img src="…" class="team-img">`
with **no `onerror` fallback**, so a missing file degrades to a broken icon
rather than a placeholder. `eswasa_team_members.photo` for the Vacant QA
Manager also points at `management/director_finance.jpg`, which likewise does
not exist.

**Fix.**
1. Add an `onerror` fallback to the team photo renderer so any missing image
   falls back to a neutral placeholder — the same defensive pattern already
   used in `admin/includes/header.php` for the logo.
2. Flag the three missing files for staff to re-upload through the existing
   Meet Our Team editor. This is a content action, not a code change; the
   fallback above stops it looking broken in the meantime.

**Verification.** Every `<img>` on `Meetourteam.php` returns 200, or renders
the placeholder rather than a broken icon.

---

## 5. Resolved and closed

**Item 14 (FAQ add/remove) — not a bug.** `faq_edit.php` renders an "Add FAQ"
button, its modal and the question field, with working add / edit / delete
against the 28 rows in `eswasa_faq`. The reporter confirmed on 2026-08-18
that the `+` button is present and the page behaves. No action.

**`eswasa_board_members` — dead table, safe to drop.** Verified: all five
rows were migrated into `eswasa_team_members` as `section='council'`
(ids 12–16) with identical names, roles, photos and sort order. No PHP file
in the repository references the table, and no board-members page exists.
The reporter confirmed the content now lives entirely in Meet Our Team.

Drop it in the Batch A deployment (see §6). Alongside it, three further
tables are confirmed unreferenced by any code and can go in the same pass:

| Table | Rows | Note |
|---|---|---|
| `eswasa_board_members` | 5 | Superseded by `eswasa_team_members` |
| `admin` | 1 | Legacy login table; `users` replaced it |
| `blogs` | 0 | Never used |
| `site_statistics` | 3 | Placeholder values (1000 / 999 / 999), never rendered |

Take a dump before dropping. These are removals of dead weight, not
behaviour changes, so they can land last and be skipped without affecting
any other item in this batch.

---

## 6. Deployment notes

Batch A includes data changes as well as code:

1. `page_content` — correct `train_cal_prospectus_url`; correct
   `cal_cta_btn2_url` if it points at the retired page.
2. `eswasa_quote_requests` — backfill `contact_name` / `organization` from
   `raw_form`.
3. `activity_log` — delete the 80 loop-generated `user.delete` rows.
4. Drop the four dead tables listed in §5, after taking a dump.

These are small and reversible; each ships as an idempotent statement in
`admin/sql/` alongside the existing `migrate_policies.sql`, so a re-run is
harmless.

Three team photos also need re-uploading through the Meet Our Team editor
(`dumile.png`, `sukati.png`, and the Vacant QA Manager's image). That is a
content task for staff, not part of the code deploy — A9's fallback keeps the
page presentable until it happens.

Ordering: **A1 first** — it is the only item leaking customer data. The rest
are independent and can land in any order.

## 7. Risks

- **A1 breaks an inbound link path.** Mitigated by using a 301 rather than
  deleting the page.
- **A2's phone rule could still reject a real number.** Mitigated by
  validating on digit count (7–15) rather than a character pattern, and by
  showing a visible error so the visitor can correct it instead of silently
  losing the message.
- **A4 tightening to PDF-only could reject something staff currently rely
  on.** This was an explicit decision (§3). Rejections are now visible and
  named, so the failure mode is legible rather than silent.
- **A7's `/^delete/` rule could strip a parameter some page relies on
  surviving a redirect.** Checked exhaustively. The admin has seven `delete*`
  GET parameters across 17 handlers: `delete`, `delete_banner`,
  `delete_quote`, `delete_user`, `delete_image`, `delete_org`, `delete_doc`.
  Only **two** handlers finish via `redirect_self()` — `delete_quote` in
  `_quote_inbox.php` (already in the strip list, so already safe) and
  `delete_user` in `users.php` (absent from it, hence the loop). The other 15
  finish with an explicit `header('Location: …')` that does not carry the
  parameter forward, so the rule cannot affect them. `delete_user` is
  therefore the only broken instance, and the rule closes the class without
  side effects.
- **No automated tests exist.** Verification is the manual checklist per
  item above, run against the local replica in §1.
