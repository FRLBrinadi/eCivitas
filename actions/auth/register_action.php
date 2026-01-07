<?php
// File: actions/auth/register_action.php
require_once __DIR__ . '/../../config/app.php';
// Pastikan $pdo sudah tersedia melalui app.php atau database.php

verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Ambil Input & Bersihkan spasi
    $nama     = trim($_POST['nama_lengkap']);
    $username = strtolower(trim($_POST['username'])); // Username biasanya case-insensitive
    $email    = strtolower(trim($_POST['email']));
    $nik      = trim($_POST['nik']);
    $password = $_POST['password']; // Jangan trim password agar spasi yang disengaja tetap ada
    $role     = 'warga';

    // 2. Validasi Input Dasar
    if (empty($nama) || empty($username) || empty($email) || empty($nik) || empty($password)) {
        $_SESSION['error'] = "Semua kolom wajib diisi!";
        header("Location: " . BASE_URL . "views/auth/register.php");
        exit;
    }

    // Validasi Panjang NIK (Harus 16 Digit)
    if (strlen($nik) !== 16 || !is_numeric($nik)) {
        $_SESSION['error'] = "NIK harus berjumlah 16 digit angka!";
        header("Location: " . BASE_URL . "views/auth/register.php");
        exit;
    }

    try {
        // 3. DEBUGGING DUPLIKASI: Cek data mana yang sudah ada
        $stmt = $pdo->prepare("SELECT username, email, nik FROM users WHERE username = ? OR email = ? OR nik = ? LIMIT 1");
        $stmt->execute([$username, $email, $nik]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            if ($existing['username'] === $username) {
                $_SESSION['error'] = "Username sudah digunakan!";
            } elseif ($existing['email'] === $email) {
                $_SESSION['error'] = "Email sudah terdaftar!";
            } elseif ($existing['nik'] === $nik) {
                $_SESSION['error'] = "NIK sudah terdaftar!";
            }
            header("Location: " . BASE_URL . "views/auth/register.php");
            exit;
        }

        // 4. Hash Password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // 5. Simpan ke Database dengan Transaction (opsional tapi disarankan)
        $pdo->beginTransaction();

        $sql = "INSERT INTO users (nama_lengkap, username, email, nik, password, role) 
                VALUES (:nama, :user, :email, :nik, :pass, :role)";
        
        $insert = $pdo->prepare($sql);
        $insert->execute([
            'nama'  => $nama,
            'user'  => $username,
            'email' => $email,
            'nik'   => $nik,
            'pass'  => $password_hash,
            'role'  => $role
        ]);

        $pdo->commit();

        // 6. Sukses
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
        header("Location: " . BASE_URL . "views/auth/login.php");
        exit;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        
        // Log error asli untuk admin, tampilkan pesan ramah untuk user
        error_log("Register Error: " . $e->getMessage());
        $_SESSION['error'] = "Terjadi kesalahan sistem saat menyimpan data.";
        header("Location: " . BASE_URL . "views/auth/register.php");
        exit;
    }
}