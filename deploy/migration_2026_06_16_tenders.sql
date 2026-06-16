-- ESWASA migration 2026-06-16 (Tenders)
-- Adds a Tenders section under Updates: tenders + their multi-document packs.
-- Safe to re-run: uses IF NOT EXISTS.

-- ── 1. Tenders ────────────────────────────────────────────────
-- Status is derived from closing_date (>= today = Open, else Closed); no stored
-- status column. reference_no / category are optional.
CREATE TABLE IF NOT EXISTS eswasa_tenders (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    title          VARCHAR(255) NOT NULL,
    reference_no   VARCHAR(120) DEFAULT NULL,
    category       VARCHAR(120) DEFAULT NULL,
    description    TEXT NOT NULL,
    published_date DATE NOT NULL,
    closing_date   DATE NOT NULL,
    created_at     TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_closing (closing_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── 2. Tender documents (the bid-document pack) ───────────────
-- Multiple PDFs per tender, each with a display label. Cascade on tender delete
-- is handled in app code (matching the codebase's manual-delete style).
CREATE TABLE IF NOT EXISTS eswasa_tender_documents (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    tender_id   INT NOT NULL,
    label       VARCHAR(200) NOT NULL,
    file_path   VARCHAR(255) NOT NULL,
    sort_order  INT NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tender (tender_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
