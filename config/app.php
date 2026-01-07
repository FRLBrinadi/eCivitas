<?php
// File: config/app.php

$debugMode = false; // Ubah jadi false jika mau presentasi

define('APP_DEBUG', $debugMode);

if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // VS Code tidak akan protes lagi karena dia pikir $debugMode bisa berubah
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// 2. SESSION & TIMEZONE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Jakarta');

// 3. BASE URL (MANUAL - PALING AMAN)
// Sesuai nama folder di RAR Anda: eCivitas_Refactored_V2
// Pastikan folder ini ada di htdocs/eCivitas_V2/ atau sesuaikan path-nya.
define('BASE_URL', 'http://localhost/eCivitas/');

// 4. KONFIGURASI DATABASE
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecivitas'); // Sesuai nama file SQL
define('DB_USER', 'root');
define('DB_PASS', '');

// 5. LOAD CORE FILES
// Urutan: Database Class dulu, baru Helpers
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../helpers/helpers.php';
?>