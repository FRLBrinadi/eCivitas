<?php
// File: actions/auth/change_password.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

verify_csrf();

if (!isset($_SESSION['is_login'])) {
    die("Akses Ditolak.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $user_id = $_SESSION['user_id'];
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $conf_pass = $_POST['confirm_password'];

    try {
        // 1. Ambil Password Hash User saat ini
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        // 2. Verifikasi Password Lama
        if (!password_verify($old_pass, $user['password'])) {
            $_SESSION['error'] = "Password lama salah!";
            header("Location: " . BASE_URL . "views/auth/ganti_password.php");
            exit;
        }

        // 3. Cek Konfirmasi Password Baru
        if ($new_pass !== $conf_pass) {
            $_SESSION['error'] = "Konfirmasi password baru tidak cocok!";
            header("Location: " . BASE_URL . "views/auth/ganti_password.php");
            exit;
        }
        // 4. Update Password
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $update = $pdo->prepare("UPDATE users SET password = :p WHERE id = :id");
        $update->execute(['p' => $new_hash, 'id' => $user_id]);

        $_SESSION['success'] = "Password berhasil diubah!";
        
        // REVISI REDIRECT: Cek Role untuk menentukan dashboard tujuan
        $role = $_SESSION['role'];
        if($role == 'admin') {
            header("Location: " . BASE_URL . "views/admin/dashboard.php");
        } elseif($role == 'petugas') {
            header("Location: " . BASE_URL . "views/petugas/dashboard.php");
        } else {
            header("Location: " . BASE_URL . "views/warga/dashboard.php");
        }
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: " . BASE_URL . "views/auth/ganti_password.php");
        exit;
    }

    
}
?>