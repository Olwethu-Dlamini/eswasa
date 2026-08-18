# ESWASA CMS — Batch B ("Structure & parity") design

**Date:** 2026-08-18
**Status:** **implemented and pushed to `main`** (2026-08-18)

| Item | Commit | Outcome |
|------|--------|---------|
| B1 sidebar order | `04c8805` | Sidebar now matches the public nav exactly |
| B2 About Us | `72bb6de`, `3f9de38` | Breadcrumb on the shared system; 13 headings, 10 affiliation slots and 4 accreditation slots editable |
| B3 Standards | `247358f` | **Mostly a false alarm** — see the correction below. One dead editor card hidden |
| B4 Service Charter | `20e1695` | All five charter blocks editable; the "remains in code" notice is gone |
| B5 LinkedIn | `1e75a10` | Removed from both forms and the public page; 0 members affected |
| B6 stale content | `72bb6de` | 74 dead rows deleted (905 → 831); 4 breadcrumb slugs and the bank SWIFT field added |

### Correction to B3

The spec claimed `std_sector_1..8` and `std_info_item_1..4` had no inputs.
**Both were wrong.** They are generated in `for` loops with interpolated `name`
attributes, so a literal grep could not see them, but they render and save
correctly (confirmed 8/8 and 4/4). The editor also already had section anchors
and a jump nav, and once the dead card was hidden its section order already
matched the live page — so the planned reorder was unnecessary too.

What was real: an "Our Affiliations" card offering ~25 fields that reach
nothing. `Standards.php` declares `std_affiliations_*` and `std_aff_1..6_*`,
ships populated defaults and carries the finished grid CSS, but no markup
renders any of it. Hidden behind a `$std_affiliations_live` flag on the
reporter's instruction, with keys, defaults and CSS left intact.

**Lesson for future audits in this codebase:** interpolated key names
(`"prefix_{$i}_field"`) are common, so a literal search for `name="key"`
systematically under-reports coverage. The same mistake produced 30 false
positives on `index.php` during the Batch B audit. Check for loops before
concluding a field is missing.

### Verification

Every public page returns 200 with no PHP notices, warnings or fatals, and
every admin page likewise. Visible text was diffed across all 36 public pages
before and after: only `service-charter.php` differs, and only where a link
now covers a whole bullet rather than a phrase inside it (B4, deliberate).
**Predecessor:** `2026-08-18-cms-batch-a-design.md` (implemented, on `main`)

Batch B makes the CMS mirror the live site: the nav order, and the content
each editor actually exposes. Nothing here is a crash or a data-loss bug —
Batch A covered those. The theme is *an editor should be able to change what
they can see, and find it where they expect*.

Verification uses the same local replica described in Batch A §1.

---

## 1. Scope

| ID | Item | Summary |
|----|------|---------|
| B1 | 3  | CMS sidebar order should follow the live nav |
| B2 | 2  | About Us: breadcrumb into the shared system; headings, Affiliations and Accreditation become editable |
| B3 | 12 | Standards: two sections have no inputs; editor order doesn't match the page |
| B4 | 15 | Service Charter: all five charter blocks are hardcoded |
| B5 | 4  | Remove the LinkedIn field from Meet Our Team |
| B6 | 1  | Stale content: 74 dead rows, a missing field, three missing breadcrumb slugs |

### Decisions taken

| Decision | Choice |
|---|---|
| About Us scope | Headings **and** Affiliations **and** Accreditation |
| Charter block editing | Title + textarea per block; blank line separates paragraphs, one line per bullet |
| Standards editor | Fill the gaps **and** reorder sections to match the live page |
| Stale rows | Delete, after a dump |

### Out of scope

Batch C: item 10 (calibration brands), 11 (upload-instead-of-link elsewhere),
13 (event image previews), 6a (calendar day-pills), 17b (CMS UI polish).

---

## 2. The changes

### B1 — Sidebar order

The live nav runs Home → About Us → Our Services → Training → Certification →
Calibration → Standards → Updates → Customer Care → Contact Us. The CMS
matches except that **Our Services sits above About Us**. Moving one block in
`admin/includes/sidebar.php` aligns them.

Breadcrumb Images stays pinned directly under Home. It has no live-nav
counterpart — it's a cross-cutting tool, not a page — and it is the most
frequently used entry after the home page.

`Certification Status Page` also has no live-nav counterpart; it stays in the
Certification group where it already is.

### B2 — About Us

**Breadcrumb.** `about-us.php` reads its own `about_breadcrumb_bg` key rather
than the shared `breadcrumb_bg_{slug}` system, which is why it is absent from
Breadcrumb Images. Switch it to `get_breadcrumb_bg('about-us')` and add the
entry, carrying the existing stored value across in the migration so the
current image is preserved.

