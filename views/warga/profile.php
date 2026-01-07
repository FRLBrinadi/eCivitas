<?php
// File: views/warga/profile.php
require_once '../../config/app.php';

if (!isset($_SESSION['is_login'])) {
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

// PROSES UPDATE PROFIL
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Ambil data dan trim spasi
        $nama_lengkap  = trim($_POST['nama_lengkap'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $nik           = trim($_POST['nik'] ?? '');
        $alamat        = trim($_POST['alamat'] ?? '');
        $no_hp         = trim($_POST['no_hp'] ?? '');
        $pekerjaan     = trim($_POST['pekerjaan'] ?? '');
        $tempat_lahir  = trim($_POST['tempat_lahir'] ?? '');
        $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';

        // 2. VALIDASI: Cek apakah ada field yang kosong
        if (empty($nama_lengkap) || empty($email) || empty($nik) || empty($alamat) || 
            empty($no_hp) || empty($pekerjaan) || empty($tempat_lahir) || empty($tanggal_lahir)) {
            
            $msg = "<div class='alert alert-danger'>Semua kolom wajib diisi, tidak boleh ada yang kosong!</div>";
        
        } 
        // 3. VALIDASI: Cek panjang NIK harus 16
        elseif (strlen($nik) !== 16 || !ctype_digit($nik)) {
            $msg = "<div class='alert alert-danger'>NIK harus berjumlah 16 digit angka!</div>";
        }
        else {
            // 4. PROSES UPDATE (Jika validasi lolos)
            $sql = "UPDATE users SET 
                    nama_lengkap = :nama_lengkap, 
                    email = :email, 
                    nik = :nik, 
                    alamat = :alamat, 
                    no_hp = :hp, 
                    pekerjaan = :job,
                    tempat_lahir = :tmp,
                    tanggal_lahir = :tgl
                    WHERE id = :uid";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nama_lengkap' => $nama_lengkap, 'email' => $email,
                'nik' => $nik, 'alamat' => $alamat, 'hp' => $no_hp, 
                'job' => $pekerjaan, 'tmp' => $tempat_lahir, 'tgl' => $tanggal_lahir,
                'uid' => $user_id
            ]);

            $msg = "<div class='alert alert-success'>Data profil berhasil diperbarui!</div>";
        }
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Gagal update: " . $e->getMessage() . "</div>";
    }
}

// AMBIL DATA USER TERBARU
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Profil Warga</h3>
                <a href="../auth/ganti_password.php" class="btn btn-warning rounded-pill d-inline-flex align-items-center gap-2">
                    <i class="bi bi-key-fill"></i> Ganti Password
                </a>
                <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill">Kembali ke Dashboard</a>
            </div>

            <?= $msg ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="row g-3">
                            
                            <div class="col-12 bg-primary bg-opacity-10 p-3 rounded mb-2">
                                <h6 class="text-primary fw-bold mb-0">Informasi Akun</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="<?= e($user['nama_lengkap'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="text" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
                            </div>

                            <div class="col-12 bg-success bg-opacity-10 p-3 rounded mt-4 mb-2">
                                <h6 class="text-success fw-bold mb-0">Data Kependudukan (Wajib Diisi)</h6>
                                <small class="text-muted">Data ini akan otomatis muncul di surat pengajuan.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" name="nik" class="form-control" value="<?= e($user['nik'] ?? '') ?>" maxlength="16" minlength="16" placeholder="16 Digit NIK"required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. Handphone / WA</label>
                                <input type="text" name="no_hp" class="form-control" value="<?= e($user['no_hp'] ?? '') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="<?= e($user['tempat_lahir'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?= e($user['tanggal_lahir'] ?? '') ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control" value="<?= e($user['pekerjaan'] ?? '') ?>" placeholder="Wiraswasta / Karyawan / Pelajar" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Alamat Lengkap (Sesuai KTP)</label>
                                <textarea name="alamat" class="form-control" rows="3" required><?= e($user['alamat'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold">Simpan Profil</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>