<?php
// File: views/auth/ganti_password.php
require_once '../../config/app.php';

// Cek Login (Semua role boleh akses)
if (!isset($_SESSION['is_login'])) {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit;
}

// Tentukan tombol "Kembali" arahnya ke mana (sesuai role)
$back_link = ($_SESSION['role'] == 'warga') 
    ? '../warga/profile.php' 
    : '../petugas/dashboard.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <h5 class="mb-0 fw-bold text-dark">Ganti Password</h5>
                </div>
                <div class="card-body p-4">

                    <?= flash() ?>

                    <form action="<?= BASE_URL ?>actions/auth/change_password.php" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Password Lama</label>
                            <input type="password" name="old_password" class="form-control" required placeholder="Masukkan password saat ini">
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" required placeholder="Minimal 6 karakter" minlength="6">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" class="form-control" required placeholder="Ulangi password baru">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-bold">Simpan Password Baru</button>
                            <a href="<?= $back_link ?>" class="btn btn-light text-muted">Batal & Kembali</a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>