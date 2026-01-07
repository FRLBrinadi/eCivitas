
<?php
// File: views/auth/login.php
require_once '../../config/app.php';

// Cek jika sudah login, lempar ke dashboard
if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
    if ($_SESSION['role'] == 'petugas' || $_SESSION['role'] == 'admin') {
        header("Location: " . BASE_URL . "views/petugas/dashboard.php");
    } else {
        header("Location: " . BASE_URL . "views/warga/dashboard.php");
    }
    exit;
}

// AMBIL USERNAME LAMA (Jika ada error sebelumnya)
$old_user = $_SESSION['old_username'] ?? '';
unset($_SESSION['old_username']); // Hapus agar tidak nyangkut terus
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Masuk - eCivitas</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Inter:wght@400;500&display=swap" rel="stylesheet" />
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/auth.css">
</head>
<body>
  <div class="login-card">
    
    <a href="../../index.php" class="btn-back">
        <i class="bi bi-arrow-left me-2"></i> Kembali ke Beranda
    </a>

    <div class="logo-text">eCivitas</div>

    <h1 class="welcome-title">Selamat Datang di eCivitas</h1>
    <p class="welcome-subtitle">Sistem Pengajuan Dokumen Warga Digital</p>

    <?= flash() ?>

    <form action="<?= BASE_URL ?>actions/auth/login_action.php" method="POST">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input
          type="text"
          class="form-control"
          id="username"
          name="username"
          placeholder="Masukkan username"
          value="<?= e($old_user) ?>" 
          required
          autofocus
        />
        </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input
          type="password"
          class="form-control"
          id="password"
          name="password"
          placeholder="Masukkan password"
          required
        />
      </div>

      <button type="submit" class="btn-login">Masuk</button>
    </form>

    <p class="signup-text">
      Belum punya akun? <a href="register.php" class="signup-link">Daftar di sini.</a>
    </p>

    <p class="footer-copyright">© 2025 eCivitas Team</p>
  </div>
</body>
</html>