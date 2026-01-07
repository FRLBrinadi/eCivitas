<?php
// File: eCivitas_Refactored_V2/views/warga/edit_pengajuan.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'warga') {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$id) die("Error: ID tidak ditemukan.");

try {
    // 1. Ambil Data Utama
    $stmt = $pdo->prepare("SELECT t.*, m.nama_dokumen, m.kode_surat 
                           FROM t_pengajuan t
                           JOIN m_jenis_dokumen m ON t.jenis_id = m.id
                           WHERE t.id = :id AND t.user_id = :uid AND t.status = 'Revisi'");
    $stmt->execute(['id' => $id, 'uid' => $user_id]);
    $data = $stmt->fetch();

    if (!$data) die("Data tidak ditemukan atau status bukan Revisi.");

    // 2. Ambil Template Field (Agar urutan & label sesuai settingan Admin)
    $stmtTpl = $pdo->prepare("SELECT * FROM m_form_template WHERE jenis_id = ? ORDER BY urutan ASC");
    $stmtTpl->execute([$data['jenis_id']]);
    $template_fields = $stmtTpl->fetchAll();

    // 3. Ambil Data Isian yang Tersimpan (Existing Values)
    // Kita jadikan Key-Value array agar mudah dicocokkan: ['Nama Lengkap' => 'Budi', 'Omzet' => '1000']
    $stmtDet = $pdo->prepare("SELECT nama_field, isi_field FROM t_pengajuan_detail WHERE pengajuan_id = ?");
    $stmtDet->execute([$id]);
    $saved_values = $stmtDet->fetchAll(PDO::FETCH_KEY_PAIR);

    // 4. Ambil Syarat Lampiran (Template File)
    $stmtSyarat = $pdo->prepare("SELECT * FROM m_syarat_lampiran WHERE jenis_id = ?");
    $stmtSyarat->execute([$data['jenis_id']]);
    $template_files = $stmtSyarat->fetchAll();

    // 5. Ambil File yang Sudah Diupload (Existing Files)
    $stmtFiles = $pdo->prepare("SELECT * FROM t_lampiran WHERE pengajuan_id = ?");
    $stmtFiles->execute([$id]);
    $uploaded_files = $stmtFiles->fetchAll();
    
    // Mapping file agar mudah dicek: ['KTP' => 'file_a.jpg']
    $existing_files_map = [];
    foreach($uploaded_files as $f) {
        $existing_files_map[$f['tipe_lampiran']] = $f['nama_file'];
    }

    // 6. Ambil Catatan Revisi
    $stmtHist = $pdo->prepare("SELECT catatan FROM t_histori WHERE pengajuan_id = ? ORDER BY id DESC LIMIT 1");
    $stmtHist->execute([$id]);
    $last_note = $stmtHist->fetchColumn();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perbaiki Pengajuan V2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <a href="dashboard.php" class="btn btn-outline-secondary mb-3 rounded-pill btn-sm">&larr; Batal</a>

                <div class="alert alert-warning border-warning shadow-sm">
                    <h5 class="alert-heading fw-bold text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill me-2"></i>Perlu Revisi!</h5>
                    <p class="mb-0 small">Catatan Petugas:</p>
                    <hr class="my-2">
                    <p class="mb-0 fw-bold text-dark">"<?= e($last_note ?? 'Mohon lengkapi data.') ?>"</p>
                </div>

                <div class="card shadow border-0">
                    <div class="card-header bg-warning text-dark py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Formulir Perbaikan</h5>
                    </div>
                    <div class="card-body p-4">

                        <form action="<?= BASE_URL ?>actions/warga/update_pengajuan.php" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            <div class="mb-4 p-3 bg-light rounded border">
                                <label class="small text-muted fw-bold text-uppercase">Layanan</label>
                                <div class="fw-bold fs-5 text-primary">
                                    <?= e($data['nama_dokumen']) ?>
                                </div>
                            </div>

                            <?php if (count($template_fields) > 0): ?>
                                <h6 class="fw-bold text-primary mb-3">Data Isian</h6>
                                <?php foreach ($template_fields as $field): ?>
                                    <?php 
                                        $label = $field['label_field'];
                                        // Cari nilai lama di array $saved_values
                                        $val = $saved_values[$label] ?? ''; 
                                        
                                        // Setting Readonly jika sumbernya Profil (Kecuali admin ubah setting)
                                        $readonly = (strpos($field['data_source'], 'profile_') !== false) ? 'readonly' : '';
                                        $bg = ($readonly) ? 'bg-light' : '';
                                    ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">
                                            <?= e($label) ?>
                                            <?= ($readonly) ? '<span class="badge bg-info ms-1">Auto</span>' : '' ?>
                                        </label>
                                        
                                        <?php if ($field['tipe_input'] === 'textarea'): ?>
                                            <textarea name="detail[<?= $label ?>]" class="form-control <?= $bg ?>" rows="3" <?= $readonly ?> required><?= e($val) ?></textarea>
                                        <?php else: ?>
                                            <input type="<?= $field['tipe_input'] ?>" name="detail[<?= $label ?>]" class="form-control <?= $bg ?>" value="<?= e($val) ?>" <?= $readonly ?> required>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Keperluan</label>
                                    <textarea name="detail[Keperluan]" class="form-control" rows="3" required><?= e($saved_values['Keperluan'] ?? '') ?></textarea>
                                </div>
                            <?php endif; ?>

                            <?php if (count($template_files) > 0): ?>
                                <h6 class="fw-bold text-success mt-4 mb-3">Dokumen Lampiran</h6>
                                <div class="p-3 border rounded bg-light">
                                    <?php foreach ($template_files as $file): ?>
                                        <?php 
                                            $labelFile = $file['nama_lampiran'];
                                            $is_uploaded = isset($existing_files_map[$labelFile]);
                                            $old_filename = $existing_files_map[$labelFile] ?? '';
                                        ?>
                                        <div class="mb-3 pb-3 border-bottom">
                                            <label class="form-label fw-bold small"><?= e($labelFile) ?></label>
                                            
                                            <?php if ($is_uploaded): ?>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-success me-2"><i class="bi bi-check-circle"></i> Terupload</span>
                                                    <a href="<?= BASE_URL ?>file.php?name=<?= $old_filename ?>" target="_blank" class="small text-decoration-none text-truncate" style="max-width: 200px;">
                                                        Lihat File Lama
                                                    </a>
                                                </div>
                                                <div class="form-text text-muted mb-1">Upload ulang jika ingin mengganti file ini.</div>
                                            <?php else: ?>
                                                <div class="text-danger small mb-1 fw-bold"><i class="bi bi-x-circle"></i> Belum diupload / File hilang</div>
                                            <?php endif; ?>

                                            <input type="file" name="lampiran[<?= $labelFile ?>]" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-warning btn-lg fw-bold shadow-sm">
                                    Simpan Perbaikan
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>