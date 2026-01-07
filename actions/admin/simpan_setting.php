<?php
// File: actions/admin/simpan_setting.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak.");
}

verify_csrf(); // Validasi Token Keamanan

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_id = $_POST['jenis_id'];

    try {
        // Mulai Transaksi (Biar aman, kalau error satu batal semua)
        $pdo->beginTransaction();

        // 1. UPDATE HEADER DOKUMEN
        $stmt = $pdo->prepare("UPDATE m_jenis_dokumen SET kode_surat=?, nama_dokumen=?, deskripsi=? WHERE id=?");
        $stmt->execute([
            trim($_POST['kode_surat']),
            trim($_POST['nama_dokumen']),
            $_POST['deskripsi'],
            $jenis_id
        ]);

        // 2. PROSES COMPONENTS (FIELD)
        if (isset($_POST['components']) && is_array($_POST['components'])) {
            $sqlInsert = "INSERT INTO m_form_template (jenis_id, label_field, tipe_input, data_source, urutan) VALUES (?, ?, ?, ?, ?)";
            $sqlUpdate = "UPDATE m_form_template SET label_field=?, tipe_input=?, data_source=?, urutan=? WHERE id=?";

            foreach ($_POST['components'] as $comp) {
                $label  = trim($comp['label_field']);
                $tipe   = $comp['tipe_input'];
                $source = $comp['data_source'];
                $urutan = $comp['urutan'];
                $id     = $comp['id'];

                if (empty($id)) {
                    // ID Kosong = INSERT BARU
                    $stmt = $pdo->prepare($sqlInsert);
                    $stmt->execute([$jenis_id, $label, $tipe, $source, $urutan]);
                } else {
                    // ID Ada = UPDATE DATA LAMA
                    $stmt = $pdo->prepare($sqlUpdate);
                    $stmt->execute([$label, $tipe, $source, $urutan, $id]);
                }
            }
        }

        // 3. PROSES LAMPIRAN (FILE)
        if (isset($_POST['lampiran']) && is_array($_POST['lampiran'])) {
            $sqlInsertLamp = "INSERT INTO m_syarat_lampiran (jenis_id, nama_lampiran) VALUES (?, ?)";
            $sqlUpdateLamp = "UPDATE m_syarat_lampiran SET nama_lampiran=? WHERE id=?";

            foreach ($_POST['lampiran'] as $lamp) {
                $nama = trim($lamp['nama_lampiran']);
                $id   = $lamp['id'];

                if (empty($id)) {
                    // INSERT
                    $stmt = $pdo->prepare($sqlInsertLamp);
                    $stmt->execute([$jenis_id, $nama]);
                } else {
                    // UPDATE
                    $stmt = $pdo->prepare($sqlUpdateLamp);
                    $stmt->execute([$nama, $id]);
                }
            }
        }

        // Simpan Permanen
        $pdo->commit();

        $_SESSION['success'] = "Pengaturan form berhasil disimpan!";
        header("Location: " . BASE_URL . "views/admin/setting_form.php?id=" . $jenis_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack(); // Batalkan jika ada error
        die("Error System: " . $e->getMessage());
    }
}
?>