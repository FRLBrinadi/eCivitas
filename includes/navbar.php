<?php
$role = $_SESSION['role'] ?? 'guest';
$nama = $_SESSION['nama'] ?? 'Pengunjung';
$is_login = isset($_SESSION['is_login']);
$current_page = basename($_SERVER['PHP_SELF']);

// Link Dashboard
$dashLink = '#';
if ($role == 'warga') $dashLink = BASE_URL . 'views/warga/dashboard.php';
if ($role == 'petugas') $dashLink = BASE_URL . 'views/petugas/dashboard.php';
if ($role == 'admin') $dashLink = BASE_URL . 'views/admin/dashboard.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top" style="min-height: 70px;">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= ($is_login) ? $dashLink : BASE_URL ?>">
            <i class="bi bi-building-fill-check fs-4"></i>
            <span>eCivitas V2</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <?php if (!$is_login): ?>
                    <li class="nav-item"><a class="nav-link px-3" href="<?= BASE_URL ?>index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="<?= BASE_URL ?>panduan.php">Panduan</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="<?= BASE_URL ?>tentang.php">Tentang</a></li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link px-3 <?= (strpos($current_page, 'dashboard') !== false) ? 'active fw-bold' : '' ?>" href="<?= $dashLink ?>">
                            <i class="bi bi-grid-fill me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 <?= ($current_page == 'panduan.php') ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>panduan.php">
                            <i class="bi bi-life-preserver me-1"></i> Bantuan
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if ($is_login): ?>
                    
                    <div class="text-white text-end lh-1 d-none d-lg-block">
                        <div class="fw-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($nama) ?></div>
                        <small class="text-white-50 text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;"><?= $role ?></small>
                    </div>

                    <div class="vr bg-white opacity-25 mx-2 d-none d-lg-block"></div>

                    <?php if (strpos($current_page, 'dashboard') === false): ?>
                        <a href="<?= $dashLink ?>" class="btn btn-light text-primary btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Ke Dashboard">
                            <i class="bi bi-speedometer2"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($role === 'warga'): ?>
                        <a href="<?= BASE_URL ?>views/warga/profile.php" class="btn btn-light text-primary btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Profil Saya">
                            <i class="bi bi-person-fill"></i>
                        </a>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>actions/auth/logout.php" class="btn btn-danger btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" onclick="return confirm('Yakin ingin keluar?')" title="Keluar">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>

                <?php else: ?>
                    <a href="<?= BASE_URL ?>views/auth/login.php" class="btn btn-light text-primary fw-bold px-4 rounded-pill shadow-sm">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>