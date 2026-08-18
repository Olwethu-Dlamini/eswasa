# ESWASA CMS — Batch C ("New capability") design

**Date:** 2026-08-18
**Status:** **implemented and pushed to `main`** (2026-08-18)
**Predecessors:** Batch A (bugs) and Batch B (structure & parity), both on `main`

| Item | Commit | Outcome |
|------|--------|---------|
| C6 save guard | `27a653d` | The POST that emptied 131 keys in Batch B now changes only what it carries |
| C2 document uploads | `49a80b1` | 7 fields converted; a second live 404 (the TC Apply button) found and fixed |
| C1 brands | `44fb4e3` | Add and remove both work; 12 slots → 20 |
| C3 image previews | `0e1e053` | Thumbnails with per-image remove, in the shared cropper |
| C4 calendar pills | `361dfce` | Every training on a day renders as its own clickable pill |
| C5 interface polish | `bfd32de` | Sidebar fade/nowrap, accordion conflict, page-jump, reduced motion |

### Notes from implementation

**C1 needed a second fix nobody predicted.** Adding a "remove" control was
not enough: `pc_get_many()` falls back to a code default whenever a stored
value is empty, so the populated per-brand defaults resurrected any brand an
editor removed. Removal only works because those defaults were deleted and
`page_content` became the single source of truth for brands.

**C2 found another dead link.** `tcp_apply_button_url` pointed at
`admin/uploads/tc_membership_application.pdf`, which has never existed — the
Technical Committee "Apply" button was 404ing exactly like the prospectus in
Batch A. Repointed at the registration form that is in the repository.

**Verification without a browser.** No Chrome is installed on this machine and
jsdom is not available, so the three JavaScript items were verified by
executing the shipped source against a minimal DOM stub: the cropper's
thumbnail strip, and the calendar's own indexing and rendering functions run
against the page's own data with deliberately overlapping intakes. This
catches logic faults but not visual ones — the pill layout and the sidebar
fade should still be eyeballed in a browser.

Batch C is the remaining UAT items: things the CMS cannot currently do, rather
than things it does wrongly. Verification uses the local replica described in
Batch A §1.

---

## 1. Scope

| ID | Item | Summary |
|----|------|---------|
| C1 | 10 | Calibration brands can't be added or removed |
| C2 | 11 | Document fields are text boxes holding server paths, not uploads |
| C3 | 13 | No preview of event images while uploading them |
| C4 | 6a | A day with several trainings shows only one |
| C5 | 17b | CMS interface polish, mainly sidebar behaviour |
| C6 | —  | A partial form submission silently blanks a page's content |

### Decisions taken

| Decision | Choice |
|---|---|
| TC application form | Point the default at the existing `assets/forms/TEC_SDU_FO_004_...doc` |
| Brands | 20 slots, with a "remove" tick-box per slot |
| Calendar day click | Each pill is individually clickable; the day cell itself is not |
| Save guard | Skip keys absent from the submission |

C6 is not on the UAT list. It is included because it was hit twice while
testing Batch B and can destroy content.

### Audit corrections

Two Batch C items are narrower than the UAT wording implies, in both cases
because the feature already partly exists:

- **Item 10** is not "no brand editor". A 12-slot editor with cropper uploads
  and alt text already exists. The gap is that an image is only ever written,
  never cleared, and all 12 slots are occupied — so no brand can be removed
  and none can be added.
- **Item 6a is latent, not visible.** All 28 current intakes are sequential,
  so nothing overlaps today. `eventByDate[key]` holds a single entry and later
  trainings overwrite earlier ones, so the fault appears the first time two
  trainings share a date.

Continuing the Batch B lesson: fields generated in `for` loops with
interpolated `name` attributes are invisible to a literal grep. Every "missing
field" below was confirmed by rendering the editor, not by searching source.

---

## 2. The changes

### C6 — Partial submissions must not blank content

**Problem.** Editors build a key list, then write every key with
`pc_strip_text($_POST[$k] ?? '')`. A key absent from the POST is therefore
saved as an empty string. A browser always submits every field, so this is
invisible in normal use — but any truncated, scripted or interrupted request
blanks every field it did not include. Reproduced twice while testing Batch B:
a POST carrying one field emptied 131 `std_*` keys, and another emptied the
About Us body text.

**Fix.** Add `pc_post_value($key)` to `cms_helpers.php`, returning `null` when
the key is absent from `$_POST` and the cleaned string when present, and have
`pc_save_many()` skip nulls. Editors then pass absent fields through as "no
change" instead of "set to empty".

Clearing a field deliberately still works: a browser submits the emptied input,
so the key is present with an empty value and is written as empty.

