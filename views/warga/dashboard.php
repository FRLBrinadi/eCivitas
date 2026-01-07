<?php
// File: views/warga/dashboard.php
require_once __DIR__ . '/../../config/app.php';

// Cek Login Warga
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'warga') {
    redirect('views/auth/login.php');
}

$id_user = $_SESSION['user_id'];

// 1. Ambil Statistik
// Variabel $pdo tersedia otomatis dari app.php
// $stats = [
//     'total'     => $pdo->query("SELECT COUNT(*) FROM t_pengajuan WHERE user_id = $id_user AND deleted_at IS NULL")->fetchColumn(),
//     'pending'   => $pdo->query("SELECT COUNT(*) FROM t_pengajuan WHERE user_id = $id_user AND status = 'Pending' AND deleted_at IS NULL")->fetchColumn(),
//     'proses'    => $pdo->query("SELECT COUNT(*) FROM t_pengajuan WHERE user_id = $id_user AND status = 'Proses' AND deleted_at IS NULL")->fetchColumn(),
//     'revisi'    => $pdo->query("SELECT COUNT(*) FROM t_pengajuan WHERE user_id = $id_user AND status = 'Revisi' AND deleted_at IS NULL")->fetchColumn(),
//     'disetujui' => $pdo->query("SELECT COUNT(*) FROM t_pengajuan WHERE user_id = $id_user AND status = 'Disetujui' AND deleted_at IS NULL")->fetchColumn(),
//     'ditolak'   => $pdo->query("SELECT COUNT(*) FROM t_pengajuan WHERE user_id = $id_user AND status = 'Ditolak' AND deleted_at IS NULL")->fetchColumn(),
// ];  

// 1. Inisialisasi nilai awal 0 (Supaya tidak error undefined index jika datanya kosong)
$stats = [
    'total'     => 0,
    'pending'   => 0,
    'proses'    => 0,
    'revisi'    => 0,
    'disetujui' => 0,
    'ditolak'   => 0
];

try {
    // 2. Query SATU KALI untuk mengelompokkan data berdasarkan status
    // Menggunakan 'GROUP BY' jauh lebih ringan daripada 6x SELECT COUNT
    $sqlStats = "SELECT status, COUNT(*) as jumlah 
                 FROM t_pengajuan 
                 WHERE user_id = ? AND deleted_at IS NULL 
                 GROUP BY status";

    $stmtStats = $pdo->prepare($sqlStats);
    $stmtStats->execute([$id_user]);
    $hasilStats = $stmtStats->fetchAll(PDO::FETCH_ASSOC);

    // 3. Mapping hasil database ke array $stats
    foreach ($hasilStats as $row) {
        // Ubah status (misal: 'Pending') menjadi huruf kecil ('pending') agar cocok dengan key array
        $key = strtolower($row['status']); 
        
        // Masukkan jumlah ke key yang sesuai
        if (isset($stats[$key])) {
            $stats[$key] = $row['jumlah'];
        }

        // Tambahkan ke Total secara otomatis
        $stats['total'] += $row['jumlah'];
    }

} catch (PDOException $e) {
    // Jika error database, biarkan stats 0 agar dashboard tetap jalan
    error_log("Gagal hitung statistik: " . $e->getMessage());
}
// 2. Ambil Riwayat Pengajuan
$query = "SELECT t.*, m.nama_dokumen 
          FROM t_pengajuan t 
          JOIN m_jenis_dokumen m ON t.jenis_id = m.id 
          WHERE t.user_id = ? 
          AND t.deleted_at IS NULL
          ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_user]);
$riwayat = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<style>
    /* Styling Card agar interaktif & informatif */
    .filter-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    .filter-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important;
    }
    .filter-card.active-filter {
        border: 2px solid #0d6efd !important;
        background-color: #f8fbff;
    }
    .icon-box {
        width: 50px; height: 50px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        margin-bottom: 10px;
        font-size: 1.5rem;
    }
</style>

