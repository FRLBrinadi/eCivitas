<?php
// File: actions/auth/logout.php
require_once '../../config/app.php';

// Hapus semua variabel session
$_SESSION = [];

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login dengan pesan
session_start(); // Start lagi sebentar untuk simpan pesan flash
$_SESSION['error'] = "Anda berhasil logout.";
header("Location: " . BASE_URL . "views/auth/login.php");
exit;