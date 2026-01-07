<?php
// File: eCivitas_Refactored_V2/actions/petugas/verifikasi_action.php

require_once __DIR__ . '/../../config/app.php';

verify_csrf();

// 1. Cek Login Petugas
if (!isset($_SESSION['is_login']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ambil Data
    $id      = $_POST['id'];
    $status  = $_POST['status'];
    $catatan = trim($_POST['catatan']);
    $user_id = $_SESSION['user_id']; // ID Petugas yang melakukan aksi

    // Validasi Catatan (Wajib jika Ditolak/Revisi)
    if (($status === 'Ditolak' || $status === 'Revisi') && empty($catatan)) {
        echo "<script>alert('Wajib mengisi catatan untuk status Ditolak atau Revisi!'); window.history.back();</script>";
        exit;
    }

    try {
        // MULAI TRANSAKSI
        $pdo->beginTransaction();

        // 1. Update Status di Header (t_pengajuan)
        // (Di V2 kita tidak simpan catatan di tabel header lagi, tapi di t_histori)
        $sqlUpdate = "UPDATE t_pengajuan SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmtUp = $pdo->prepare($sqlUpdate);
        $stmtUp->execute(['status' => $status, 'id' => $id]);

        // 2. Catat di Audit Trail (t_histori)
        // Ini fitur penting V2: Warga bisa tahu kapan & siapa yang mengubah status
        $sqlHist = "INSERT INTO t_histori (pengajuan_id, user_id, status_baru, catatan) VALUES (:pid, :uid, :stat, :cat)";
        $stmtHist = $pdo->prepare($sqlHist);
        $stmtHist->execute([
            'pid'  => $id,
            'uid'  => $user_id,
            'stat' => $status,
            'cat'  => $catatan
        ]);

        // COMMIT
        $pdo->commit();

        // Redirect
        header("Location: " . BASE_URL . "views/petugas/dashboard.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error Verifikasi: " . $e->getMessage());
    }
}
?>