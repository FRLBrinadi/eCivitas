<?php
// File: eCivitas_Refactored_V2/views/petugas/profile.php
require_once '../../config/app.php';

// 1. Cek Login & Role Petugas
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

// 2. PROSES UPDATE PROFIL
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // verify_csrf(); // Validasi Token Keamanan

    try {
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $no_hp = trim($_POST['no_hp']);
        
        // Update Database (Hanya field umum, Username/Password/Role tidak bisa diubah sendiri)
        $sql = "UPDATE users SET nama_lengkap = ?, email = ?, no_hp = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $email, $no_hp, $user_id]);
        
        // Update Session Nama agar Sidebar langsung berubah
        $_SESSION['nama'] = $nama;
        
        $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <i class='bi bi-check-circle-fill me-2'></i> Profil berhasil diperbarui!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
                
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <i class='bi bi-exclamation-triangle-fill me-2'></i> Gagal update: " . $e->getMessage() . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    }
}

// 3. AMBIL DATA TERBARU
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Setup Layout
$pageTitle = "Profil Petugas | eCivitas";
$extraCss = '<style>.sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff !important; background: rgba(255,255,255,0.05); border-left: 3px solid #3b82f6; }</style>';

require_once '../../includes/header.php';
?>

<div class="d-flex">
    
    <?php require_once '../../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4" style="height: 100vh; overflow-y: auto;">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h3 class="fw-bold text-dark">Profil Saya</h3>
            <div class="text-muted small">Kelola informasi akun Anda</div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                
                <?= $msg ?>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-5">
                        
                        <form method="POST">
                            <!-- <?= csrf_field() ?> -->
                            
                            <div class="text-center mb-4">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 90px; height: 90px; font-size: 2.5rem; font-weight: bold;">
                                    <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                                </div>
                                <h5 class="fw-bold mb-1"><?= e($user['username']) ?></h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill"><?= e($user['role']) ?></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?= e($user['nama_lengkap']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">No. HP / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="no_hp" class="form-control" value="<?= e($user['no_hp'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary fw-bold py-2">
                                    <i class="bi bi-save me-2"></i> Simpan Perubahan
                                </button>
                                <a href="dashboard.php" class="btn btn-light text-muted py-2">Kembali ke Dashboard</a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>