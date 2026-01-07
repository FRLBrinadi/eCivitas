<?php
// File: eCivitas_Refactored_V2/actions/auth/login_action.php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // 1. Cek User di Database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // 2. Verifikasi Password
        // Kita gabungkan pengecekan User Ada + Password Benar jadi satu blok
        if ($user && password_verify($password, $user['password'])) {
            
            // 3. Cek Status Aktif
            if ($user['is_active'] == 0) {
                $_SESSION['error'] = "Akun dinonaktifkan. Hubungi Admin.";
                $_SESSION['old_username'] = $username; // Kembalikan username agar user tidak capek ngetik
                header("Location: " . BASE_URL . "views/auth/login.php");
                exit;
            }

            // --- LOGIN SUKSES ---
            session_regenerate_id(true);
            $_SESSION['is_login'] = true;
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['nama']     = $user['nama_lengkap'];
            $_SESSION['role']     = $user['role'];

            // Hapus sisa session username lama (jika ada)
            if(isset($_SESSION['old_username'])) unset($_SESSION['old_username']);

            // Redirect sesuai Role
            if ($user['role'] === 'admin') {
                header("Location: " . BASE_URL . "views/admin/dashboard.php");
            } elseif ($user['role'] === 'petugas') {
                header("Location: " . BASE_URL . "views/petugas/dashboard.php");
            } else {
                header("Location: " . BASE_URL . "views/warga/dashboard.php");
            }
            exit;

        } else {
            // --- LOGIN GAGAL (GENERIK) ---
            // Kita tidak memberitahu apakah username-nya yang salah atau password-nya.
            // Ini membuat hacker bingung.
            $_SESSION['error'] = "Username atau Password salah!";
            
            // Tetap simpan username untuk kenyamanan (UX), 
            // tapi pesan errornya tetap rahasia.
            $_SESSION['old_username'] = $username;
            
            header("Location: " . BASE_URL . "views/auth/login.php");
            exit;
        }

    } catch (PDOException $e) {
        error_log($e->getMessage()); // Log error di server (jangan tampilkan ke user)
        $_SESSION['error'] = "Terjadi kesalahan sistem.";
        header("Location: " . BASE_URL . "views/auth/login.php");
        exit;
    }
}
?>