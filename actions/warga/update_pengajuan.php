<?php
// File: eCivitas_Refactored_V2/actions/warga/update_pengajuan.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL); exit;
}

$id      = $_POST['id'];
$user_id = $_SESSION['user_id'];
$details = $_POST['detail'] ?? [];
$files   = $_FILES['lampiran'] ?? [];

try {
    // 1. Validasi Akses
    $stmtCheck = $pdo->prepare("SELECT id FROM t_pengajuan WHERE id = :id AND user_id = :uid AND status = 'Revisi'");
    $stmtCheck->execute(['id' => $id, 'uid' => $user_id]);
    if (!$stmtCheck->fetch()) die("Akses ditolak.");

    $pdo->beginTransaction();

    // 2. Update Detail Teks (Delete All -> Insert New)
    $pdo->prepare("DELETE FROM t_pengajuan_detail WHERE pengajuan_id = ?")->execute([$id]);
    
    $stmtIns = $pdo->prepare("INSERT INTO t_pengajuan_detail (pengajuan_id, nama_field, isi_field) VALUES (?, ?, ?)");
    foreach ($details as $label => $isi) {
        $stmtIns->execute([$id, $label, trim($isi)]);
    }

    // 3. Update File (Cerdas: Hanya ganti yang diupload baru)
    $uploadDir = __DIR__ . '/../../uploads/dokumen_warga/';
    $stmtCekFile = $pdo->prepare("SELECT nama_file FROM t_lampiran WHERE pengajuan_id = ? AND tipe_lampiran = ?");
    $stmtUpdateFile = $pdo->prepare("UPDATE t_lampiran SET nama_file = ?, uploaded_at = NOW() WHERE pengajuan_id = ? AND tipe_lampiran = ?");
    $stmtInsertFile = $pdo->prepare("INSERT INTO t_lampiran (pengajuan_id, nama_file, tipe_lampiran) VALUES (?, ?, ?)");

    if (isset($files['name']) && is_array($files['name'])) {
        foreach ($files['name'] as $label_lampiran => $originalName) {
            
            // Jika ada file yang diupload (Error == 0)
            if ($files['error'][$label_lampiran] === UPLOAD_ERR_OK) {
                
                $tmpName = $files['tmp_name'][$label_lampiran];
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                
                if (in_array($ext, ['jpg','jpeg','png','pdf']) && $files['size'][$label_lampiran] <= 2097152) {
                    
                    // Cek apakah file jenis ini sudah ada sebelumnya?
                    $stmtCekFile->execute([$id, $label_lampiran]);
                    $oldData = $stmtCekFile->fetch();

                    // Generate nama baru
                    $newFileName = $id . '_' . md5($label_lampiran . time()) . '_rev.' . $ext;

                    if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                        
                        if ($oldData) {
                            // UPDATE: Hapus file fisik lama, update DB
                            if (file_exists($uploadDir . $oldData['nama_file'])) {
                                unlink($uploadDir . $oldData['nama_file']);
                            }
                            $stmtUpdateFile->execute([$newFileName, $id, $label_lampiran]);
                        } else {
                            // INSERT: Jika sebelumnya belum pernah upload file jenis ini
                            $stmtInsertFile->execute([$id, $newFileName, $label_lampiran]);
                        }
                    }
                }
            }
        }
    }

    // 4. Update Header Status -> Pending
    $pdo->prepare("UPDATE t_pengajuan SET status = 'Pending', updated_at = NOW() WHERE id = ?")->execute([$id]);

    // 5. Histori
    $pdo->prepare("INSERT INTO t_histori (pengajuan_id, user_id, status_baru, catatan) VALUES (?, ?, 'Pending', 'Revisi dikirim oleh Warga')")
        ->execute([$id, $user_id]);

    $pdo->commit();
    $_SESSION['success'] = "Revisi berhasil dikirim! Menunggu verifikasi.";
    header("Location: " . BASE_URL . "views/warga/dashboard.php");

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error Update: " . $e->getMessage());
}
?>