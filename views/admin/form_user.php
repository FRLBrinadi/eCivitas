<?php
// File: views/admin/form_user.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') die("Akses Ditolak.");

$id = $_GET['id'] ?? null;
$user = null;
$title = "Tambah User Baru";
$is_edit = false;

if ($id) {
    $is_edit = true;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if(!$user) die("User tidak ditemukan");
    $title = "Edit User";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><?= $title ?></h5>
                    <a href="users.php" class="btn btn-sm btn-outline-secondary rounded-pill">Kembali</a>
                </div>
                <div class="card-body p-4">
                    
                    <?php if(!$is_edit): ?>
                        <div class="alert alert-info small border-0">
                            <i class="bi bi-info-circle me-1"></i> 
                            <strong>Password Default:</strong> Password akan disamakan dengan Username. 
                            Status akun otomatis <strong>Aktif</strong>.
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>actions/admin/simpan_user.php" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $user['id'] ?? '' ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control form-control-lg" value="<?= e($user['nama_lengkap'] ?? '') ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" name="username" class="form-control" value="<?= e($user['username'] ?? '') ?>" required <?= $is_edit ? 'readonly style="background-color:#e9ecef;"' : '' ?>>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <select name="role" class="form-select">
                                    <option value="warga" <?= (isset($user) && $user['role']=='warga') ? 'selected' : '' ?>>Warga</option>
                                    <option value="petugas" <?= (isset($user) && $user['role']=='petugas') ? 'selected' : '' ?>>Petugas</option>
                                    <option value="admin" <?= (isset($user) && $user['role']=='admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email (Opsional)</label>
                            <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>">
                        </div>

                        <?php if($is_edit): ?>
                            <hr class="my-4">
                            <h6 class="fw-bold text-danger mb-3">Zona Pengaturan Lanjutan</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Akun</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" <?= $user['is_active']==1 ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= $user['is_active']==0 ? 'selected' : '' ?>>Non-Aktif (Blokir)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Reset Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin mereset password user ini">
                                <div class="form-text text-danger">Biarkan kosong jika tidak ingin mengubah password.</div>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">Simpan Data</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>