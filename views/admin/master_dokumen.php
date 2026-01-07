<?php
// File: eCivitas_Refactored_V2/views/admin/master_dokumen.php
require_once '../../config/app.php';

// 1. Cek Login Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak.");
}

// 2. Query Data
$stmt = $pdo->query("SELECT * FROM m_jenis_dokumen ORDER BY id DESC");
$dokumen = $stmt->fetchAll();

// 3. Setup Layout
$pageTitle = "Kelola Layanan | Admin";
// Kita hanya sisakan link DataTables karena itu plugin eksternal
$extraCss = '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">';

require_once '../../includes/header.php';
?>

<div class="d-flex">
    
    <?php require_once '../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4" style="height: 100vh; overflow-y: auto;">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h3 class="fw-bold text-dark">Kelola Jenis Layanan</h3>
                <p class="text-muted mb-0 small">Atur surat yang tersedia untuk warga.</p>
            </div>
            <a href="form_dokumen.php" class="btn btn-primary rounded-pill shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-2"></i>Tambah Layanan
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3" width="5%">No</th>
                                <th width="10%">Kode</th>
                                <th width="40%">Nama Layanan</th>
                                <th width="15%">Status</th>
                                <th width="30%" class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($dokumen as $row): ?>
                            <tr>
                                <td class="ps-4 text-muted small fw-bold"><?= $no++ ?></td>
                                <td><span class="badge bg-light text-dark border"><?= $row['kode_surat'] ?></span></td>
                                <td class="fw-bold text-primary"><?= $row['nama_dokumen'] ?></td>
                                <td>
                                    <?php if($row['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success px-3">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary px-3">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="form_dokumen.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Info">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="setting_form.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm" title="Atur Template Form">
                                        <i class="bi bi-sliders me-1"></i> Atur Form
                                    </a>
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

<?php require_once '../../includes/footer.php'; ?>