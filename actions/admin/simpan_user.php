<?php
// File: actions/admin/simpan_user.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

verify_csrf();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') die("Akses Ditolak.");

$id       = $_POST['id'] ?? null;
$nama     = trim($_POST['nama']);
$username = trim($_POST['username']);
$email    = trim($_POST['email']);
$role     = $_POST['role'];

try {
    if (empty($id)) {
        // --- CREATE NEW USER (Logic Baru) ---
        
        // 1. Cek username kembar
        $stmtCek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmtCek->execute([$username]);
        if ($stmtCek->rowCount() > 0) die("Username sudah ada!");

        // 2. Default Password = Username
        $hash = password_hash($username, PASSWORD_DEFAULT);
        
        // 3. Default Status = 1 (Aktif)
        $status = 1;

        $sql = "INSERT INTO users (nama_lengkap, username, email, role, is_active, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $username, $email, $role, $status, $hash]);

    } else {
        // --- UPDATE USER ---
        $status   = $_POST['is_active'];
        $password = $_POST['password'];

        if (!empty($password)) {
            // Update + Reset Password
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET nama_lengkap=?, email=?, role=?, is_active=?, password=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $email, $role, $status, $hash, $id]);
        } else {
            // Update Data Saja
            $sql = "UPDATE users SET nama_lengkap=?, email=?, role=?, is_active=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $email, $role, $status, $id]);
        }
    }

    header("Location: " . BASE_URL . "views/admin/users.php");
    exit;

} catch (PDOException $e) {
    die("Gagal: " . $e->getMessage());
}
?>