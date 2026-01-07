<?php
// File: eCivitas_Refactored_V2/views/warga/form_pengajuan.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'warga') {
    header("Location: " . BASE_URL . "views/auth/login.php"); exit;
}

$user_id = $_SESSION['user_id'];

try {
    // 1. Cek Profil (Tetap perlu untuk validasi, tapi tidak untuk ditampilkan paksa)
    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $userData = $stmtUser->fetch();

    if (empty($userData['nik']) || empty($userData['alamat'])) {
        echo "<script>alert('Lengkapi profil dulu!'); window.location.href = 'profile.php';</script>"; exit;
    }

    // 2. Ambil Jenis Dokumen
    $stmt = $pdo->query("SELECT * FROM m_jenis_dokumen WHERE is_active = 1 ORDER BY nama_dokumen ASC");
    $jenis_dokumen_list = $stmt->fetchAll();

    // 3. Logic Flash Data
    $old_data = $_SESSION['old_data'] ?? [];
    if (isset($_SESSION['old_data'])) unset($_SESSION['old_data']);

    $selected_jenis_id = $old_data['jenis_id'] ?? ($_GET['jenis_id'] ?? null);
    $old_details = $old_data['detail'] ?? []; 

    $form_fields = [];
    $syarat_files = [];

    if ($selected_jenis_id) {
        $stmtFields = $pdo->prepare("SELECT * FROM m_form_template WHERE jenis_id = ? ORDER BY urutan ASC");
        $stmtFields->execute([$selected_jenis_id]);
        $form_fields = $stmtFields->fetchAll();

        $stmtFiles = $pdo->prepare("SELECT * FROM m_syarat_lampiran WHERE jenis_id = ?");
        $stmtFiles->execute([$selected_jenis_id]);
        $syarat_files = $stmtFiles->fetchAll();
    }

} catch (PDOException $x) { die("Error: " . $x->getMessage()); }

$pageTitle = "Formulir Pengajuan";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-primary">Formulir Pengajuan</h5>
                </div>
                <div class="card-body p-4">

                    <?= flash() ?>

                    <form action="<?= BASE_URL ?>actions/warga/simpan_pengajuan.php" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Jenis Dokumen</label>
                            <select name="jenis_id" class="form-select" onchange="window.location.href='?jenis_id='+this.value">
                                <option value="" selected disabled>-- Pilih --</option>
                                <?php foreach($jenis_dokumen_list as $dok): ?>
                                    <option value="<?= $dok['id'] ?>" <?= ($selected_jenis_id == $dok['id']) ? 'selected' : '' ?>>
                                        <?= e($dok['nama_dokumen']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($selected_jenis_id): ?>
                            <hr>
                            
                            <?php if (count($form_fields) > 0): ?>
                                <h6 class="fw-bold text-primary mb-3">Data Isian</h6>
                                <?php foreach ($form_fields as $field): ?>
                                    <?php 
                                        $label = $field['label_field'];
                                        $value = '';
                                        $readonly = '';
                                        $bgClass = '';
                                        $badgeAuto = '';

                                        // 1. Prioritas: Data Lama (Flash Data Error)
                                        // Kita ganti spasi dengan underscore karena PHP kadang otomatis mengubah key POST
                                        // Tapi kita coba akses langsung dulu
                                        if (isset($old_details[$label])) {
                                            $value = $old_details[$label];
                                        } 
                                        // 2. Data Binding Profil (Jika tidak ada data lama)
                                        elseif ($field['data_source'] === 'profile_nama') {
                                            $value = $userData['nama_lengkap']; 
                                            $readonly = 'readonly'; 
                                            $bgClass = 'bg-light';
                                            $badgeAuto = '<span class="badge bg-info ms-1">Auto</span>';
                                        } elseif ($field['data_source'] === 'profile_nik') {
                                            $value = $userData['nik']; 
                                            $readonly = 'readonly'; 
                                            $bgClass = 'bg-light';
                                            $badgeAuto = '<span class="badge bg-info ms-1">Auto</span>';
                                        } elseif ($field['data_source'] === 'profile_alamat') {
                                            $value = $userData['alamat']; 
                                            $readonly = 'readonly'; 
                                            $bgClass = 'bg-light';
                                            $badgeAuto = '<span class="badge bg-info ms-1">Auto</span>';
                                        } elseif ($field['data_source'] === 'profile_hp') {
                                            $value = $userData['no_hp']; 
                                            $readonly = 'readonly'; 
                                            $bgClass = 'bg-light';
                                            $badgeAuto = '<span class="badge bg-info ms-1">Auto</span>';
                                        }
                                    ?>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">
                                            <?= e($label) ?> <?= $badgeAuto ?>
                                        </label>
                                        <?php if ($field['tipe_input'] === 'textarea'): ?>
                                            <textarea name="detail[<?= e($label) ?>]" class="form-control <?= $bgClass ?>" rows="3" 
                                            <?= $readonly ?> required><?= e($value) ?></textarea>
                                        <?php else: ?>
                                            <input type="<?= e($field['tipe_input']) ?>" name="detail[<?= e($label) ?>]" class="form-control 
                                                <?= $bgClass ?>" value="<?= e($value) ?>" <?= $readonly ?> required>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">Template form belum diatur oleh Admin.</div>
                            <?php endif; ?>

                            <?php if (count($syarat_files) > 0): ?>
                                <h6 class="fw-bold text-success mt-4 mb-3">Lampiran</h6>
                                <div class="p-3 bg-light border rounded">
                                    <?php foreach ($syarat_files as $file): ?>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold"><?= e($file['nama_lampiran']) ?> <span class="text-danger">*</span></label>
                                            <input type="file" name="lampiran[<?= e($file['nama_lampiran']) ?>]" class="form-control" required>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary fw-bold py-2">Simpan Draft</button>
                            </div>
                        <?php endif; ?>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>