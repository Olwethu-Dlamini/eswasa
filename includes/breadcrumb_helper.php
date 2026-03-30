<?php
/**
 * Breadcrumb Helper
 * Loads a page's breadcrumb background image from the page_content table.
 *
 * Usage: include this file AFTER db_connect.php, then call get_breadcrumb_bg('page_slug')
 * The page_key stored in DB is: breadcrumb_bg_{slug}
 */

function get_breadcrumb_bg($page_slug, $default = 'assets/img/bg/breadcrumb_bg.jpg') {
    global $conn;

    // If no DB connection, ensure one exists
    if (!isset($conn) || !$conn) {
        include_once __DIR__ . '/db_connect.php';
    }

    $key = 'breadcrumb_bg_' . $page_slug;
    $stmt = $conn->prepare("SELECT content FROM page_content WHERE page_key = ? LIMIT 1");
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $path = ($row && !empty($row['content'])) ? $row['content'] : $default;
    // Cache-bust so browsers pick up new images immediately
    $file = __DIR__ . '/../' . $path;
    $ts = file_exists($file) ? filemtime($file) : time();
    return htmlspecialchars($path) . '?v=' . $ts;
}
