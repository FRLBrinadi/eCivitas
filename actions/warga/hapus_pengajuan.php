<?php
// File: eCivitas_Refactored_V2/actions/warga/hapus_pengajuan.php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

// 1. CEK TOKEN CSRF (WAJIB)
// Fungsi ini akan otomatis mati (die) jika token tidak cocok
verify_csrf();

// 2. Pastikan Method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses Ditolak: Metode request tidak valid.");
}

// 3. Cek Login
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'warga') {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit;
}

$id = $_POST['id'] ?? null; // Ambil dari $_POST, bukan $_GET
$user_id = $_SESSION['user_id'];

if ($id) {
    try {
        // SOFT DELETE Logic
        // Hanya boleh hapus jika status Draft atau Pending
        $sql = "UPDATE t_pengajuan SET deleted_at = NOW() 
                WHERE id = :id AND user_id = :uid 
                AND (status = 'Draft' OR status = 'Pending')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'uid' => $user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Pengajuan berhasil dibatalkan (Data diarsipkan).";
        } else {
            $_SESSION['error'] = "Gagal menghapus. Dokumen mungkin sudah diproses petugas.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error Database: " . $e->getMessage();
    }
}

header("Location: " . BASE_URL . "views/warga/dashboard.php");
exit;
?>