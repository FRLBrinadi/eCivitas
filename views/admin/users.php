<?php
// File: eCivitas_Refactored_V2/views/admin/users.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') die("Akses Ditolak.");

// Query Data
$stmt = $pdo->query("SELECT * FROM users ORDER BY nama_lengkap ASC");
$all_users = $stmt->fetchAll();

// Filter Array
$warga_list = array_filter($all_users, function($u) { return $u['role'] === 'warga'; });
$petugas_list = array_filter($all_users, function($u) { return $u['role'] === 'petugas'; });
$admin_list = array_filter($all_users, function($u) { return $u['role'] === 'admin'; });

// Setup Layout
$pageTitle = "Manajemen User | Admin";
// Kita hanya sisakan link DataTables karena itu plugin eksternal
$extraCss = '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">';

require_once '../../includes/header.php';
?>

<div class="d-flex">
    
    <?php require_once '../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4" style="height: 100vh; overflow-y: auto;">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h3 class="fw-bold text-dark">Manajemen Pengguna</h3>
            <a href="form_user.php" class="btn btn-primary rounded-pill shadow-sm fw-bold">
                <i class="bi bi-person-plus-fill me-2"></i>Tambah User
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold text-dark" id="warga-tab" data-bs-toggle="tab" data-bs-target="#warga">
                            Warga <span class="badge bg-secondary ms-1 rounded-pill"><?= count($warga_list) ?></span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-dark" id="petugas-tab" data-bs-toggle="tab" data-bs-target="#petugas">
                            Petugas <span class="badge bg-secondary ms-1 rounded-pill"><?= count($petugas_list) ?></span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-dark" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin">
                            Admin
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="userTabsContent">
                    <div class="tab-pane fade show active" id="warga"><?php renderTable($warga_list); ?></div>
                    <div class="tab-pane fade" id="petugas"><?php renderTable($petugas_list); ?></div>
                    <div class="tab-pane fade" id="admin"><?php renderTable($admin_list); ?></div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php 
// Helper Render Table (Sama seperti sebelumnya, hanya dirapikan sedikit)
function renderTable($data) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="bg-light"><tr>
            <th class="ps-4">No</th><th>Nama Lengkap</th><th>Akun</th><th>Kontak</th><th>Status</th><th class="text-end pe-4">Aksi</th>
          </tr></thead><tbody>';
    
    if (empty($data)) {
        echo '<tr><td colspan="6" class="text-center py-5 text-muted">Tidak ada data.</td></tr>';
    } else {
        $no = 1;
        foreach ($data as $u) {
            $statusBadge = $u['is_active'] ? '<span class="badge bg-success-subtle text-success border border-success">Aktif</span>' : '<span class="badge bg-danger-subtle text-danger border border-danger">Non-Aktif</span>';
            echo '<tr>
                    <td class="ps-4 fw-bold text-muted small">'. $no++ .'</td>
                    <td><div class="fw-bold">'. e($u['nama_lengkap']) .'</div><small class="text-muted">NIK: '. e($u['nik'] ?? '-') .'</small></td>
                    <td><div>'. e($u['username']) .'</div></td>
                    <td><small class="d-block">'. e($u['email'] ?? '-') .'</small></td>
                    <td>'. $statusBadge .'</td>
                    <td class="text-end pe-4"><a href="form_user.php?id='. $u['id'] .'" class="btn btn-sm btn-outline-primary rounded-pill px-3">Edit</a></td>
                  </tr>';
        }
    }
    echo '</tbody></table></div>';
}

require_once '../../includes/footer.php'; 
?>