**Headings.** 13 of the page's 14 headings are hardcoded: *About Us*,
*Vision & Mission*, *Vision*, *Mission*, *Our Core Values*, the five value
names, *Brief History*, *Our Affiliations*, *ESWASA Accreditation*. Each gets
a key with the current text as its default, so the page is unchanged until
someone edits it.

**Affiliations.** Currently a hardcoded PHP array of 8 logos
(`src`/`alt`/`href`) inside the template. Becomes
`about_affiliation_{1..10}_logo` / `_alt` / `_url`, mirroring the
`index_affiliation_*` convention already used by the home page editor —
including its cropper-backed image upload. Empty slots render nothing, so the
count is effectively variable without a repeater table.

**Accreditation.** One logo (SADCAS) plus a caption. Becomes
`about_accreditation_title`, `about_accreditation_body`, and
`about_accreditation_{1..4}_logo` / `_alt` / `_url` on the same pattern.

### B3 — Standards

Two genuine gaps, both rendered on the page with no way to edit them:

- **`std_sector_1..8`** — the eight sector names. They are in the editor's
  key array, so they load and save, but no input was ever added.
- **`std_info_item_1..4`** — the four Information Centre items. Absent from
  the editor entirely.

Both get inputs. The editor's sections are then reordered to follow the live
page: Breadcrumb & Meta → About → Sectors → Catalogue → What is a Standard →
Benefits → Process → Proposal → Work Programmes → Purchase → Information
Centre → AfCFTA → NEP → CTA. Today Affiliations and Information Centre are
transposed relative to the page, and Sectors and Catalogue have no group of
their own.

This is field reorganisation only. No key is renamed and no stored value
moves, so there is no migration and no risk to existing content.

### B4 — Service Charter

All five blocks are hardcoded, and the editor says so on screen. Each becomes
a title plus a body textarea, using the convention already established by
`train_about_list_items()` and `pc_paragraphs_html()`: a blank line separates
paragraphs, and a single newline separates bullets.

| Block | Keys |
|---|---|
| Who We Are | `charter_who_title`, `charter_who_body` |
| Our Service Standards | `charter_standards_title`, `charter_standards_intro`, `charter_commit_{1..8}_label`, `charter_commit_{1..8}_body` |
| Our Core Values | `charter_values_title`, `charter_values_items` |
| What We Ask Of You | `charter_ask_title`, `charter_ask_items` |
| If We Fall Short | `charter_short_title`, `charter_short_intro`, `charter_short_items` |

The commitments grid is a six-item label/description layout, so it keeps
discrete fields rather than a textarea — the two halves render into different
elements. Eight slots are provided so a seventh can be added without a code
change; empty slots render nothing.

Every default is the current on-page text, so the rendered page is byte-identical
until someone edits it. The "these blocks remain in code" notice is removed.

### B5 — LinkedIn

Remove the LinkedIn URL input from the add and edit forms in
`admin/pages/about_team.php`, and the corresponding markup in
`Meetourteam.php`. Safe: 0 of 9 members have a value.

The `social_linkedin` column stays on `eswasa_team_members`. Dropping it buys
nothing and makes the change harder to reverse; it simply stops being written
or read.

### B6 — Stale content

- **Delete 74 dead `page_content` rows**: `ms_doc_1..11_title/_url` (22),
  superseded by the `certification_documents` table, and
  `train_cal_session_1..13_*` (52), superseded by `training_sessions` /
  `training_intakes`. Verified unreferenced by any PHP file.
- **`train_about_bank_swift`** is rendered on the training page but has no
  input; the surrounding bank fields all do. Add it.
- **Three breadcrumb slugs** are requested by front-end pages but missing
  from Breadcrumb Images: `certification_status`, `event-details`, `tenders`.
  Add them, along with `about-us` from B2.

---

## 3. Risks

- **B2 and B4 add roughly 90 keys between them.** Every one takes its current
  on-page text as its default, so an un-edited site renders exactly as it does
  now. The failure mode is a mistyped default, which shows up immediately as
  changed text on the page — checked by diffing the rendered page before and
  after.
- **B3 reorders a 711-line file.** Field reorganisation only; verified by
  confirming the same set of input names before and after, and that a save
  round-trips every value.
- **B6 deletes rows.** Take a dump first. The statements name the exact key
  patterns and are idempotent.
- **B1 changes muscle memory** for anyone used to the current sidebar. It is
  the change the reporter asked for.

## 4. Verification

Per item, against the local replica:

1. Rendered output of every affected public page is diffed before and after.
   Only intended text may change.
2. Each new field is edited in the admin, saved, and confirmed to change the
   live page — then reverted.
3. The full smoke sweep from Batch A is re-run: every public page 200, every
   admin page 200, no PHP notices, warnings or fatals.
