<?php
$role = $_SESSION['role'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']); // Untuk deteksi menu aktif
?>
<div class="sidebar d-none d-md-block flex-shrink-0 text-white" style="width: 260px; min-height: 100vh; background-color: #1e293b;">
    <div class="p-4 border-bottom border-secondary border-opacity-25">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary rounded-3 p-2 d-flex align-items-center justify-content-center shadow-lg">
                <i class="bi bi-shield-check text-white fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold text-white mb-0" style="letter-spacing: 0.5px;">eCivitas V2</h6>
                <small class="text-secondary text-uppercase" style="font-size: 0.65rem;"><?= $role ?> PANEL</small>
            </div>
        </div>
    </div>
    
    <div class="py-2">
        <nav class="nav flex-column">
            <p class="px-4 text-uppercase text-secondary fw-bold mb-2 mt-3" style="font-size: 0.65rem; letter-spacing: 1px;">Menu Utama</p>
            
            <?php if ($role === 'admin'): ?>
                <a href="dashboard.php" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" style="color: #94a3b8;">
                    <i class="bi bi-speedometer2 me-3"></i>Dashboard
                </a>
                <a href="master_dokumen.php" class="nav-link <?= strpos($current_page, 'dokumen') !== false ? 'active' : '' ?>" style="color: #94a3b8;">
                    <i class="bi bi-sliders me-3"></i>Kelola Layanan
                </a>
                <a href="users.php" class="nav-link <?= strpos($current_page, 'user') !== false ? 'active' : '' ?>" style="color: #94a3b8;">
                    <i class="bi bi-people me-3"></i>Manajemen User
                </a>
                <a href="pengaturan_instansi.php" class="nav-link <?= $current_page == 'pengaturan_instansi.php' ? 'active' : '' ?>" style="color: #94a3b8;">
                    <i class="bi bi-building-gear me-3"></i>Setting Instansi
                </a>
                <a href="<?= BASE_URL ?>panduan.php" class="nav-link" style="color: #94a3b8;">
                    <i class="bi bi-book-half me-3"></i>Panduan Sistem
                </a>
            <?php endif; ?>

            <?php if ($role === 'petugas'): ?>
                <a href="dashboard.php" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" style="color: #94a3b8;">
                    <i class="bi bi-grid-1x2-fill me-3"></i>Dashboard
                </a>
                <a href="laporan.php" class="nav-link <?= $current_page == 'laporan.php' ? 'active' : '' ?>" style="color: #94a3b8;">
                    <i class="bi bi-file-earmark-bar-graph me-3"></i>Laporan
                </a>
                <a href="<?= BASE_URL ?>panduan.php" class="nav-link" style="color: #94a3b8;">
                    <i class="bi bi-book-half me-3"></i>Panduan Sistem
                </a>
            <?php endif; ?>

            <p class="px-4 mt-4 text-uppercase text-secondary fw-bold mb-2" style="font-size: 0.65rem; letter-spacing: 1px;">Akun</p>
            
            <?php if ($role === 'petugas'): ?>
                <a href="profile.php" class="nav-link <?= $current_page == 'profile.php' ? 'active' : '' ?>" style="color: #94a3b8;">
                    <i class="bi bi-person-circle me-3"></i>Profil Saya
                </a>
            <?php endif; ?>

            <a href="../auth/ganti_password.php" class="nav-link" style="color: #94a3b8;">
                <i class="bi bi-key me-3"></i>Ganti Password
            </a>
            
            <a href="<?= BASE_URL ?>actions/auth/logout.php" class="nav-link text-danger mt-3" onclick="return confirm('Keluar dari sistem?')">
                <i class="bi bi-box-arrow-left me-3"></i>Keluar
            </a>
        </nav>
    </div>
</div>