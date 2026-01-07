<?php
// File: eCivitas_Refactored_V2/views/petugas/verifikasi.php

require_once __DIR__ . '/../../config/app.php';

if (!isset($_SESSION['is_login']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit;
}

$id_pengajuan = $_GET['id'] ?? null;
if (!$id_pengajuan) die("Error: ID tidak ditemukan.");

try {
    // 1. AMBIL HEADER
    $sqlHeader = "SELECT t.*, u.nama_lengkap, u.nik, m.nama_dokumen 
                  FROM t_pengajuan t
                  JOIN users u ON t.user_id = u.id
                  JOIN m_jenis_dokumen m ON t.jenis_id = m.id
                  WHERE t.id = :id LIMIT 1";
    $stmt = $pdo->prepare($sqlHeader);
    $stmt->execute(['id' => $id_pengajuan]);
    $data = $stmt->fetch();

    if (!$data) die("Data tidak ditemukan.");

    // 2. AMBIL DETAIL ISIAN
    $sqlDetail = "SELECT * FROM t_pengajuan_detail WHERE pengajuan_id = :id";
    $stmtDet = $pdo->prepare($sqlDetail);
    $stmtDet->execute(['id' => $id_pengajuan]);
    $details = $stmtDet->fetchAll();

    // 3. AMBIL SEMUA LAMPIRAN (MULTI FILE FIX)
    $sqlFile = "SELECT * FROM t_lampiran WHERE pengajuan_id = :id";
    $stmtFile = $pdo->prepare($sqlFile);
    $stmtFile->execute(['id' => $id_pengajuan]);
    $lampiran_list = $stmtFile->fetchAll(); // Pakai fetchAll()

    // 4. AMBIL HISTORI
    $sqlHist = "SELECT h.*, u.nama_lengkap as user_name 
                FROM t_histori h 
                JOIN users u ON h.user_id = u.id 
                WHERE h.pengajuan_id = :id ORDER BY h.created_at DESC";
    $stmtHist = $pdo->prepare($sqlHist);
    $stmtHist->execute(['id' => $id_pengajuan]);
    $histories = $stmtHist->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi #<?= $data['no_pengajuan'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Verifikasi: <?= e($data['no_pengajuan']) ?></span>
            <a href="dashboard.php" class="btn btn-sm btn-outline-light">Kembali</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">
            
            <div class="col-lg-7">
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i>Data Pemohon</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td class="text-muted" width="35%">Jenis Dokumen</td><td class="fw-bold"><?= e($data['nama_dokumen']) ?></td></tr>
                            <tr><td class="text-muted">Pemohon</td><td><?= e($data['nama_lengkap']) ?></td></tr>
                            <tr><td class="text-muted">NIK</td><td><?= e($data['nik'] ?? '-') ?></td></tr>
                            <tr><td class="text-muted">Tanggal Masuk</td><td><?= date('d M Y, H:i', strtotime($data['created_at'])) ?></td></tr>
                        </table>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-success"><i class="bi bi-list-check me-2"></i>Isian Formulir</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($details as $d): ?>
                                <li class="list-group-item px-0">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">
                                        <?= e($d['nama_field']) ?>
                                    </small>
                                    <span class="fs-6"><?= nl2br(e($d['isi_field'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-paperclip me-2"></i>Lampiran Berkas</h6>
                    </div>
                    <div class="card-body bg-light">
                        <?php if (count($lampiran_list) > 0): ?>
                            <div class="row g-3">
                                <?php foreach ($lampiran_list as $file): ?>
                                    <?php 
                                        $file_url = BASE_URL . "file.php?name=" . $file['nama_file'];
                                        $ext = strtolower(pathinfo($file['nama_file'], PATHINFO_EXTENSION));
                                    ?>
                                    <div class="col-md-6">
                                        <div class="card h-100 border shadow-sm">
                                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                                <small class="fw-bold text-truncate d-block" title="<?= e($file['tipe_lampiran']) ?>">
                                                    <?= e($file['tipe_lampiran']) ?>
                                                </small>
                                            </div>
                                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                                <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                                                    <img src="<?= $file_url ?>" class="img-fluid rounded mb-2" style="max-height: 150px; object-fit: contain;">
                                                <?php else: ?>
                                                    <i class="bi bi-file-earmark-pdf text-danger display-4"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-footer bg-white border-top-0">
                                                <a href="<?= $file_url ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                                    <i class="bi bi-eye me-1"></i> Buka
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">Tidak ada berkas yang diunggah.</div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="col-lg-5">
                
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px; z-index: 10;">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-gavel me-2"></i>Keputusan Petugas</h6>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>actions/petugas/verifikasi_action.php" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Status Dokumen</label>
                                <select name="status" class="form-select form-select-lg">
                                    <option value="Pending" <?= $data['status']=='Pending'?'selected':'' ?>>⏳ Pending</option>
                                    <option value="Proses" <?= $data['status']=='Proses'?'selected':'' ?>>⚙️ Sedang Diproses</option>
                                    <option value="Disetujui" <?= $data['status']=='Disetujui'?'selected':'' ?>>✅ Setujui (Final)</option>
                                    <option value="Revisi" <?= $data['status']=='Revisi'?'selected':'' ?>>⚠️ Minta Revisi</option>
                                    <option value="Ditolak" <?= $data['status']=='Ditolak'?'selected':'' ?>>❌ Tolak</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Catatan / Alasan</label>
                                <textarea name="catatan" class="form-control" rows="4" placeholder="Wajib diisi jika status Revisi atau Ditolak..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-muted"><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($histories as $hist): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-dark"><?= $hist['status_baru'] ?></strong>
                                    <small class="text-muted"><?= date('d/m H:i', strtotime($hist['created_at'])) ?></small>
                                </div>
                                <div class="small text-muted mt-1">By: <?= e($hist['user_name']) ?></div>
                                <?php if(!empty($hist['catatan'])): ?>
                                    <div class="alert alert-light border mt-2 mb-0 p-2 small fst-italic text-secondary">
                                        "<?= e($hist['catatan']) ?>"
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>

</body>
</html>