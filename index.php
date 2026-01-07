<?php
// File: index.php (Root Folder)
require_once 'config/app.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eCivitas | Layanan Administrasi Warga</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="bi bi-building-fill-check me-2"></i>eCivitas
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link active" href="#home">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="panduan.php">Panduan</a></li>
                    <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang Kami</a></li>
                    <li class="nav-item">
                        <?php if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true): ?>
                            <?php 
                                $dash_url = ($_SESSION['role'] == 'petugas') ? 'views/petugas/dashboard.php' : 'views/warga/dashboard.php';
                            ?>
                            <a href="<?= BASE_URL . $dash_url ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>views/auth/login.php" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                                Masuk
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-section d-flex align-items-center">
        <div class="container text-center text-white">
            <h1 class="display-4 fw-bold mb-3">Urus Surat Kini Lebih Mudah</h1>
            <p class="lead mb-4 mx-auto" style="max-width: 700px;">
                Sistem pelayanan administrasi digital untuk warga RW 05. 
                Ajukan surat pengantar, domisili, hingga izin usaha langsung dari rumah.
            </p>
            
            <?php if (!isset($_SESSION['is_login'])): ?>
                <a href="<?= BASE_URL ?>views/auth/register.php" class="btn btn-light text-primary btn-lg rounded-pill px-5 fw-bold shadow">
                    Daftar Sekarang
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL . $dash_url ?>" class="btn btn-light text-primary btn-lg rounded-pill px-5 fw-bold shadow">
                    Ajukan Surat
                </a>
            <?php endif; ?>
        </div>
    </section>

    <section id="fitur" class="py-5">
        <div class="container">
            <div class="text-center mb-5 mt-4">
                <h6 class="text-primary fw-bold text-uppercase">Layanan Kami</h6>
                <h2 class="fw-bold">Fitur Unggulan eCivitas</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 p-3 hover-card">
                        <div class="card-body text-center">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                                <i class="bi bi-laptop fs-3"></i>
                            </div>
                            <h5 class="fw-bold">Akses Online</h5>
                            <p class="text-muted small">Tidak perlu antre di kantor RW/Kelurahan. Akses 24 jam dari mana saja.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 p-3 hover-card">
                        <div class="card-body text-center">
                            <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3">
                                <i class="bi bi-file-earmark-arrow-up fs-3"></i>
                            </div>
                            <h5 class="fw-bold">Upload Mudah</h5>
                            <p class="text-muted small">Cukup foto atau scan dokumen pendukung (KTP/KK) dan upload.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 p-3 hover-card">
                        <div class="card-body text-center">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                                <i class="bi bi-bell fs-3"></i>
                            </div>
                            <h5 class="fw-bold">Notifikasi Realtime</h5>
                            <p class="text-muted small">Pantau status pengajuan Anda (Disetujui/Revisi) langsung dari dashboard.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 p-3 hover-card">
                        <div class="card-body text-center">
                            <div class="icon-box bg-danger bg-opacity-10 text-danger mx-auto mb-3">
                                <i class="bi bi-printer fs-3"></i>
                            </div>
                            <h5 class="fw-bold">Cetak Mandiri</h5>
                            <p class="text-muted small">Surat yang disetujui dapat langsung diunduh dan dicetak sendiri.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="mb-0 small">&copy; 2025 eCivitas Project - Kelompok 4 PBL</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>