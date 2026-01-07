<?php
// File: eCivitas_Refactored_V2/views/petugas/cetak_laporan.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    die("Akses Ditolak.");
}

$bulan    = $_GET['bulan'] ?? date('m');
$tahun    = $_GET['tahun'] ?? date('Y');
$jenis_id = $_GET['jenis_id'] ?? ''; 
$status   = $_GET['status'] ?? ''; 
$format   = $_GET['format'] ?? 'print';

$bulanIndo = [
    '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
    '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
    '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
];

try {
    // 1. AMBIL DATA INSTANSI
    $stmtInst = $pdo->query("SELECT * FROM m_instansi WHERE id = 1");
    $instansi = $stmtInst->fetch();
    
    // Fallback jika kosong
    if (!$instansi) {
        $instansi = ['nama_resmi' => 'RUKUN WARGA 05', 'kelurahan' => 'KELURAHAN CONTOH', 'kecamatan' => 'KECAMATAN CONTOH', 'alamat' => 'Alamat belum disetting', 'nama_ketua' => 'ADMIN', 'jabatan_ketua' => 'Ketua RW'];
    }

    // 2. QUERY DATA LAPORAN
    $sql = "SELECT t.*, u.nama_lengkap, u.nik, m.nama_dokumen, m.kode_surat 
            FROM t_pengajuan t
            JOIN users u ON t.user_id = u.id
            JOIN m_jenis_dokumen m ON t.jenis_id = m.id
            WHERE MONTH(t.created_at) = :bln 
            AND YEAR(t.created_at) = :thn 
            AND t.deleted_at IS NULL 
            AND t.status != 'Draft'";
    
    $params = ['bln' => $bulan, 'thn' => $tahun];

    if (!empty($jenis_id)) {
        $sql .= " AND t.jenis_id = :jid";
        $params['jid'] = $jenis_id;
    }
    if (!empty($status)) {
        $sql .= " AND t.status = :stat";
        $params['stat'] = $status;
    }

    $sql .= " ORDER BY t.created_at ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();

} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

// Export Excel Header
if ($format === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_$tahun-$bulan.xls");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan</title>
    <?php if($format !== 'excel'): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/print.css">
    <?php endif; ?>
</head>
<body onload="<?= ($format==='print') ? 'window.print()' : '' ?>">

    <?php if ($format === 'print'): ?>
        <div class="no-print">
            <button onclick="window.print()" class="btn-print">🖨️ CETAK SEKARANG</button>
        </div>
    <?php endif; ?>

    <div class="<?= ($format !== 'excel') ? 'page' : '' ?>">

        <div class="header">
            <h3>PEMERINTAH KOTA BATAM</h3>
            <h3><?= e($instansi['kecamatan']) ?></h3>
            <h3><?= e($instansi['kelurahan']) ?></h3>
            <h2><?= e($instansi['nama_resmi']) ?></h2>
            <p><?= e($instansi['alamat']) ?></p>
        </div>

        <center><h3 style="text-decoration:underline;">LAPORAN PELAYANAN ADMINISTRASI</h3></center>
        <center><p>Periode: <?= strtoupper($bulanIndo[$bulan]) ?> <?= $tahun ?></p></center>
        <br>

        <table class="<?= ($format==='excel') ? '' : 'report-table' ?>" border="1" width="100%">
            <thead>
                <tr>
                    <th width="5%" class="text-center">NO</th>
                    <th width="15%">TIKET</th>
                    <th width="12%" class="text-center">TANGGAL</th>
                    <th width="25%">NAMA PEMOHON</th>
                    <th width="25%">JENIS DOKUMEN</th>
                    <th width="10%" class="text-center">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding:20px; font-style:italic;">
                            Tidak ada data pengajuan pada periode ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no=1; foreach($data as $row): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= e($row['no_pengajuan']) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <?= e($row['nama_lengkap']) ?><br>
                            <small style="color:#555;">NIK: <?= e($row['nik'] ?? '-') ?></small>
                        </td>
                        <td><?= e($row['nama_dokumen']) ?></td>
                        <td class="text-center" style="font-weight:bold;">
                            <?= strtoupper($row['status']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="signature">
            <p>Batam, <?= date('d F Y') ?></p>
            <p class="jabatan"><?= e($instansi['jabatan_ketua']) ?></p>
            <br><br><br>
            <p class="nama"><?= e($instansi['nama_ketua']) ?></p>
        </div>

    </div> </body>
</html>