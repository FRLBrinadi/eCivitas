<?php
// File: views/warga/cetak_surat.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login'])) die("Akses ditolak.");
$id = $_GET['id'] ?? null;
if (!$id) die("ID Surat tidak ditemukan.");

try {
    // Ambil Instansi
    $stmtInst = $pdo->query("SELECT * FROM m_instansi WHERE id = 1");
    $instansi = $stmtInst->fetch();
    if (!$instansi) $instansi = ['nama_resmi'=>'RW 05', 'alamat'=>'alamat default', 'nama_ketua'=>'Ketua', 'jabatan_ketua'=>'Ketua RW'];

    // Ambil Data Surat
    $sql = "SELECT t.*, u.nik, u.alamat, m.nama_dokumen, m.kode_surat FROM t_pengajuan t 
            JOIN users u ON t.user_id = u.id JOIN m_jenis_dokumen m ON t.jenis_id = m.id
            WHERE t.id = :id AND t.status = 'Disetujui'";
    
    if ($_SESSION['role'] === 'warga') { $sql .= " AND t.user_id = :uid"; $params = ['id'=>$id, 'uid'=>$_SESSION['user_id']]; } 
    else { $params = ['id'=>$id]; }
    
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    $data = $stmt->fetch();
    if (!$data) die("Surat tidak ditemukan.");

    // Ambil Detail
    $stmtDet = $pdo->prepare("SELECT nama_field, isi_field FROM t_pengajuan_detail WHERE pengajuan_id = :id ORDER BY id ASC");
    $stmtDet->execute(['id' => $id]);
    $details = $stmtDet->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

$romawi = ["", "I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII"];
$no_surat = $data['kode_surat'] . "/" . str_pad($data['id'], 3, '0', STR_PAD_LEFT) . "/RW-05/" . $romawi[date('n')] . "/" . date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak - <?= htmlspecialchars($data['nama_dokumen']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/print.css">
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ CETAK PDF</button>
    </div>

    <div class="page">
        <div class="header">
            <h3>PEMERINTAH KOTA BATAM</h3>
            <h3><?= htmlspecialchars($instansi['kecamatan']) ?></h3>
            <h3><?= htmlspecialchars($instansi['kelurahan']) ?></h3>
            <h2><?= htmlspecialchars($instansi['nama_resmi']) ?></h2>
            <p>Alamat: <?= htmlspecialchars($instansi['alamat']) ?></p>
        </div>

        <div class="title-section">
            <span class="title-text"><?= strtoupper($data['nama_dokumen']) ?></span>
            <span class="nomor-surat">Nomor: <?= $no_surat ?></span>
        </div>

        <div class="content">
            <p>Yang bertanda tangan di bawah ini <?= htmlspecialchars($instansi['jabatan_ketua']) ?> <?= htmlspecialchars(ucwords(strtolower($instansi['kelurahan']))) ?>, Kecamatan <?= htmlspecialchars(ucwords(strtolower($instansi['kecamatan']))) ?>, Kota Batam, dengan ini menerangkan bahwa:</p>
            
            <table class="data-table">
                <tr>
                    <td class="label-col">Nama Lengkap</td><td class="sep-col">:</td>
                    <td class="val-col"><?= htmlspecialchars(strtoupper($details['Nama Lengkap'] ?? $details['Nama_Lengkap'] ?? $data['nama_lengkap'])) ?></td>
                </tr>
                <tr><td>NIK</td><td>:</td><td><?= htmlspecialchars($details['NIK'] ?? $data['nik'] ?? '-') ?></td></tr>
                <tr><td>Alamat</td><td>:</td><td><?= htmlspecialchars($details['Alamat'] ?? $data['alamat'] ?? '-') ?></td></tr>
                
                <?php foreach ($details as $key => $val): ?>
                    <?php if (!in_array($key, ['Nama Lengkap', 'Nama_Lengkap', 'NIK', 'Alamat'])): ?>
                    <tr><td><?= htmlspecialchars($key) ?></td><td>:</td><td><?= nl2br(htmlspecialchars($val)) ?></td></tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>

            <p>Orang tersebut di atas adalah benar-benar warga kami yang berdomisili di lingkungan <?= htmlspecialchars($instansi['nama_resmi']) ?>. Surat keterangan ini diberikan untuk dapat dipergunakan sebagaimana mestinya.</p>
            <p>Demikian surat keterangan ini dibuat dengan sebenarnya agar dapat dipergunakan seperlunya.</p>
        </div>

        <div class="signature">
            <p>Batam, <?= date('d F Y') ?></p>
            <p class="jabatan"><?= htmlspecialchars($instansi['jabatan_ketua']) ?></p>
            <p class="nama"><?= htmlspecialchars($instansi['nama_ketua']) ?></p>
        </div>
    </div>
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>