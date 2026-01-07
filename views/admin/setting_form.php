<?php
// File: views/admin/setting_form.php
require_once __DIR__ . '/../../config/app.php';

// Validasi Akses & ID
if (!isset($_GET['id'])) {
    echo "<script>alert('ID Dokumen tidak ditemukan!'); window.location.href='views/admin/dashboard.php';</script>";
    exit;
}

    $id_dokumen = $_GET['id'];
    // $pdo = Database::connect(); // Gunakan Singleton Pattern dari fase 1

// 1. Ambil Data Header Dokumen
$stmt = $pdo->prepare("SELECT * FROM m_jenis_dokumen WHERE id = ?");
$stmt->execute([$id_dokumen]);
$dokumen = $stmt->fetch();

if (!$dokumen) die("Dokumen tidak ditemukan.");

// 2. Ambil Komponen Form (Urutkan ASC)
$stmt_comp = $pdo->prepare("SELECT * FROM m_form_template WHERE jenis_id = ? ORDER BY urutan ASC");
$stmt_comp->execute([$id_dokumen]);
$components = $stmt_comp->fetchAll();

// 3. Ambil Syarat Lampiran
$stmt_lamp = $pdo->prepare("SELECT * FROM m_syarat_lampiran WHERE jenis_id = ?");
$stmt_lamp->execute([$id_dokumen]);
$lampiran = $stmt_lamp->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Form - <?= e($dokumen['nama_dokumen']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    
    <style>
        .drag-handle { cursor: grab; color: #aaa; padding: 10px; }
        .drag-handle:active { cursor: grabbing; color: #0d6efd; }
        .component-row { border-left: 4px solid #0d6efd; background: #fff; transition: all 0.2s; }
        .lampiran-row { border-left: 4px solid #198754; background: #fff; }
        .component-row:hover { background-color: #f8f9fa; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <?= flash() ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0"><i class="fas fa-tools text-primary"></i> Konfigurasi Form</h3>
            <small class="text-muted">Edit surat: <strong><?= e($dokumen['nama_dokumen']) ?></strong></small>
        </div>
        <a href="<?= BASE_URL ?>views/admin/dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <form action="<?= BASE_URL ?>actions/admin/simpan_setting.php" method="POST" id="formBuilder">
        <?= csrf_field() ?>
        <input type="hidden" name="jenis_id" value="<?= $dokumen['id'] ?>">
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">1. Informasi Surat</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Kode Surat</label>
                        <input type="text" name="kode_surat" class="form-control" value="<?= e($dokumen['kode_surat']) ?>" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Nama Dokumen</label>
                        <input type="text" name="nama_dokumen" class="form-control" value="<?= e($dokumen['nama_dokumen']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi / Instruksi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"><?= e($dokumen['deskripsi']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary">2. Komponen Input (Field)</span>
                <button type="button" class="btn btn-sm btn-primary" onclick="addComponent()">
                    <i class="fas fa-plus-circle"></i> Tambah Input
                </button>
            </div>
            <div class="card-body bg-light p-3">
                <div id="container-components">
                    <?php 
                    $idx = 0; 
                    foreach ($components as $row): 
                    ?>
                    <div class="card mb-2 component-row shadow-sm">
                        <div class="card-body p-2">
                            <div class="row align-items-center g-2">
                                <div class="col-auto drag-handle"><i class="fas fa-grip-vertical"></i></div>
                                
                                <input type="hidden" name="components[<?= $idx ?>][id]" value="<?= $row['id'] ?>">
                                <input type="hidden" name="components[<?= $idx ?>][urutan]" class="input-urutan" value="<?= $row['urutan'] ?>">

                                <div class="col-md-3">
                                    <input type="text" name="components[<?= $idx ?>][label_field]" class="form-control form-control-sm" value="<?= e($row['label_field']) ?>" placeholder="Label (cth: Nama Ibu)" required>
                                </div>
                                
                                <div class="col-md-3">
                                    <select name="components[<?= $idx ?>][data_source]" class="form-select form-select-sm select-source" onchange="toggleType(this)">
                                        <option value="manual" <?= $row['data_source'] == 'manual' ? 'selected' : '' ?>>Isian Manual</option>
                                        <option value="profile_nama" <?= $row['data_source'] == 'profile_nama' ? 'selected' : '' ?>>Auto: Nama User</option>
                                        <option value="profile_nik" <?= $row['data_source'] == 'profile_nik' ? 'selected' : '' ?>>Auto: NIK</option>
                                        <option value="profile_alamat" <?= $row['data_source'] == 'profile_alamat' ? 'selected' : '' ?>>Auto: Alamat</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="components[<?= $idx ?>][tipe_input]" class="form-select form-select-sm input-type" <?= $row['data_source'] != 'manual' ? 'readonly style="background-color:#e9ecef; pointer-events:none;"' : '' ?>>
                                        <option value="text" <?= $row['tipe_input'] == 'text' ? 'selected' : '' ?>>Text Pendek</option>
                                        <option value="textarea" <?= $row['tipe_input'] == 'textarea' ? 'selected' : '' ?>>Area Panjang</option>
                                        <option value="number" <?= $row['tipe_input'] == 'number' ? 'selected' : '' ?>>Angka</option>
                                        <option value="date" <?= $row['tipe_input'] == 'date' ? 'selected' : '' ?>>Tanggal</option>
                                    </select>
                                </div>

                                <div class="col-auto ms-auto">
                                    <a href="<?= BASE_URL ?>actions/admin/hapus_setting.php?id=<?= $row['id'] ?>&type=field&parent=<?= $id_dokumen ?>" 
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Hapus field ini permanen?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $idx++; endforeach; ?>
                </div>
                <div class="text-center mt-3 text-muted small">
                    <i class="fas fa-info-circle"></i> Geser ikon <i class="fas fa-grip-vertical"></i> untuk mengubah urutan.
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-success">3. Syarat Upload File</span>
                <button type="button" class="btn btn-sm btn-success" onclick="addLampiran()">
                    <i class="fas fa-plus-circle"></i> Tambah File
                </button>
            </div>
            <div class="card-body bg-light p-3">
                <div id="container-lampiran">
                    <?php 
                    $idxL = 0;
                    foreach ($lampiran as $rowL): 
                    ?>
                    <div class="card mb-2 lampiran-row shadow-sm">
                        <div class="card-body p-2">
                            <div class="row align-items-center g-2">
                                <input type="hidden" name="lampiran[<?= $idxL ?>][id]" value="<?= $rowL['id'] ?>">
                                
                                <div class="col-md-10">
                                    <input type="text" name="lampiran[<?= $idxL ?>][nama_lampiran]" class="form-control form-control-sm" value="<?= e($rowL['nama_lampiran']) ?>" required>
                                </div>
                                <div class="col-auto ms-auto">
                                    <a href="<?= BASE_URL ?>actions/admin/hapus_setting.php?id=<?= $rowL['id'] ?>&type=file&parent=<?= $id_dokumen ?>" 
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Hapus syarat lampiran ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $idxL++; endforeach; ?>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 pb-5">
            <button type="submit" class="btn btn-primary btn-lg shadow">
                <i class="fas fa-save me-2"></i> SIMPAN SEMUA PERUBAHAN
            </button>
        </div>
    </form>
</div>

<template id="tpl-component">
    <div class="card mb-2 component-row shadow-sm border-warning">
        <div class="card-body p-2">
            <div class="row align-items-center g-2">
                <div class="col-auto drag-handle"><i class="fas fa-grip-vertical"></i></div>
                <input type="hidden" name="components[INDEX][id]" value="">
                <input type="hidden" name="components[INDEX][urutan]" class="input-urutan" value="">
                
                <div class="col-md-3">
                    <input type="text" name="components[INDEX][label_field]" class="form-control form-control-sm" placeholder="Label Baru" required>
                </div>
                <div class="col-md-3">
                    <select name="components[INDEX][data_source]" class="form-select form-select-sm select-source" onchange="toggleType(this)">
                        <option value="manual">Isian Manual</option>
                        <option value="profile_nama">Auto: Nama User</option>
                        <option value="profile_nik">Auto: NIK</option>
                        <option value="profile_alamat">Auto: Alamat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="components[INDEX][tipe_input]" class="form-select form-select-sm input-type">
                        <option value="text">Text Pendek</option>
                        <option value="textarea">Area Panjang</option>
                        <option value="number">Angka</option>
                        <option value="date">Tanggal</option>
                    </select>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.card').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="tpl-lampiran">
    <div class="card mb-2 lampiran-row shadow-sm border-warning">
        <div class="card-body p-2">
            <div class="row align-items-center g-2">
                <input type="hidden" name="lampiran[INDEX][id]" value="">
                <div class="col-md-10">
                    <input type="text" name="lampiran[INDEX][nama_lampiran]" class="form-control form-control-sm" placeholder="Nama Dokumen Syarat" required>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.card').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    // 1. Inisialisasi Drag & Drop (SortableJS)
    var el = document.getElementById('container-components');
    var sortable = Sortable.create(el, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'bg-primary-subtle',
        onEnd: function() { updateUrutan(); }
    });

    // 2. Fungsi Update Nomor Urut Hidden Input
    function updateUrutan() {
        document.querySelectorAll('.input-urutan').forEach((el, index) => {
            el.value = index + 1; // Urutan dimulai dari 1
        });
    }

    // 3. Tambah Field Baru
    function addComponent() {
        const id = Date.now(); // Hack biar index unik
        const tpl = document.getElementById('tpl-component').innerHTML.replace(/INDEX/g, id);
        document.getElementById('container-components').insertAdjacentHTML('beforeend', tpl);
        updateUrutan();
        // Scroll ke elemen baru
        document.getElementById('container-components').lastElementChild.scrollIntoView({ behavior: 'smooth' });
    }

    // 4. Tambah Lampiran Baru
    function addLampiran() {
        const id = Date.now();
        const tpl = document.getElementById('tpl-lampiran').innerHTML.replace(/INDEX/g, id);
        document.getElementById('container-lampiran').insertAdjacentHTML('beforeend', tpl);
    }

    // 5. Logika Toggle (Jika Auto, input jadi Readonly)
    function toggleType(select) {
        const row = select.closest('.row');
        const input = row.querySelector('.input-type');
        if(select.value !== 'manual') {
            input.value = 'text'; // Default text kalau auto
            input.style.backgroundColor = '#e9ecef';
            input.style.pointerEvents = 'none'; // Disable klik
            // Opsional: input.setAttribute('readonly', true);
        } else {
            input.style.backgroundColor = '#fff';
            input.style.pointerEvents = 'auto';
        }
    }
</script>

</body>
</html>