Applied to the shared save path, so every editor benefits without each being
rewritten.

### C2 — Document fields become uploads

Eight content keys hold document paths that an editor is expected to type:

| Key | Editor | Target exists? |
|---|---|---|
| `train_cal_prospectus_url` | Training Calendar | fixed in Batch A (A6) |
| `cert_status_footer_appeals_link_url` | Certification Status | yes |
| `cert_status_footer_complaints_link_url` | Certification Status | yes |
| `cert_status_footer_info_link_url` | Certification Status | yes |
| `ingelo_apply_button_url` | Ingelo | yes |
| `purchase_catalogue_link_url` | Purchase Standards | yes |
| `std_proposal_form_url` | Standards | yes (fixed in Batch A) |
| `tcp_apply_button_url` | Technical Committee | **no — 404 on the live page** |

Batch A solved this once for the prospectus with a local
`train_cal_upload_pdf()`. That function is promoted to a shared
`pc_upload_document()` in `cms_helpers.php` and the Batch A copy is replaced by
it, so there is one implementation rather than eight.

Each field becomes: a file picker, an optional URL box beneath it for genuinely
external links, and a status badge showing whether the current target resolves
("file found" / "file missing" / "external link"). Uploads win over the URL box
when both are present, so existing values keep working untouched — the
upload-plus-fallback pattern agreed in Batch A.

Accepts PDF and Word (`.doc`, `.docx`) — the TC registration form is a `.doc`,
so PDF-only would exclude a document already in use. Validated by content via
`finfo`, not by extension. 25 MB cap.

`tcp_apply_button_url` is additionally repointed at
`assets/forms/TEC_SDU_FO_004_Technical_Committee_Registration_Form.doc`, which
exists, fixing the dead button.

### C1 — Calibration brands

Raise the slot count from 12 to 20 and add a "Remove this brand" tick-box per
slot which clears both image and alt text on save. The image write becomes
conditional on the tick-box rather than unconditional-on-upload, which is what
made removal impossible.

Empty slots already render nothing on the public page, so slot count is a
maximum rather than a fixed number.

### C3 — Event image previews

`cropper-modal.js` shows a live preview for single file inputs via
`data-crop-preview`, but a `multiple` input (the event gallery) only appends a
hidden field per crop, with nothing on screen. An editor cropping four photos
sees no confirmation until after saving.

Add a thumbnail strip below any `multiple` crop input: each applied crop
appends a thumbnail with a remove button that discards both the thumbnail and
its hidden field. Implemented in the shared cropper so any future multi-image
field gets it too.

### C4 — Calendar day-pills

`eventByDate[key]` becomes an array. Each day cell renders one pill per
training running that day, coloured by training family and labelled with the
course code, stacked vertically inside the cell. Each pill is individually
clickable and opens the apply modal for that training; the day cell itself is
no longer a click target.

A day with one training keeps today's appearance. Cells grow to fit up to three
pills; beyond that the cell scrolls internally so the calendar grid stays
aligned. On narrow screens pills collapse to colour bars with the code as a
tooltip, since three code labels will not fit in a mobile day cell.

### C5 — CMS interface polish

Four concrete issues, rather than a general restyle:

1. **Sidebar text reflows while collapsing.** Links wrap mid-animation as the
   width animates to zero. Fixed with `white-space: nowrap` plus an opacity
   fade so the panel fades rather than squashes.
2. **The accordion and its persistence disagree.** `main.js` restores
   remembered open submenus *and* PHP marks the active page's group open, so
   two can be open at once despite the accordion being meant to allow one.
   Restoration now defers to the active group.
3. **Loading a page can scroll the whole window.** `scrollIntoView` on the
   active sidebar link scrolls the nearest scrollable ancestor, which can move
   the main page. Scoped to scroll the sidebar only.
4. **Motion preferences ignored.** Wrap the transitions in
   `@media (prefers-reduced-motion: reduce)`.

---

## 3. Risks

- **C6 changes the shared save path.** The failure mode would be a legitimate
  clear no longer working. Explicitly tested: emptying a field through the form
  must still empty it.
- **C2 touches five editors.** Each conversion is the same shape and is
  verified by uploading a document, confirming the link changes, and confirming
  the previous value still works when nothing is uploaded.
- **C4 rewrites calendar rendering.** Verified with temporary overlapping
  intakes in the replica, then removed. A single-training day must look
  unchanged.
- **C1 raises slot count**, adding 16 keys. Empty slots render nothing, so
  there is no public change until someone fills one.

## 4. Verification

As Batch B: visible text diffed across all 36 public pages before and after,
every public and admin page returning 200 with no PHP notices, warnings or
fatals, plus the per-item checks above.
