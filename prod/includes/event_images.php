<?php
// Helpers for the eswasa_event_images table (event galleries — up to 5 images).
// Auto-creates the table on first call so no manual migration is needed.

if (!function_exists('eswasa_event_images_max')) {
    function eswasa_event_images_max(): int { return 5; }
}

if (!function_exists('eswasa_ensure_event_images_table')) {
    function eswasa_ensure_event_images_table(mysqli $conn): void {
        static $ensured = false;
        if ($ensured) return;

        @$conn->query(
            "CREATE TABLE IF NOT EXISTS eswasa_event_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                image VARCHAR(255) NOT NULL,
                sort_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_event (event_id, sort_order),
                CONSTRAINT fk_event_images_event
                    FOREIGN KEY (event_id) REFERENCES eswasa_events(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        $ensured = true;
    }
}

if (!function_exists('eswasa_get_event_images')) {
    /**
     * Returns the gallery for an event. If the gallery is empty but the
     * legacy `eswasa_events.image` cover is set, falls back to a single-item
     * synthetic list so existing data keeps rendering.
     *
     * @return array<int, array{id:?int, image:string, sort_order:int}>
     */
    function eswasa_get_event_images(mysqli $conn, int $event_id): array {
        eswasa_ensure_event_images_table($conn);

        $rows = [];
        if ($stmt = $conn->prepare("SELECT id, image, sort_order FROM eswasa_event_images WHERE event_id = ? ORDER BY sort_order ASC, id ASC")) {
            $stmt->bind_param('i', $event_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) {
                $rows[] = [
                    'id' => (int)$r['id'],
                    'image' => (string)$r['image'],
                    'sort_order' => (int)$r['sort_order'],
                ];
            }
            $stmt->close();
        }

        if (!$rows) {
            if ($stmt = $conn->prepare("SELECT image FROM eswasa_events WHERE id = ?")) {
                $stmt->bind_param('i', $event_id);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($r = $res->fetch_assoc()) {
                    $img = trim((string)($r['image'] ?? ''));
                    if ($img !== '') {
                        $rows[] = ['id' => null, 'image' => $img, 'sort_order' => 0];
                    }
                }
                $stmt->close();
            }
        }

        return $rows;
    }
}

if (!function_exists('eswasa_count_event_images')) {
    function eswasa_count_event_images(mysqli $conn, int $event_id): int {
        eswasa_ensure_event_images_table($conn);
        $n = 0;
        if ($stmt = $conn->prepare("SELECT COUNT(*) AS c FROM eswasa_event_images WHERE event_id = ?")) {
            $stmt->bind_param('i', $event_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($r = $res->fetch_assoc()) $n = (int)$r['c'];
            $stmt->close();
        }
        return $n;
    }
}
