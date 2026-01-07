<?php
// File: eCivitas_Refactored_V2/views/petugas/laporan.php
require_once '../../config/app.php';

// 1. Cek Login
if (!isset($_SESSION['is_login']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    header("Location: " . BASE_URL . "views/auth/login.php"); exit;
}

// 2. Ambil Daftar Jenis Dokumen (Untuk Filter)
try {
    $stmt = $pdo->query("SELECT * FROM m_jenis_dokumen ORDER BY nama_dokumen ASC");
    $jenis_dokumen = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$pageTitle = "Filter Laporan | eCivitas";
$extraCss = '<style>.sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.05); border-left: 3px solid #3b82f6; }</style>';

require_once '../../includes/header.php';
?>

<div class="d-flex">
    <?php require_once '../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 d-flex flex-column" style="height: 100vh; overflow-y: auto;">
        
        <header class="bg-white border-bottom py-3 px-4 shadow-sm sticky-top">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">Laporan & Rekapitulasi</h6>
                <div class="text-end d-none d-sm-block">
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;"><?= e($_SESSION['nama']) ?></p>
                    <small class="text-muted" style="font-size: 0.75rem;">Petugas RW 05</small>
                </div>
            </div>
        </header>

        <div class="p-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 mt-3">
                        <div class="card-header bg-white py-3 text-center border-bottom">
                            <h5 class="fw-bold text-primary mb-0"><i class="bi bi-funnel-fill me-2"></i>Filter Laporan Bulanan</h5>
                        </div>
                        <div class="card-body p-5">
                            
                            <form action="cetak_laporan.php" method="GET" target="_blank">
                                
                                <div class="row g-3">
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Bulan</label>
                                        <select name="bulan" class="form-select">
                                            <?php
                                            $bulan_sekarang = date('m');
                                            $nama_bulan = [
                                                '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                                                '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                                                '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                                            ];
                                            foreach($nama_bulan as $k => $v) {
                                                $sel = ($k == $bulan_sekarang) ? 'selected' : '';
                                                echo "<option value='$k' $sel>$v</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Tahun</label>
                                        <select name="tahun" class="form-select">
                                            <?php
                                            $tahun_sekarang = date('Y');
                                            for($i = $tahun_sekarang; $i >= $tahun_sekarang-5; $i--) {
                                                echo "<option value='$i'>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Jenis Dokumen</label>
                                        <select name="jenis_id" class="form-select">
                                            <option value="">-- Semua Jenis Dokumen --</option>
                                            <?php foreach($jenis_dokumen as $dok): ?>
                                                <option value="<?= $dok['id'] ?>">
                                                    <?= e($dok['nama_dokumen']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Status Pengajuan</label>
                                        <select name="status" class="form-select">
                                            <option value="">-- Semua Status --</option>
                                            <option value="Disetujui">Disetujui (Selesai)</option>
                                            <option value="Proses">Sedang Diproses</option>
                                            <option value="Pending">Menunggu Verifikasi</option>
                                            <option value="Revisi">Perlu Revisi</option>
                                            <option value="Ditolak">Ditolak</option>
                                        </select>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <div class="d-grid gap-2">
                                    <button type="submit" name="format" value="print" class="btn btn-primary btn-lg fw-bold">
                                        <i class="bi bi-printer me-2"></i> Preview & Cetak PDF
                                    </button>
                                    <button type="submit" name="format" value="excel" class="btn btn-success btn-lg fw-bold">
                                        <i class="bi bi-file-earmark-excel me-2"></i> Export ke Excel
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>