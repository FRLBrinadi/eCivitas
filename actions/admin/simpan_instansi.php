<?php
// File: eCivitas_Refactored_V2/actions/admin/simpan_instansi.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

// Validasi Keamanan
verify_csrf();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            trim($_POST['nama_resmi']), 
            trim($_POST['kelurahan']), 
            trim($_POST['kecamatan']), 
            trim($_POST['alamat']),
            trim($_POST['nama_ketua']), 
            trim($_POST['jabatan_ketua'])
        ];

        // Cek apakah ID 1 sudah ada?
        $check = $pdo->query("SELECT count(*) FROM m_instansi WHERE id = 1")->fetchColumn();

        if ($check > 0) {
            // UPDATE (Jika sudah ada)
            $sql = "UPDATE m_instansi SET 
                    nama_resmi = ?, kelurahan = ?, kecamatan = ?, alamat = ?, 
                    nama_ketua = ?, jabatan_ketua = ? 
                    WHERE id = 1";
        } else {
            // INSERT (Jaga-jaga jika tabel kosong)
            $sql = "INSERT INTO m_instansi (nama_resmi, kelurahan, kecamatan, alamat, nama_ketua, jabatan_ketua, id) 
                    VALUES (?, ?, ?, ?, ?, ?, 1)";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        $_SESSION['success'] = "Data instansi berhasil diperbarui.";
        header("Location: " . BASE_URL . "views/admin/pengaturan_instansi.php");
        exit;

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>