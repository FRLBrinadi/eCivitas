<?php
require_once '../../config/app.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | eCivitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #F5F7FA; font-family: 'Inter', sans-serif; }
        .register-card { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body class="auth-bg">

<div class="container">
    <div class="register-card card auth-card shadow-lg border-0">
        <h3 class="text-center fw-bold text-primary mb-4">Daftar Akun Baru</h3>

        <?= flash() ?>

        <form action="<?= BASE_URL ?>actions/auth/register_action.php" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required placeholder="Contoh: Budi Santoso">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIK</label>
                    <input type="text" inputmode="numeric"name="nik" class="form-control" maxlength="16" minlength="16" placeholder="16 Digit NIK" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Username unik">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required placeholder="email@contoh.com">
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Daftar Sekarang</button>
        </form>

        <p class="text-center mt-3 text-muted">
            Sudah punya akun? <a href="login.php" class="text-decoration-none">Masuk disini</a>
        </p>
    </div>
</div>

</body>
</html>