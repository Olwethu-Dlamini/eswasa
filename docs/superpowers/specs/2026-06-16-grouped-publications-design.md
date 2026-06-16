# Grouped Publications ("folders") — Design

**Date:** 2026-06-16
**Page:** `publications.php` (Updates ▸ Publications) + `admin/pages/publications_edit.php`
**Status:** Implemented

## Goal

Turn the flat Publications list into grouped "folders": an **Annual Reports**
section plus other report categories, hide any folder that has no documents,
and let admins also create their own custom folders. Manage everything from the
existing admin editor.

## Decisions (from brainstorming)

- **Grouping = hybrid.** Automatic groups by `pub_type` **and** admin-defined
  custom folders, interleaved in a single admin-controlled order.
- **Placement.** Enhance the existing `publications.php`; no new page, no nav
  change (stays under *Updates ▸ Publications*).
- **Empty folders are hidden** on the public page.
- **Flat list inside each folder** (no year sub-grouping for now).

## Data model

New table `eswasa_publication_groups`:

| column | meaning |
|--------|---------|
| `id` | PK |
| `name` | display label |
| `type_key` | a `pub_type` value (auto group) or `NULL` (custom folder) |
| `sort_order` | single ordering space for all sections |
| `is_system` | 1 = seeded type group (rename/reorder, **not** delete) |

New column `eswasa_publications.group_id INT NULL` — the custom-folder
assignment. `NULL` = auto-grouped by `pub_type`.

Seed: 5 system groups — Annual Reports (10), Reports (20), Guidance Documents
(30), Newsletters (40), Standards (50).

**Bucketing rule:** a publication belongs to its custom folder when `group_id`
is set and that folder exists; otherwise to the system group whose `type_key`
equals its `pub_type`. Each PDF appears in exactly one folder. If an assigned
folder is missing (e.g. deleted), the row falls back to its type group.

No DB foreign key — folder deletion clears `group_id` in app code, matching the
codebase's manual-delete style. Migration is idempotent
(`deploy/migration_2026_06_16.sql`).

## Frontend (`publications.php`)

Query groups by `sort_order`; bucket publications (newest first); render each
non-empty group as a heading (`.pub-group-title`, folder icon + count badge) +
the existing `.pub-row` list. Theme-locked `#2B3388`/`#fff`, Arial; mobile rules
reuse the existing 575px breakpoint. If every group is empty → existing empty
state.

## Admin (`publications_edit.php`)

- **New "Folders" tab:** add custom folder (name + order); list all folders with
  inline rename + reorder (Save) and Delete for custom folders (system locked).
  Deleting a custom folder returns its docs to Auto (by type); files untouched.
- **Add/Edit Publication:** a **Folder** dropdown — "Auto (by type)" or a custom
  folder. Server validates the id is an existing custom folder, else `NULL`.

## Out of scope

Year sub-grouping; nav changes; changes to other pages; moving PDF storage.
