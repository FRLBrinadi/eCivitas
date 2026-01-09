# eCivitas - Sistem Pelayanan Warga

eCivitas adalah aplikasi berbasis web untuk mempermudah warga dalam mengajukan dokumen atau surat-surat ke instansi terkait secara digital.

## Fitur Utama
* Register & Login (Warga, Petugas, Admin)
* Pengajuan Dokumen Online
* Verifikasi Dokumen oleh Petugas
* Cetak Laporan & Surat (PDF)

## Persyaratan Sistem
* PHP >= 7.4
* MySQL / MariaDB
* Laragon atau XAMPP

## Cara Instalasi
1. Clone repository ini atau download file ZIP.
2. Letakkan folder di dalam `C:/laragon/www` atau `htdocs`.
3. Import database:
   * Buka phpMyAdmin.
   * Buat database baru bernama `ecivitas`.
   * Import file `ecivitas.sql` yang ada di folder root.
4. Sesuaikan konfigurasi database di file `config/database.php`.

## Akun Login (Demo)
* admin: password: 123456
* petugas01: password: 123456
* Warga01: password: 123456

