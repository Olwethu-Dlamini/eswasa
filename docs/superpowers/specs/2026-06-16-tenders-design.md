# Tenders (Updates ▸ Tenders) — Design

**Date:** 2026-06-16
**New page:** `tenders.php` + `admin/pages/tenders_edit.php`
**Status:** Implemented

## Goal

A dedicated Tenders section under *Updates* for current procurement
opportunities: tender notices, bid documents, and submission deadlines, managed
from the admin.

## Decisions (from brainstorming)

- **Open list + Closed archive**, split automatically by `closing_date`
  (`>= today` = Open, else Closed). Status is derived, never stored.
- **Multiple documents per tender** (notice, BOQ, terms…), each with a label.
- Category is **free text with a datalist** (Goods/Services/Works/Consultancy
  suggested, anything allowed).
- Enhance under *Updates* (new page + nav entry); no change to Publications/
  Vacancies.

## Data model — two new tables

```
eswasa_tenders
  id, title, reference_no (opt), category (opt), description,
  published_date, closing_date, created_at

eswasa_tender_documents
  id, tender_id, label, file_path, sort_order, created_at
```

Deleting a tender removes its document rows and PDF files (app-code cascade,
matching the codebase style — no DB FK). PDFs live in `admin/uploads/tenders/`.
Migration: `deploy/migration_2026_06_16_tenders.sql` (idempotent).

## Frontend (`tenders.php`)

Load all tenders, split Open (deadline ≥ today, soonest first) and Closed
(deadline < today, most recently closed first). Documents fetched in one query
and bucketed per tender (no N+1). **Open Tenders** always shown (empty state if
none); **Closed Tenders** archive only rendered when non-empty. Each tender:
title, Open/Closed badge, reference no, category, prominent closing date,
description, and its list of PDF downloads with sizes. Theme-locked
(`#2B3388`/`#fff`, Arial); mobile rules at 575px.

## Admin (`tenders_edit.php`)

- **Tenders tab:** table of all tenders (title, ref, category, closing date,
  Open/Closed badge, doc count, edit/delete).
- **Add/Edit:** shared field partial (`_tender_fields.php`) — title, ref,
  category (datalist), description, published + closing dates — plus a
  repeatable document uploader (label + PDF, JS "Add another document"). Edit
  view lists existing documents with per-document remove. PDF-only, 25 MB,
  reusing the Publications sanitize/MIME pattern.
- **Page Content tab:** breadcrumb / intro / section headings / empty state.

## Plumbing

Header *Updates* dropdown gains **Tenders** (after Vacancies). Admin registers
`tenders_edit.php` (allowed pages, page title, sidebar Updates group +
`$upd_pages`).

## Out of scope

Stored/awarded status workflow; e-submission; email alerts; changes to other
pages.
