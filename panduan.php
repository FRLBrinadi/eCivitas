<?php
// File: eCivitas_Refactored_V2/panduan.php
require_once 'config/app.php';

$pageTitle = "Panduan Penggunaan | eCivitas";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Tentukan Role Aktif
// Jika belum login (Guest), kita anggap dia butuh panduan Warga (untuk daftar)
$role = $_SESSION['role'] ?? 'guest';
?>

<style>
    .step-icon { 
        width: 50px; height: 50px; 
        border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 800; font-size: 1.25rem; flex-shrink: 0;
        border: 2px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-right: 20px;
    }
    /* Warna Warga (Biru) */
    .theme-warga .step-icon { background: #e0f2fe; color: #0369a1; }
    .theme-warga .card-header { background: #0d6efd; color: white; }
    
    /* Warna Petugas (Hitam/Gelap) */
    .theme-petugas .step-icon { background: #f1f5f9; color: #1e293b; }
    .theme-petugas .card-header { background: #1e293b; color: white; }

    /* Warna Admin (Merah) */
    .theme-admin .step-icon { background: #fee2e2; color: #b91c1c; }
    .theme-admin .card-header { background: #dc3545; color: white; }

    .guide-card { border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .list-group-item { padding: 1.5rem; border: none; border-bottom: 1px solid #f1f5f9; }
    .list-group-item:last-child { border-bottom: none; }
</style>

<div class="container py-5">
    
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Pusat Bantuan</h2>
        <p class="text-muted">Panduan penggunaan sistem sesuai hak akses Anda.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <?php if ($role === 'warga' || $role === 'guest'): ?>
            <div class="card guide-card theme-warga">
                <div class="card-header py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2"></i>Panduan Warga</h5>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex">
                        <div class="step-icon">1</div>
                        <div>
                            <h5 class="fw-bold text-primary mb-2">Registrasi & Profil</h5>
                            <p class="mb-0 text-muted">Daftar akun baru, lalu login. Sistem akan meminta Anda melengkapi <strong>Data Profil (NIK & Alamat)</strong>. Data ini wajib diisi satu kali saja agar formulir surat selanjutnya terisi otomatis.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">2</div>
                        <div>
                            <h5 class="fw-bold text-primary mb-2">Mengajukan Surat</h5>
                            <p class="mb-0 text-muted">Klik tombol "Buat Baru" di Dashboard. Formulir akan menyesuaikan diri (Isian & Lampiran) sesuai jenis surat yang dipilih. Anda bisa mengupload banyak file sekaligus.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">3</div>
                        <div>
                            <h5 class="fw-bold text-primary mb-2">Draft & Konfirmasi</h5>
                            <p class="mb-0 text-muted">Data yang disimpan akan masuk status <strong>Draft</strong>. Cek kembali data Anda, lalu klik tombol biru <strong>"Ajukan"</strong> agar data masuk ke petugas.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">4</div>
                        <div>
                            <h5 class="fw-bold text-primary mb-2">Revisi (Perbaikan)</h5>
                            <p class="mb-0 text-muted">Jika status berubah menjadi <strong>Revisi</strong> (Biru Muda), klik tombol "Perbaiki". Baca catatan petugas, perbaiki isian atau ganti file yang salah, lalu kirim ulang.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">5</div>
                        <div>
                            <h5 class="fw-bold text-primary mb-2">Cetak Surat</h5>
                            <p class="mb-0 text-muted">Jika status <strong>Disetujui</strong> (Hijau), tombol "Cetak" akan muncul. Klik untuk mengunduh surat resmi dalam format PDF yang siap diprint.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif ($role === 'petugas'): ?>
            <div class="card guide-card theme-petugas">
                <div class="card-header py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge-fill me-2"></i>Panduan Petugas RW</h5>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex">
                        <div class="step-icon">1</div>
                        <div>
                            <h5 class="fw-bold text-dark mb-2">Dashboard & Filter</h5>
                            <p class="mb-0 text-muted">Gunakan fitur <strong>Pencarian</strong> untuk mencari nama warga atau nomor tiket. Gunakan <strong>Filter Tanggal & Status</strong> untuk menyaring data spesifik.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">2</div>
                        <div>
                            <h5 class="fw-bold text-dark mb-2">Verifikasi Dokumen</h5>
                            <p class="mb-0 text-muted">Klik tombol "Periksa". Cek kelengkapan isian warga dan lihat semua lampiran file. Anda bisa membuka file PDF/Gambar langsung di browser.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">3</div>
                        <div>
                            <h5 class="fw-bold text-dark mb-2">Keputusan & Audit</h5>
                            <p class="mb-0 text-muted">Ubah status menjadi Setuju, Tolak, atau Revisi. Setiap perubahan status akan tercatat di riwayat (Audit Trail) agar transparan siapa yang mengubahnya.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">4</div>
                        <div>
                            <h5 class="fw-bold text-dark mb-2">Laporan Bulanan</h5>
                            <p class="mb-0 text-muted">Masuk menu Laporan di sidebar. Pilih periode bulan dan tahun, lalu cetak PDF atau Export ke Excel untuk arsip kelurahan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif ($role === 'admin'): ?>
            <div class="card guide-card theme-admin">
                <div class="card-header py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Panduan Administrator</h5>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex">
                        <div class="step-icon">1</div>
                        <div>
                            <h5 class="fw-bold text-danger mb-2">Manajemen User</h5>
                            <p class="mb-0 text-muted">Tambah akun petugas baru (Password default = Username). Anda juga bisa mereset password warga atau memblokir akun yang menyalahi aturan.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">2</div>
                        <div>
                            <h5 class="fw-bold text-danger mb-2">Kelola Layanan (Master Data)</h5>
                            <p class="mb-0 text-muted">Tambah jenis surat baru tanpa coding. Cukup masukkan Kode dan Nama Layanan. Tentukan apakah layanan aktif atau non-aktif.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">3</div>
                        <div>
                            <h5 class="fw-bold text-danger mb-2">Setting Form Dinamis</h5>
                            <p class="mb-0 text-muted">Klik tombol "Atur Form" pada layanan. Tentukan field apa saja yang harus diisi warga (bisa ambil otomatis dari profil) dan dokumen apa yang wajib diupload.</p>
                        </div>
                    </div>
                    <div class="list-group-item d-flex">
                        <div class="step-icon">4</div>
                        <div>
                            <h5 class="fw-bold text-danger mb-2">Pengaturan Instansi</h5>
                            <p class="mb-0 text-muted">Masuk ke menu "Setting Instansi" untuk mengubah Kop Surat (Nama RW, Alamat) dan Nama Pejabat Penandatangan.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>