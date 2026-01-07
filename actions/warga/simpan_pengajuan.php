<?php
// File: eCivitas_Refactored_V2/actions/warga/simpan_pengajuan.php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

// 1. Pastikan Request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "views/warga/dashboard.php");
    exit;
}

$user_id  = $_SESSION['user_id'];
$jenis_id = $_POST['jenis_id'] ?? null;
$details  = $_POST['detail'] ?? [];
$files    = $_FILES['lampiran'] ?? []; 

// --- FUNGSI BANTUAN UNTUK REDIRECT ERROR + SIMPAN DATA ---
function redirectWithError($msg) {
    $_SESSION['error'] = $msg;
    
    // SIMPAN DATA LAMA (FLASH DATA)
    // Kita simpan array POST 'detail' dan 'jenis_id' ke session
    $_SESSION['old_data'] = [
        'jenis_id' => $_POST['jenis_id'] ?? '',
        'detail'   => $_POST['detail'] ?? []
    ];
    
    // Redirect balik dengan membawa parameter jenis_id agar form langsung terbuka
    $queryParam = ($_POST['jenis_id']) ? "?jenis_id=" . $_POST['jenis_id'] : "";
    header("Location: " . BASE_URL . "views/warga/form_pengajuan.php" . $queryParam);
    exit;
}

// 2. VALIDASI INPUT TEXT
if (empty($jenis_id)) {
    redirectWithError("Wajib memilih jenis dokumen!");
}

// Cek detail isian
$isi_kosong = true;
foreach($details as $d) {
    if(!empty(trim($d))) $isi_kosong = false;
}
if ($isi_kosong) {
    redirectWithError("Wajib mengisi keperluan/keterangan!");
}

// --- 3. VALIDASI FILE CERDAS ---
try {
    $stmtSyarat = $pdo->prepare("SELECT COUNT(*) FROM m_syarat_lampiran WHERE jenis_id = ?");
    $stmtSyarat->execute([$jenis_id]);
    $jumlah_syarat = $stmtSyarat->fetchColumn();
} catch (Exception $e) {
    redirectWithError("Gagal mengecek syarat file.");
}

if ($jumlah_syarat > 0) {
    
    $fileCount = 0;
    if (isset($files['name']) && is_array($files['name'])) {
        foreach ($files['error'] as $key => $err) {
            if ($err === UPLOAD_ERR_OK) $fileCount++;
            
            // CEK UKURAN FILE (Max 2MB = 2097152 bytes)
            if ($files['size'][$key] > 2097152) {
                redirectWithError("File '" . $files['name'][$key] . "' terlalu besar! Maksimal 2MB.");
            }
        }
    }

    if ($fileCount === 0) {
        redirectWithError("Dokumen ini mewajibkan upload lampiran!");
    }
    
    if ($fileCount < $jumlah_syarat) {
        redirectWithError("Mohon lengkapi semua lampiran yang diminta.");
    }
}

// 4. SIAPKAN FOLDER
$uploadDir = __DIR__ . '/../../uploads/dokumen_warga/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// --- 5. TRANSAKSI DATABASE ---
try {
    $pdo->beginTransaction();

    // A. Insert Header
    $no_pengajuan = "REG/" . date('Y') . "/" . $user_id . "/" . rand(100,999);
    $sqlHeader = "INSERT INTO t_pengajuan (user_id, jenis_id, no_pengajuan, status) VALUES (?, ?, ?, 'Draft')";
    $stmt = $pdo->prepare($sqlHeader);
    $stmt->execute([$user_id, $jenis_id, $no_pengajuan]);
    $pengajuan_id = $pdo->lastInsertId();

    // B. Insert Detail Isian
    $sqlDetail = "INSERT INTO t_pengajuan_detail (pengajuan_id, nama_field, isi_field) VALUES (?, ?, ?)";
    $stmtDetail = $pdo->prepare($sqlDetail);
    foreach ($details as $key => $value) {
        // Gunakan key apa adanya (ini penting agar data konsisten)
        $stmtDetail->execute([$pengajuan_id, $key, trim($value)]);
    }

    // C. Proses Upload
    if ($jumlah_syarat > 0 && isset($files['name'])) {
        $sqlFile = "INSERT INTO t_lampiran (pengajuan_id, nama_file, tipe_lampiran) VALUES (?, ?, ?)";
        $stmtFile = $pdo->prepare($sqlFile);

        foreach ($files['name'] as $label_lampiran => $originalName) {
            if ($files['error'][$label_lampiran] === UPLOAD_ERR_OK) {
                
                $tmpName = $files['tmp_name'][$label_lampiran];
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                
                $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
                if (!in_array($ext, $allowed)) {
                    throw new Exception("Format file '$originalName' tidak didukung.");
                }

                $newFileName = $pengajuan_id . '_' . md5($label_lampiran . time()) . '.' . $ext;
                
                if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                    $stmtFile->execute([$pengajuan_id, $newFileName, $label_lampiran]);
                } else {
                    throw new Exception("Gagal mengupload file ke server.");
                }
            }
        }
    }

    // D. Insert Histori
    $pdo->prepare("INSERT INTO t_histori (pengajuan_id, user_id, status_baru, catatan) VALUES (?, ?, 'Draft', 'Draft disimpan')")
        ->execute([$pengajuan_id, $user_id]);

    $pdo->commit();
    
    // Hapus data lama jika sukses (SUKSES = DATA BARU KOSONG)
    if(isset($_SESSION['old_data'])) unset($_SESSION['old_data']);

    $_SESSION['success'] = "Berhasil! Nomor Tiket: " . $no_pengajuan;
    header("Location: " . BASE_URL . "views/warga/dashboard.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    redirectWithError("Gagal: " . $e->getMessage());
}
?>