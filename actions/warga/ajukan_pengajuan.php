<?php
// File: eCivitas_Refactored_V2/actions/warga/ajukan_pengajuan.php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

// 1. CEK KEAMANAN (Wajib POST & CSRF)
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses Ditolak: Metode request tidak valid.");
}

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'warga') {
    header("Location: " . BASE_URL . "views/auth/login.php"); exit;
}

$id = $_POST['id'] ?? null; // Ambil dari POST
$user_id = $_SESSION['user_id'];

if ($id) {
    try {
        $pdo->beginTransaction();

        // 2. Cek Validasi: Hanya ubah jika statusnya 'Draft' dan milik user ini
        $stmt = $pdo->prepare("UPDATE t_pengajuan SET status = 'Pending', updated_at = NOW() 
                               WHERE id = :id AND user_id = :uid AND status = 'Draft'");
        $stmt->execute(['id' => $id, 'uid' => $user_id]);

        if ($stmt->rowCount() > 0) {
            // 3. Catat di History
            $pdo->prepare("INSERT INTO t_histori (pengajuan_id, user_id, status_baru, catatan) VALUES (?, ?, 'Pending', 'Diajukan ke Petugas')")
                ->execute([$id, $user_id]);
            
            $pdo->commit();
            $_SESSION['success'] = "Dokumen berhasil diajukan! Mohon tunggu verifikasi petugas.";
        } else {
            $pdo->rollBack();
            $_SESSION['error'] = "Gagal mengajukan. Pastikan status dokumen masih Draft.";
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error Database: " . $e->getMessage();
    }
}

header("Location: " . BASE_URL . "views/warga/dashboard.php");
exit;
?>