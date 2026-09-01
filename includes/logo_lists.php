<?php
/**
 * The logo strips scattered across the site — affiliations, accreditation
 * bodies, supplier brands.
 *
 * Every one of them used to be a fixed run of page_content keys
 * (about_affiliation_1..10_logo, cal_brand_1..20_image, and so on), so adding
 * one more logo meant editing PHP and the editor faced a wall of mostly-empty
 * slots. They are now rows in `logo_lists`, keyed by which strip they belong
 * to, and every strip is managed by the same admin partial.
 *
 * See docs/superpowers/specs/2026-09-01-cms-batch-d-design.md.
 */

// list_key => how the admin describes and renders it.
//   noun    singular label used in buttons and flash messages
//   url     whether entries carry an external link (brands are logos only)
//   page    the admin page that manages the list
//   label   heading shown above the manager
$LOGO_LISTS = [
    // One affiliations list, shown on the home page, Our Services and About Us.
    // These were three separate lists of the same standards bodies, maintained
    // by hand, and they had drifted apart — 10, 5 and 8 entries. Editable from
    // any of the three admin pages; `page` is overridden per page via $LL_PAGE
    // so each redirects back to itself.
    'affiliations'        => ['noun' => 'affiliation', 'url' => true,  'page' => 'index_edit.php',
                              'label' => 'Affiliations',
                              'note'  => 'Shared by the home page, Our Services and About Us — a change here shows on all three.'],
    'about_accreditation' => ['noun' => 'accrediting body', 'url' => true, 'page' => 'about_edit.php',
                              'label' => 'Accreditation'],
    'cal_brands'          => ['noun' => 'brand', 'url' => false, 'page' => 'calibration_edit.php',
                              'label' => 'Brands We Supply and Service'],
];

if (!function_exists('logo_list_rows')) {
    /**
     * Active rows for one strip, in display order. Returns [] when the table
     * is missing, so a page still renders if the migration hasn't been run.
     */
    function logo_list_rows(mysqli $conn, string $list_key): array
    {
        $out = [];
        $stmt = @$conn->prepare('SELECT logo_path, url, alt FROM logo_lists WHERE list_key = ? AND is_active = 1 ORDER BY sort_order ASC, id ASC');
        if (!$stmt) return $out;
        $stmt->bind_param('s', $list_key);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) $out[] = $row;
        $stmt->close();
        return $out;
    }
}
