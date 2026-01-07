<?php
// File: eCivitas_Refactored_V2/views/warga/detail_pengajuan.php
require_once '../../config/app.php';

// 1. Cek Login
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'warga') {
    header("Location: " . BASE_URL . "views/auth/login.php"); exit;
}

$id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$id) die("Error: ID tidak ditemukan.");

try {
    // 2. Ambil Data Utama
    $stmt = $pdo->prepare("SELECT t.*, m.nama_dokumen, m.kode_surat 
                           FROM t_pengajuan t
                           JOIN m_jenis_dokumen m ON t.jenis_id = m.id
                           WHERE t.id = :id AND t.user_id = :uid");
    $stmt->execute(['id' => $id, 'uid' => $user_id]);
    $data = $stmt->fetch();

    if (!$data) die("Data tidak ditemukan.");

    // 3. Ambil Detail
    $stmtDet = $pdo->prepare("SELECT nama_field, isi_field FROM t_pengajuan_detail WHERE pengajuan_id = ?");
    $stmtDet->execute([$id]);
    $details = $stmtDet->fetchAll();

    // 4. Ambil Lampiran
    $stmtFiles = $pdo->prepare("SELECT * FROM t_lampiran WHERE pengajuan_id = ?");
    $stmtFiles->execute([$id]);
    $files = $stmtFiles->fetchAll();

    // 5. Ambil History
    $stmtHist = $pdo->prepare("SELECT h.*, u.nama_lengkap, u.role 
                               FROM t_histori h 
                               JOIN users u ON h.user_id = u.id 
                               WHERE h.pengajuan_id = ? 
                               ORDER BY h.created_at DESC");
    $stmtHist->execute([$id]);
    $histories = $stmtHist->fetchAll();

} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

function getStatusColor($status) {
    if ($status == 'Disetujui') return 'success';
    if ($status == 'Pending') return 'warning';
    if ($status == 'Draft') return 'secondary';
    if ($status == 'Revisi') return 'info';
    if ($status == 'Ditolak') return 'danger';
    return 'light';
}

// Setup Layout
$pageTitle = "Detail Pengajuan";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                
                <?php if ($data['status'] === 'Pending' || $data['status'] === 'Draft'): ?>
                    <form action="<?= BASE_URL ?>actions/warga/hapus_pengajuan.php" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan dan menghapus pengajuan ini? Data tidak bisa dikembalikan.')">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        <?= csrf_field() ?> <button type="submit" class="btn btn-danger btn-sm rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-trash me-1"></i> Batalkan Pengajuan
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="card shadow-sm border-0 mb-4 rounded-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-primary">Informasi Pengajuan</h6>
                            <!-- <span class="badge bg-<?= getStatusColor($data['status']) ?>"><?= $data['status'] ?></span> -->
                            <!-- <?= status_badge($row['status']) ?> -->
                            <!-- ffrl -->
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="small text-muted fw-bold">NOMOR TIKET</label>
                                    <div class="fs-5 fw-bold text-dark font-monospace"><?= e($data['no_pengajuan'] ?? '-') ?></div>
                                </div>
                                <div class="col-6 text-end">
                                    <label class="small text-muted fw-bold">TANGGAL</label>
                                    <div><?= date('d M Y, H:i', strtotime($data['created_at'])) ?></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted fw-bold">JENIS LAYANAN</label>
                                <div class="fw-bold text-primary"><?= e($data['nama_dokumen']) ?></div>
                                <small class="text-muted">Kode: <?= e($data['kode_surat']) ?></small>
                            </div>
                            
                            <hr class="border-secondary border-opacity-10">
                            
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-check me-2"></i>Detail Data</h6>
                            <ul class="list-group list-group-flush border rounded mb-4">
                                <?php foreach ($details as $d): ?>
                                    <li class="list-group-item bg-light">
                                        <small class="d-block text-muted fw-bold text-uppercase" style="font-size: 0.7rem;"><?= e($d['nama_field']) ?></small>
                                        <span class="text-dark"><?= nl2br(e($d['isi_field'])) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-paperclip me-2"></i>Lampiran</h6>
                            <?php if (count($files) > 0): ?>
                                <div class="row g-2">
                                    <?php foreach ($files as $f): ?>
                                        <div class="col-sm-6">
                                            <a href="<?= BASE_URL ?>file.php?name=<?= $f['nama_file'] ?>" target="_blank" class="text-decoration-none">
                                                <div class="border rounded p-2 d-flex align-items-center bg-white hover-shadow transition-all">
                                                    <i class="bi bi-file-earmark-text fs-3 text-danger me-2"></i>
                                                    <div class="overflow-hidden">
                                                        <div class="text-truncate small text-dark fw-bold" style="max-width: 150px;">
                                                            <?= e($f['tipe_lampiran']) ?>
                                                        </div>
                                                        <small class="text-muted" style="font-size: 0.7rem;">Klik untuk buka</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light border text-muted small"><i class="bi bi-info-circle me-1"></i> Tidak ada lampiran.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-clock-history me-2"></i>Jejak Proses</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php foreach ($histories as $hist): ?>
                                    <div class="list-group-item p-3 border-bottom-0 border-top">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <?= status_badge($hist['status_baru']) ?>
                                                <div class="small text-muted" style="font-size: 0.75rem;">
                                                    Oleh: <strong><?= e($hist['nama_lengkap']) ?></strong>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <small class="fw-bold text-dark" style="font-size: 0.75rem;"><?= date('d M', strtotime($hist['created_at'])) ?></small>
                                                <div class="text-muted" style="font-size: 0.65rem;"><?= date('H:i', strtotime($hist['created_at'])) ?></div>
                                            </div>
                                        </div>
                                        <?php if (!empty($hist['catatan'])): ?>
                                            <div class="mt-2 p-2 bg-light border rounded small fst-italic text-secondary">
                                                "<?= e($hist['catatan']) ?>"
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>