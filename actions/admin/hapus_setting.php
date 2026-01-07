<?php
// File: actions/admin/hapus_setting.php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak.");
}

$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null; // 'field' atau 'file'
$parent_id = $_GET['parent'] ?? null;

if ($id && $type && $parent_id) {
    try {

        if ($type === 'field') {
            $stmt = $pdo->prepare("DELETE FROM m_form_template WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("DELETE FROM m_syarat_lampiran WHERE id = ?");
        }
        
        $stmt->execute([$id]);
        
        $_SESSION['success'] = "Item berhasil dihapus.";
        header("Location: " . BASE_URL . "views/admin/setting_form.php?id=" . $parent_id);
        exit;

    } catch (PDOException $e) {
        die("Gagal menghapus: " . $e->getMessage());
    }
} else {
    die("Parameter tidak lengkap.");
}
?>