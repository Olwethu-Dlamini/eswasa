<?php
// includes/cms_helpers.php
// Shared CMS helpers used by both front-end and admin.
// Flat key-value model on page_content (page_key UNIQUE, content TEXT).

if (!function_exists('pc_strip_text')) {
    function pc_strip_text(?string $input): string
    {
        if ($input === null) return '';
        $s = (string)$input;
        $s = strip_tags($s);
        $s = preg_replace('/&nbsp;|&#160;|\xC2\xA0/u', ' ', $s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = preg_replace("/\r\n?/", "\n", $s);
        $s = preg_replace("/[ \t]+/", ' ', $s);
        $s = preg_replace("/\n{3,}/", "\n\n", $s);
        return trim($s);
    }
}

if (!function_exists('pc_get_many')) {
    function pc_get_many(mysqli $conn, array $keys, array $defaults = []): array
    {
        $out = [];
        if (empty($keys)) return $out;
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $types = str_repeat('s', count($keys));
        $stmt = $conn->prepare("SELECT page_key, content FROM page_content WHERE page_key IN ($placeholders)");
        if ($stmt) {
            $stmt->bind_param($types, ...$keys);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $out[$row['page_key']] = $row['content'];
            }
            $stmt->close();
        }
        foreach ($keys as $k) {
            if (!array_key_exists($k, $out) || $out[$k] === null || $out[$k] === '') {
                $out[$k] = $defaults[$k] ?? '';
            }
        }
        return $out;
    }
}

if (!function_exists('pc_save')) {
    function pc_save(mysqli $conn, string $key, string $value): bool
    {
        $stmt = $conn->prepare("INSERT INTO page_content (page_key, content) VALUES (?, ?)
                                ON DUPLICATE KEY UPDATE content = VALUES(content)");
        if (!$stmt) return false;
        $stmt->bind_param('ss', $key, $value);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}

if (!function_exists('pc_save_many')) {
    function pc_save_many(mysqli $conn, array $kv): array
    {
        $errors = [];
        foreach ($kv as $k => $v) {
            if (!pc_save($conn, (string)$k, (string)$v)) {
                $errors[] = (string)$k;
            }
        }
        return $errors;
    }
}

if (!function_exists('pc_paragraphs_html')) {
    function pc_paragraphs_html(?string $text): string
    {
        $t = (string)$text;
        if ($t === '') return '';
        $paras = preg_split("/\n{2,}/", trim($t));
        $out = '';
        foreach ($paras as $p) {
            $p = trim($p);
            if ($p === '') continue;
            $out .= '<p>' . nl2br(htmlspecialchars($p, ENT_QUOTES, 'UTF-8')) . '</p>';
        }
        return $out;
    }
}

if (!function_exists('pc_h')) {
    function pc_h(?string $text): string
    {
        return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('pc_upload_image')) {
    /**
     * Handles a single file upload from $_FILES.
     * Returns relative path (e.g. "admin/uploads/foo.jpg") on success, null if no file,
     * false on error. Caller passes $field (the $_FILES key) and $prefix (filename prefix).
     * $max_bytes defaults to 5MB.
     */
    function pc_upload_image(string $field, string $upload_dir, string $prefix = 'cms', int $max_bytes = 5242880): ?string
    {
        if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        $err = $_FILES[$field]['error'] ?? UPLOAD_ERR_OK;
        if ($err !== UPLOAD_ERR_OK) return null;

        $size = (int)($_FILES[$field]['size'] ?? 0);
        if ($size <= 0 || $size > $max_bytes) return null;

        $tmp = $_FILES[$field]['tmp_name'];
        if (!is_uploaded_file($tmp)) return null;

        $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : null;
        $mime = $finfo ? finfo_file($finfo, $tmp) : ($_FILES[$field]['type'] ?? '');
        if ($finfo) finfo_close($finfo);

        $ext_map = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            'image/svg+xml' => 'svg',
        ];
        if (!isset($ext_map[$mime])) return null;
        $ext = $ext_map[$mime];

        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);
        if (!is_writable($upload_dir)) return null;

        $name = uniqid($prefix . '_', true) . '.' . $ext;
        $dest = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $name;
        if (!move_uploaded_file($tmp, $dest)) return null;

        return 'admin/uploads/' . $name;
    }
}

if (!function_exists('pc_save_base64_image')) {
    /**
     * Decode a base64 data URL (e.g. from a cropper canvas.toDataURL) and
     * write it to the upload directory. Returns the stored path
     * ("admin/uploads/foo.jpg") on success, null when there's no payload,
     * false on failure.
     */
    function pc_save_base64_image(?string $base64, string $upload_dir, string $prefix = 'cms')
    {
        if (empty($base64) || strpos($base64, 'data:image') !== 0) return null;

        $parts = explode(';', $base64, 2);
        if (count($parts) !== 2) return false;
        $type = $parts[0];
        if (strpos($parts[1], ',') === false) return false;
        $data = explode(',', $parts[1], 2)[1];
        $decoded = base64_decode($data);
        if ($decoded === false) return false;

        $ext = 'jpg';
        if (strpos($type, 'image/png')  !== false) $ext = 'png';
        if (strpos($type, 'image/webp') !== false) $ext = 'webp';

        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);
        if (!is_writable($upload_dir)) return false;

        $name = uniqid($prefix . '_', true) . '.' . $ext;
        $dest = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $name;
        if (file_put_contents($dest, $decoded) === false) return false;

        return 'admin/uploads/' . $name;
    }
}

if (!function_exists('pc_image_src')) {
    /**
     * Resolve a stored image path for use in <img src="">.
     * Accepts admin/uploads/foo.jpg | uploads/foo.jpg | bare filename | full asset path.
     */
    function pc_image_src(?string $stored, string $fallback = ''): string
    {
        $s = trim((string)$stored);
        if ($s === '') return $fallback;
        if (preg_match('#^(https?:)?//#i', $s)) return $s;
        if (strpos($s, 'admin/') === 0) return $s;
        if (strpos($s, 'uploads/') === 0) return 'admin/' . $s;
        if (strpos($s, 'assets/') === 0) return $s;
        return 'admin/uploads/' . basename($s);
    }
}