<div class="container py-5">
    
    <?= flash() ?>

    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-primary">Dashboard Layanan</h2>
            <p class="text-muted mb-0">Halo, <strong><?= e($_SESSION['nama'] ) ?></strong>. Berikut status pengajuan surat Anda.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="form_pengajuan.php" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i> Buat Pengajuan
            </a>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 mb-5">
        <div class="col">
            <div class="card h-100 filter-card active-filter text-center py-3 shadow-sm" onclick="filterTable('', this)">
                <div class="card-body">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto"><i class="bi bi-layers-half"></i></div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['total'] ?></h3>
                    <div class="fw-bold text-muted small">Total</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 filter-card text-center py-3 shadow-sm" onclick="filterTable('Pending', this)">
                <div class="card-body">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto"><i class="bi bi-hourglass-split"></i></div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['pending'] ?></h3>
                    <div class="fw-bold text-muted small">Menunggu</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 filter-card text-center py-3 shadow-sm" onclick="filterTable('Proses', this)">
                <div class="card-body">
                    <div class="icon-box bg-info bg-opacity-10 text-info mx-auto"><i class="bi bi-gear-wide-connected"></i></div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['proses'] ?></h3>
                    <div class="fw-bold text-muted small">Diproses</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 filter-card text-center py-3 shadow-sm" onclick="filterTable('Revisi', this)">
                <div class="card-body">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger mx-auto"><i class="bi bi-pencil-square"></i></div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['revisi'] ?></h3>
                    <div class="fw-bold text-muted small">Revisi</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 filter-card text-center py-3 shadow-sm" onclick="filterTable('Disetujui', this, true)">
                <div class="card-body">
                    <div class="icon-box bg-success bg-opacity-10 text-success mx-auto"><i class="bi bi-check-circle-fill"></i></div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['disetujui'] ?></h3>
                    <div class="fw-bold text-muted small">Disetujui</div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 filter-card text-center py-3 shadow-sm" onclick="filterTable('Ditolak', this, true)">
                <div class="card-body">
                    <div class="icon-box bg-dark bg-opacity-10 text-dark mx-auto"><i class="bi bi-slash-circle"></i></div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['ditolak'] ?></h3>
                    <div class="fw-bold text-muted small">Ditolak</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-table me-2"></i> Riwayat Pengajuan</h5>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <div class="d-inline-flex align-items-center gap-2">
                        <label class="small text-muted fw-bold">Filter Tanggal:</label>
                        <input type="date" id="inputTanggal" class="form-control form-control-sm" style="width: 160px;">
                        <button class="btn btn-sm btn-light border" onclick="resetDate()" title="Reset Tanggal"><i class="bi bi-arrow-counterclockwise"></i></button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableRiwayat" class="table table-hover align-middle w-100 border-bottom ">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Waktu Pengajuan</th>
                            <th>Dokumen & Nomor</th>
                            <th width="15%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($riwayat as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            
                            <td data-order="<?= date('Y-m-d', strtotime($row['created_at'])) ?>"> 
                                <span class="fw-semibold"><?= tanggal_indo(date('Y-m-d', strtotime($row['created_at']))) ?></span>
                                <br>
                                <small class="text-muted"><i class="bi bi-clock"></i> <?= date('H:i', strtotime($row['created_at'])) ?> WIB</small>
                            </td>
                            
                            <td>
                                <div class="fw-bold text-primary"><?= e($row['nama_dokumen']) ?></div>
                                <span class="badge bg-light text-secondary border">No: <?= e($row['no_pengajuan']) ?></span>
                            </td>
                            
                            <td><?= status_badge($row['status']) ?></td>
                            
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="detail_pengajuan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <?php if ($row['status'] == 'Draft'): ?>
                                        

                                        <form action="<?= BASE_URL ?>actions/warga/ajukan_pengajuan.php" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success" title="Kirim / Ajukan ke Petugas" onclick="return confirm('Apakah data sudah benar? Pengajuan akan dikirim ke petugas.')">
                                                <i class="bi bi-send-fill"></i>
                                            </button>
                                        </form>

                                        <form action="<?= BASE_URL ?>actions/warga/hapus_pengajuan.php" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Draft" onclick="return confirm('Yakin hapus draft ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                   <?php elseif ($row['status'] == 'Revisi'): ?>
        
                                        <a href="edit_pengajuan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit Data">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                    <?php elseif ($row['status'] == 'Disetujui'): ?>
                                    
                                    <a href="cetak_surat.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Cetak Surat">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>

                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // 1. Inisialisasi DataTables
        var table = $('#tableRiwayat').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json",
                "search": "Cari Surat:"
            },
            "order": [[ 1, "desc" ]], // Default Sort: Tanggal Terbaru
            "pageLength": 10,
        });

        // 2. LOGIKA FILTER TANGGAL
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var inputDate = $('#inputTanggal').val();
                if (inputDate == '') return true;
                var rowDate = table.cell(dataIndex, 1).nodes().to$().attr('data-order');
                return rowDate == inputDate;
            }
        );

        $('#inputTanggal').on('change', function() {
            table.draw();
        });

        // 3. LOGIKA FILTER STATUS (Klik Card)
        window.filterTable = function(statusKeyword, cardElement, isRegex = false) {
            $('.filter-card').removeClass('active-filter');
            $(cardElement).addClass('active-filter');
            table.column(3).search(statusKeyword, isRegex, false).draw();
        };

        // 4. RESET TANGGAL
        window.resetDate = function() {
            $('#inputTanggal').val('');
            table.draw();
        }
    });
</script>