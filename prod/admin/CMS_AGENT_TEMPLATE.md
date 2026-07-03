# CMS Agent Template — ESWASA

Shared conventions every wiring agent MUST follow so the codebase stays uniform.

## DB model
Flat key-value in `page_content (page_key UNIQUE, content TEXT, updated_at TIMESTAMP)`.

## Key naming
`{pagebase}_{component}_{index?}_{field}` — lowercase, snake_case, ASCII only.
Examples:
- `index_discover_1_title`, `index_discover_1_desc`, `index_discover_1_icon` (or `_image`)
- `cert_hero_title`, `cert_hero_subtitle`
- `cert_step_1_title`, `cert_step_1_body`
- `cert_mark_1_title`, `cert_mark_1_desc`, `cert_mark_1_image`
- `affiliation_1_logo`, `affiliation_1_url`, `affiliation_1_alt`

The pagebase prefixes are: `index`, `cert`, `prod`, `ingelo`, `ms` (managementsystems), `std` (Standards), `tcp`, `work`, `purchase`, `cal`, `train_about`, `train_cal`, `team`, `board`, `contact`, `news`, `services`, `cert_status`.

## Front-end rules
1. **Do not change layout, CSS, classes, IDs, or structure.** Swap hardcoded text/image only.
2. At the top of the page (after `<?php` opening but before HTML), include:
   ```php
   require_once __DIR__ . '/includes/db_connect.php';
   require_once __DIR__ . '/includes/cms_helpers.php';
   $pc = pc_get_many($conn, [/* all keys for this page */], [/* default values */]);
   ```
3. Render text with `<?= pc_h($pc['key']) ?>`.
4. Render multi-paragraph text with `<?= pc_paragraphs_html($pc['key']) ?>` — but only where the original page used multi-paragraph blocks. The DB stores plain text; `\n\n` separates paragraphs.
5. Render images with `<?= pc_h(pc_image_src($pc['key'], 'assets/img/default.jpg')) ?>`. Always provide a sensible fallback that already exists on disk so the page doesn't break before content is seeded.
6. Render links/URLs with `<?= pc_h($pc['key']) ?>` inside `href="..."`.
7. If a hardcoded value has any HTML (`<br>`, `&mdash;`, `&nbsp;`, `<em>`), convert it to plain-text equivalent for the default (replace `<br>` with space or newline, decode entities to UTF-8 chars: `—`, ` `, `'` etc).
8. **No structural HTML in the DB.** Admin form strips on save; renderer escapes on output.

## Admin editor rules
1. File path: `admin/pages/{pagebase}_edit.php` (or per existing convention).
2. Guard with `if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');`.
3. Include helpers: `require_once __DIR__ . '/../../includes/cms_helpers.php';`.
4. Form: `<form method="POST" enctype="multipart/form-data">`.
5. Text fields:
   - Short (≤120 chars): `<input type="text" name="<key>" class="form-control" value="<?= pc_h($pc['<key>']) ?>">`
   - Long/paragraph: `<textarea name="<key>" class="form-control" rows="N"><?= pc_h($pc['<key>']) ?></textarea>`
   - **No WYSIWYG, no markdown editor, no rich text** — plain inputs only.
6. Image fields:
   - Preview current: `<img src="../<?= pc_h(pc_image_src($pc['<key>'])) ?>" style="max-height:120px;border:1px solid #ddd">` (only if non-empty)
   - File picker: `<input type="file" name="<key>_file" accept="image/*" class="form-control">`
   - Helper text: "Leave empty to keep current image."
7. URL/link fields: plain `<input type="url">`.
8. Save handler (top of file):
   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_<pagebase>'])) {
       $text_keys = [/* list */];
       $image_keys = [/* list */];
       $kv = [];
       foreach ($text_keys as $k) {
           $kv[$k] = pc_strip_text($_POST[$k] ?? '');
       }
       foreach ($image_keys as $k) {
           $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', '<pagebase>');
           if ($path !== null) $kv[$k] = $path;
       }
       $errs = pc_save_many($conn, $kv);
       set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
       redirect_self();
   }
   $pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
   ```
9. Submit button: `<button type="submit" name="save_<pagebase>" class="btn btn-primary">Save Changes</button>`.
10. Group related fields into sections with `<h5>` headings inside `<div class="card mb-3"><div class="card-body">...</div></div>` so the form is scannable.

## Index router
If creating a new admin page that's not already in `admin/index.php $allowed_pages` and `$page_titles`, add the entry. Also update `admin/includes/sidebar.php` if the page should appear in nav.

## Commit
After completing a page (front-end edit + admin editor + index/sidebar wiring), commit immediately:
```
git add -A
git commit -m "feat(cms): wire <front-end-page> via page_content + admin editor"
```
One commit per page. Use the `Co-Authored-By` line per project convention if applicable.

## Things to NOT do
- Do NOT add `<br>`, `<p>`, `&nbsp;` etc to default text in the front-end. Keep defaults as plain UTF-8 strings.
- Do NOT install WYSIWYG, CKEditor, TinyMCE, croppers, etc.
- Do NOT change the visual design.
- Do NOT touch unrelated files.
- Do NOT mass-add new database tables — the user chose flat key-value.
