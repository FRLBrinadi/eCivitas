<?php
// File: views/admin/form_dokumen.php
require_once '../../config/app.php';

// 1. Cek Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak.");
}

// 2. Cek Mode (Tambah atau Edit?)
$id = $_GET['id'] ?? null;
$data = null;

if ($id) {
    // Mode Edit: Ambil data lama
    $stmt = $pdo->prepare("SELECT * FROM m_jenis_dokumen WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    
    if (!$data) die("Data tidak ditemukan.");
    $title = "Edit Layanan";
} else {
    // Mode Tambah
    $title = "Tambah Layanan Baru";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?> | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark"><?= $title ?></h4>
                    <a href="master_dokumen.php" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>

                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        
                        <form action="<?= BASE_URL ?>actions/admin/simpan_dokumen.php" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $data['id'] ?? 0 ?>">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Kode Surat (Singkatan)</label>
                                <input type="text" name="kode_surat" class="form-control text-uppercase" placeholder="Contoh: SKD, SKU" value="<?= e($data['kode_surat'] ?? '') ?>" required maxlength="10">
                                <div class="form-text">Maksimal 10 karakter.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Layanan / Dokumen</label>
                                <input type="text" name="nama_dokumen" class="form-control" placeholder="Contoh: Surat Keterangan Domisili" value="<?= e($data['nama_dokumen'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control" rows="3"><?= e($data['deskripsi'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">Status Layanan</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_active" id="aktif" value="1" <?= (!isset($data) || $data['is_active'] == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-success fw-bold" for="aktif">Aktif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_active" id="nonaktif" value="0" <?= (isset($data) && $data['is_active'] == 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-secondary" for="nonaktif">Non-Aktif (Sembunyikan)</label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="bi bi-save me-2"></i>Simpan Data
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