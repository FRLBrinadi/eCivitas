<?php
// File: views/admin/dashboard.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak.");
}

// 1. DATA KARTU STATISTIK
$total_warga = $pdo->query("SELECT COUNT(*) FROM users WHERE role='warga'")->fetchColumn();
$total_petugas = $pdo->query("SELECT COUNT(*) FROM users WHERE role='petugas'")->fetchColumn();
$total_layanan = $pdo->query("SELECT COUNT(*) FROM m_jenis_dokumen WHERE is_active=1")->fetchColumn();
$total_surat = $pdo->query("SELECT COUNT(*) FROM t_pengajuan WHERE deleted_at IS NULL")->fetchColumn();

// 2. DATA UNTUK GRAFIK (Group By Status)
// Kita ambil jumlah pengajuan per status
$stmtChart = $pdo->query("SELECT status, COUNT(*) as jumlah FROM t_pengajuan WHERE deleted_at IS NULL GROUP BY status");
$dataStatus = $stmtChart->fetchAll(PDO::FETCH_KEY_PAIR); // Hasil: ['Pending'=>5, 'Disetujui'=>10]

// Pastikan semua status ada nilainya (meski 0) agar grafik tidak error
$labels = ['Draft', 'Pending', 'Proses', 'Disetujui', 'Revisi', 'Ditolak'];
$chartValues = [];
foreach ($labels as $l) {
    $chartValues[] = $dataStatus[$l] ?? 0;
}

// Setup Layout
$pageTitle = "Admin Dashboard | eCivitas";
// Kita hanya sisakan link DataTables karena itu plugin eksternal
$extraCss = '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">';

require_once '../../includes/header.php';
?>

<div class="d-flex">
    
    <?php require_once '../../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4" style="height: 100vh; overflow-y: auto;">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h3 class="fw-bold text-dark">Admin Dashboard</h3>
            <div class="text-end">
                <small class="text-muted d-block">Login sebagai</small>
                <strong><?= e($_SESSION['nama']) ?></strong>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-white-50">Total Warga</h6>
                        <h2 class="fw-bold mb-0"><?= $total_warga ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-white-50">Layanan Aktif</h6>
                        <h2 class="fw-bold mb-0"><?= $total_layanan ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-dark-50">Total Petugas</h6>
                        <h2 class="fw-bold mb-0"><?= $total_petugas ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-white-50">Total Pengajuan</h6>
                        <h2 class="fw-bold mb-0"><?= $total_surat ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-bar-chart-fill me-2"></i>Statistik Pengajuan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="barChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-success"><i class="bi bi-pie-chart-fill me-2"></i>Komposisi User</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="pieChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php 
// Kita suntikkan data PHP ke JavaScript
$js_labels = json_encode($labels);
$js_values = json_encode($chartValues);
$js_warga = $total_warga;
$js_petugas = $total_petugas;

$extraJs = "
<script>
    // 1. Grafik Batang (Status)
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: $js_labels,
            datasets: [{
                label: 'Jumlah Dokumen',
                data: $js_values,
                backgroundColor: [
                    '#6c757d', // Draft
                    '#ffc107', // Pending
                    '#0d6efd', // Proses
                    '#198754', // Disetujui
                    '#0dcaf0', // Revisi
                    '#dc3545'  // Ditolak
                ],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 2. Grafik Donat (User)
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Warga', 'Petugas'],
            datasets: [{
                data: [$js_warga, $js_petugas],
                backgroundColor: ['#0d6efd', '#ffc107'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
";
require_once '../../includes/footer.php'; 
?>