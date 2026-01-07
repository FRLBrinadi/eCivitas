<?php
// File: eCivitas_Refactored_V2/views/petugas/dashboard.php
require_once '../../config/app.php';

// 1. Cek Login
if (!isset($_SESSION['is_login']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    redirect('views/auth/login.php');
}

try {
    // 2. Data Instansi & Pengajuan
    $stmtInst = $pdo->query("SELECT nama_resmi FROM m_instansi WHERE id = 1");
    $instansi = $stmtInst->fetch();
    $nama_instansi = $instansi['nama_resmi'] ?? 'RW 05';

    $sql = "SELECT t.*, u.nama_lengkap, u.nik, u.alamat, m.nama_dokumen, m.kode_surat 
            FROM t_pengajuan t 
            JOIN users u ON t.user_id = u.id 
            JOIN m_jenis_dokumen m ON t.jenis_id = m.id
            WHERE t.deleted_at IS NULL AND t.status != 'Draft'
            ORDER BY t.created_at ASC";
    $daftar_pengajuan = $pdo->query($sql)->fetchAll();

} catch (PDOException $x) { die("Error: " . $x->getMessage()); }

// (Fungsi getBadgeColor SUDAH DIHAPUS karena pakai helper)

$pageTitle = "Dashboard Petugas V2";
$extraCss = '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">';

require_once '../../includes/header.php';
?>

<div class="d-flex">
    <?php require_once '../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 d-flex flex-column" style="height: 100vh; overflow-y: auto;">
        
        <header class="bg-white border-bottom py-3 px-4 shadow-sm sticky-top z-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">Dashboard Overview</h6>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block">
                        <p class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;"><?= e($_SESSION['nama']) ?></p>
                        <small class="text-muted text-uppercase" style="font-size: 0.7rem;">
                            Admin <?= e($nama_instansi) ?>
                        </small>
                    </div>
                    <div class="avatar-initial bg-light text-secondary border"><i class="bi bi-person"></i></div>
                </div>
            </div>
        </header>

        <div class="p-4">
            
            <?= flash() ?> 
            <div class="row g-4 mb-4">
                <div class="col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 rounded-4 h-100 d-flex justify-content-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary"><i class="bi bi-files fs-4"></i></div>
                            <div>
                                <h6 class="text-muted mb-1 small fw-bold">Total Masuk</h6>
                                <h3 class="fw-bold mb-0"><?= count($daftar_pengajuan)?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card table-card border-0 bg-white">
                <div class="filter-bar">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted mb-2">Pencarian</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0 rounded-start-3 bg-white ps-3"><i class="bi bi-search"></i></span>
                                <input type="text" id="textSearch" class="form-control form-control-filter border-start-0 ps-0" placeholder="Nama / Alamat / Tiket...">
                            </div>
                        </div>
                        <div class="col-md-2"><label class="small fw-bold text-muted mb-2">Dari Tanggal</label><input type="date" id="minDate" class="form-control form-control-filter"></div>
                        <div class="col-md-2"><label class="small fw-bold text-muted mb-2">Sampai Tanggal</label><input type="date" id="maxDate" class="form-control form-control-filter"></div>
                        <div class="col-md-2">
                            <label class="small fw-bold text-muted mb-2">Status</label>
                            <select id="filterStatus" class="form-select form-control-filter">
                                <option value="">Semua Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Proses">Proses</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Revisi">Revisi</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <button id="resetFilter" class="btn btn-light border text-muted w-100 py-2 fw-medium rounded-3"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset</button>
                        </div>
                    </div>
                </div>

                <div class="p-3">
                    <table id="tableData" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th><th>Pemohon</th><th>Dokumen & Tiket</th><th>Tanggal Masuk</th><th>Status</th><th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($daftar_pengajuan as $row): ?>
                            <tr>
                                <td class="ps-4 text-muted small text-center"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-initial bg-light border text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px;">
                                            <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= e($row['nama_lengkap']) ?></div>
                                            <div class="text-muted small d-flex flex-column" style="font-size: 0.75rem;">
                                                <span><i class="bi bi-card-heading me-1"></i><?= e($row['nik']) ?></span>
                                                <span class="text-truncate" style="max-width: 150px;" title="<?= e($row['alamat']) ?>">
                                                    <i class="bi bi-geo-alt me-1"></i><?= e($row['alamat']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-primary" style="font-size: 0.8rem; letter-spacing: 0.5px;"><i class="bi bi-ticket-perforated me-1"></i><?= e($row['no_pengajuan']) ?></span>
                                        <span class="text-dark small mt-1"><?= e($row['nama_dokumen']) ?></span>
                                    </div>
                                </td>
                                <td data-date="<?= date('Y-m-d H:i:s', strtotime($row['created_at'])) ?>">
                                    <div class="text-dark fw-medium small"><?= date('d M Y', strtotime($row['created_at'])) ?></div>
                                    <div class="text-muted" style="font-size: 0.7rem;"><?= date('H:i', strtotime($row['created_at'])) ?> WIB</div>
                                </td>
                                
                                <td><?= status_badge($row['status']) ?></td>
                                
                                <td class="text-end pe-4">
                                    <div class="btn-group-action">
                                        <a href="verifikasi.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm fw-bold d-flex align-items-center gap-2">
                                            <i class="bi bi-search"></i> Periksa
                                        </a>
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
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    // 1. Logic Filter Custom (Tanggal & Status)
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var min = $('#minDate').val();
        var max = $('#maxDate').val();
        var status = $('#filterStatus').val();
        
        // Ambil data tanggal asli dari atribut HTML data-date
        // Ini lebih aman dan tidak merusak tampilan karena mengambil data tersembunyi
        var rowNode = settings.aoData[dataIndex].nTr;
        var dateAttr = $(rowNode).find('td').eq(3).attr('data-date'); // Index 3 adalah kolom Tanggal
        
        if (!dateAttr) return true;
        var rowDate = new Date(dateAttr);

        // Logic Filter Tanggal
        if (min && rowDate < new Date(min + 'T00:00:00')) return false;
        if (max && rowDate > new Date(max + 'T23:59:59')) return false;
        
        // Logic Filter Status (Kolom Index 4)
        if (status && !data[4].includes(status)) return false;

        return true;
    });

    $(document).ready(function() {
        // 2. Inisialisasi DataTables
        var table = $('#tableData').DataTable({ 
            'order': [[ 3, 'desc' ]], // Default urut Tanggal (Index 3) Terbaru
            'dom': 'rtp',             // HANYA tampilkan: Processing(r), Table(t), Pagination(p). Search & Info DISEMBUNYIKAN.
            'pageLength': 10,
            'language': {
                'emptyTable': "Belum ada pengajuan masuk.",
                'zeroRecords': "Data tidak ditemukan."
            }
        });

        // 3. Sambungkan Input Anda ke DataTables
        $('#textSearch').on('keyup', function() { 
            table.search(this.value).draw(); 
        });

        $('#minDate, #maxDate, #filterStatus').on('change', function() { 
            table.draw(); 
        });

        $('#resetFilter').on('click', function() {
            $('#textSearch, #minDate, #maxDate, #filterStatus').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>