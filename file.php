<?php
// File: file.php (Secure File Viewer)
require_once 'config/app.php';

// 1. Cek Login
if (!isset($_SESSION['is_login'])) {
    die("Akses Ditolak.");
}

$filename = $_GET['name'] ?? null;
if (!$filename) die("File tidak ditemukan.");

// Validasi Nama File (Mencegah Directory Traversal ../..)
if (basename($filename) !== $filename) {
    die("Nama file tidak valid.");
}

// 2. Cek Hak Akses (Authorization)
// Hanya Petugas/Admin ATAU Pemilik File yang boleh lihat
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Ambil info file dari database untuk cek pemilik
$stmt = $pdo->prepare("SELECT t.user_id 
                       FROM t_lampiran l 
                       JOIN t_pengajuan t ON l.pengajuan_id = t.id 
                       WHERE l.nama_file = ?");
$stmt->execute([$filename]);
$file_owner = $stmt->fetchColumn();

if (!$file_owner) die("File tidak terdaftar di sistem.");

// Logika Izin: Boleh jika Petugas/Admin ATAU Pemilik Asli
if ($role !== 'petugas' && $role !== 'admin' && $user_id != $file_owner) {
    die("Anda tidak memiliki izin melihat dokumen ini.");
}

// 3. Tampilkan File
$filepath = __DIR__ . '/uploads/dokumen_warga/' . $filename;

if (file_exists($filepath)) {
    // Deteksi Content-Type (MIME)
    $mime = mime_content_type($filepath);
    
    // Header agar browser menampilkan file (bukan download text)
    header("Content-Type: $mime");
    header("Content-Length: " . filesize($filepath));
    
    // Baca dan outputkan file
    readfile($filepath);
    exit;
} else {
    die("File fisik tidak ditemukan di server.");
}
?>