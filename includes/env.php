<?php
/**
 * includes/env.php — single environment bootstrap for the ESWASA site.
 *
 * Two jobs:
 *   1. Decide whether PHP errors are shown to visitors (NO in production).
 *   2. Provide the database credentials in one place.
 *
 * Switching environments:
 *   - Default is PRODUCTION-safe (errors hidden, logged instead).
 *   - For local development (Laragon) set the env var APP_ENV=development
 *     — or, if you can't set env vars, change the $appEnv fallback below
 *     to 'development' on your machine only (don't commit that).
 *
 * Production database credentials (preferred): set these environment
 * variables on the host — ESWASA_DB_HOST, ESWASA_DB_USER, ESWASA_DB_PASS,
 * ESWASA_DB_NAME. If your host can't set env vars, edit the fallback
 * values on the right-hand side of each line below, after upload.
 */

if (defined('ESWASA_ENV_LOADED')) {
    return; // guard against double-include
}
define('ESWASA_ENV_LOADED', true);

// ── Environment ─────────────────────────────────────────────────────
// Explicit APP_ENV env var wins. Otherwise auto-detect: local hostnames
// (localhost, *.test, *.local, LAN IPs) are treated as development so your
// Laragon machine stays verbose with no config; everything else, including
// the live domain, defaults to production-safe.
$appEnv = getenv('APP_ENV');
if (!$appEnv) {
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
    $host = strtolower(preg_replace('/:\d+$/', '', $host)); // strip :port
    $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1'], true)
        || substr($host, -5) === '.test'
        || substr($host, -6) === '.local'
        || strpos($host, '192.168.') === 0
        || strpos($host, '10.') === 0;
    $appEnv = $isLocal ? 'development' : 'production';
}
define('APP_ENV', $appEnv);

if (APP_ENV === 'development') {
    // Local dev: surface everything.
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    // Production: never leak errors/paths to visitors — log them instead.
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// ── Database credentials ────────────────────────────────────────────
// getenv() first (production), with the current local-dev values as
// fallbacks so nothing changes on your machine.
define('DB_HOST', getenv('ESWASA_DB_HOST') ?: 'localhost');
define('DB_USER', getenv('ESWASA_DB_USER') ?: 'root');
define('DB_PASS', getenv('ESWASA_DB_PASS') !== false ? getenv('ESWASA_DB_PASS') : '');
define('DB_NAME', getenv('ESWASA_DB_NAME') ?: 'eswasa');
