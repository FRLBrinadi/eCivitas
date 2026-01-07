<?php
// File: helpers/helpers.php

// 1. Fungsi Redirect Aman
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

// 2. Fungsi Escape HTML (Mencegah XSS)
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// 3. Fungsi Tanggal Indonesia
function tanggal_indo($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', $tanggal); 
    // format yyyy-mm-dd
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

// 4. Fungsi Status Badge (Konsisten di Admin, Petugas, Warga)
function status_badge($status) {
    $warna = 'secondary';
    $icon  = 'bi-question-circle';
    
    switch ($status) {
        case 'Draft':     $warna = 'secondary'; $icon = 'bi-file-earmark'; break;
        case 'Pending':   $warna = 'warning';   $icon = 'bi-hourglass-split'; break;
        case 'Proses':    $warna = 'info';      $icon = 'bi-gear-wide-connected'; break;
        case 'Revisi':    $warna = 'danger';    $icon = 'bi-pencil-square'; break;
        case 'Disetujui': $warna = 'success';   $icon = 'bi-check-circle'; break;
        case 'Ditolak':   $warna = 'dark';      $icon = 'bi-x-circle'; break;
    }

    return "<span class='badge bg-$warna bg-opacity-10 text-$warna border border-$warna border-opacity-25 rounded-pill px-3'>
                <i class='bi $icon me-1'></i> $status
            </span>";
}

// 5. CSRF Token (Generate Input di Form)
function csrf_field() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

// 6. CSRF Verify (Panggil di Action)
function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("<b>Error 419:</b> Page Expired (CSRF Token Mismatch). Silakan refresh halaman.");
        }
    }
}

// 7. Flash Message (Pesan Notifikasi)
// Cara pakai di Action: set_flash('success', 'Berhasil simpan!');
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

// Cara pakai di View: display_flash();
function flash() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $msg  = $_SESSION['flash']['message'];
        
        // Mapping class bootstrap
        $alertClass = ($type == 'error') ? 'danger' : $type;
        $icon = ($type == 'success') ? 'check-circle' : 'exclamation-circle';

        echo "<div class='alert alert-$alertClass alert-dismissible fade show shadow-sm' role='alert'>
                <i class='bi bi-$icon me-2'></i> $msg
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        
        unset($_SESSION['flash']);
    }
}
?>