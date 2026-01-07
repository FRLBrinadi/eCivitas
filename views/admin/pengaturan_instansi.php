<?php
// File: eCivitas_Refactored_V2/views/admin/pengaturan_instansi.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak.");
}

// Ambil Data Instansi
$stmt = $pdo->query("SELECT * FROM m_instansi WHERE id = 1");
$instansi = $stmt->fetch();

// Jika database kosong total (belum di-seed), kita buat array kosong agar tidak error
if (!$instansi) {
    $instansi = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Instansi | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body class="bg-light">

    <div class="d-flex">
        <?php require_once '../../includes/sidebar.php'; ?>
        
        <div class="flex-grow-1 p-4" style="height: 100vh; overflow-y: auto;">
            
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h3 class="fw-bold text-dark">Pengaturan Instansi</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <div class="alert alert-info border-0 shadow-sm small">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Data ini akan digunakan sebagai <strong>Kop Surat</strong> dan <strong>Tanda Tangan</strong> pada hasil cetak PDF.
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-building-gear me-2"></i>Form Data Instansi</h6>
                        </div>
                        <div class="card-body p-4">

                            <?= flash() ?>

                            <form action="<?= BASE_URL ?>actions/admin/simpan_instansi.php" method="POST">
                                <?= csrf_field() ?>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Nama Instansi (RW)</label>
                                        <input type="text" name="nama_resmi" class="form-control" placeholder="Contoh: RUKUN WARGA 05" value="<?= e($instansi['nama_resmi'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Kelurahan</label>
                                        <input type="text" name="kelurahan" class="form-control" placeholder="Contoh: KELURAHAN TELUK TERING" value="<?= e($instansi['kelurahan'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Kecamatan / Kota</label>
                                    <input type="text" name="kecamatan" class="form-control" placeholder="Contoh: KECAMATAN BATAM KOTA" value="<?= e($instansi['kecamatan'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. Engku Putri No. 1..." required><?= e($instansi['alamat'] ?? '') ?></textarea>
                                </div>

                                <hr>
                                <h6 class="fw-bold text-muted mb-3">Penandatangan Surat</h6>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Nama Pejabat (Ketua)</label>
                                        <input type="text" name="nama_ketua" class="form-control" placeholder="Nama Lengkap Ketua" value="<?= e($instansi['nama_ketua'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Jabatan (Di Bawah TTD)</label>
                                        <input type="text" name="jabatan_ketua" class="form-control" placeholder="Contoh: Ketua RW 05" value="<?= e($instansi['jabatan_ketua'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Perubahan</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php require_once '../../includes/footer.php'; ?>
</body>
</html>