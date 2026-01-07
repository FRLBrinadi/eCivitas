<?php
// File: tentang.php
require_once 'config/app.php';

// Menetapkan judul halaman dinamis (jika header.php mendukung variabel ini)
$pageTitle = "Tentang Kami | eCivitas";

require_once 'includes/header.php';
require_once 'includes/navbar.php';

// DATA TIM PENGEMBANG
// Cukup edit array ini untuk menambah/mengubah anggota tim
$teamMembers = [
    [
        'name'    => 'M. Alfa Reza',
        'nim'     => '4342511072',
        // 'role'    => 'Front-end & Documentation',
        'role'    => '',
        'initial' => 'M',
        'color'   => 'bg-primary'
    ],
    [
        'name'    => 'Christian Nukie L.H',
        'nim'     => '4342511074',
        // 'role'    => 'System Analyst & Diagram',
        'role'    => '',
        'initial' => 'C',
        'color'   => 'bg-success'
    ],
    [
        'name'    => 'Gabriel Judhika G.',
        'nim'     => '4342511063',
        // 'role'    => 'Database Designer',
        'role'    => '',
        'initial' => 'G',
        'color'   => 'bg-info'
    ],
    [
        'name'    => 'Vito Wahyu Widodo',
        'nim'     => '4342511065',
        // 'role'    => 'UI/UX Designer',
        'role'    => '',
        'initial' => 'V',
        'color'   => 'bg-warning'
    ],
    [
        'name'    => 'Farrel Ranggaza B.',
        'nim'     => '4342511087',
        'role'    => 'Full Stack Developer',
        'initial' => 'F',
        'color'   => 'bg-danger'
    ]
];
?>

<div class="container py-5" style="min-height: 80vh;">
    
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Tim Pengembang</h2>
        <p class="text-muted">Project Based Learning (PBL) - Teknologi Rekayasa Perangkat Lunak</p>
        <span class="badge bg-dark px-3 py-2 rounded-pill">Politeknik Negeri Batam</span>
    </div>

    <div class="row justify-content-center g-4">
        <?php foreach ($teamMembers as $member): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3 card-hover hover-effect">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="avatar-circle <?= $member['color'] ?> text-white mx-auto d-flex align-items-center justify-content-center rounded-circle shadow-sm" 
                             style="width: 80px; height: 80px; font-size: 2rem; font-weight:bold;">
                            <?= $member['initial'] ?>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark"><?= $member['name'] ?></h5>
                    <p class="text-muted small mb-2"><?= $member['nim'] ?></p>
                    <hr class="mx-auto" style="width: 50px; opacity: 0.2;">
                    <p class="small text-secondary fw-semibold"><?= $member['role'] ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<footer class="bg-dark text-white py-4 text-center mt-5">
    <div class="container">
        <small>&copy; <?= date('Y') ?> eCivitas Project - Kelompok 4</small>
    </div>
</footer>

<?php require_once 'includes/footer.php'; ?>

<style>
    .hover-effect {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>