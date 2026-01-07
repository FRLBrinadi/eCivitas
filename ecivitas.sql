-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 05, 2026 at 03:45 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecivitas`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_form_template`
--

CREATE TABLE `m_form_template` (
  `id` int NOT NULL,
  `jenis_id` int NOT NULL,
  `label_field` varchar(100) NOT NULL,
  `tipe_input` enum('text','number','textarea','date') DEFAULT 'text',
  `data_source` enum('manual','profile_nik','profile_nama','profile_alamat','profile_hp','static') DEFAULT 'manual',
  `is_required` tinyint(1) DEFAULT '1',
  `urutan` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `m_form_template`
--

INSERT INTO `m_form_template` (`id`, `jenis_id`, `label_field`, `tipe_input`, `data_source`, `is_required`, `urutan`) VALUES
(1, 1, 'Nama Lengkap', 'text', 'profile_nama', 1, 1),
(2, 1, 'NIK Pemohon', 'number', 'profile_nik', 1, 2),
(3, 1, 'Alamat Sekarang', 'textarea', 'profile_alamat', 1, 3),
(4, 1, 'Alamat Asal (Sesuai KTP)', 'textarea', 'manual', 1, 4),
(5, 1, 'Lama Tinggal (Tahun)', 'number', 'manual', 1, 5),
(6, 1, 'Keperluan', 'textarea', 'manual', 1, 6),
(7, 2, 'Nama Kepala Keluarga', 'text', 'profile_nama', 1, 1),
(8, 2, 'NIK', 'number', 'profile_nik', 1, 2),
(9, 2, 'Pekerjaan', 'text', 'manual', 1, 3),
(10, 2, 'Penghasilan Rata-rata (Rp)', 'number', 'manual', 1, 4),
(11, 2, 'Jumlah Tanggungan', 'number', 'manual', 1, 5),
(12, 2, 'Alasan Pengajuan', 'textarea', 'manual', 1, 6),
(13, 3, 'Nama Pemilik', 'text', 'profile_nama', 1, 1),
(14, 3, 'Nama Usaha', 'text', 'manual', 1, 2),
(15, 3, 'Bidang Usaha', 'text', 'manual', 1, 3),
(16, 3, 'Alamat Lokasi Usaha', 'textarea', 'manual', 1, 4),
(17, 3, 'Omzet Bulanan (Rp)', 'number', 'manual', 1, 5),
(18, 4, 'Nama Lengkap', 'text', 'profile_nama', 1, 1),
(19, 4, 'NIK', 'number', 'profile_nik', 1, 2),
(20, 4, 'No. HP / WA', 'text', 'profile_hp', 1, 3),
(21, 4, 'Keperluan Pengurusan', 'textarea', 'manual', 1, 4),
(29, 5, 'a', 'text', 'profile_nama', 1, 1),
(30, 7, 'nama', 'text', 'profile_nama', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_instansi`
--

CREATE TABLE `m_instansi` (
  `id` int NOT NULL,
  `nama_resmi` varchar(100) DEFAULT NULL,
  `kelurahan` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `alamat` text,
  `nama_ketua` varchar(100) DEFAULT NULL,
  `jabatan_ketua` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `m_instansi`
--

INSERT INTO `m_instansi` (`id`, `nama_resmi`, `kelurahan`, `kecamatan`, `alamat`, `nama_ketua`, `jabatan_ketua`) VALUES
(1, 'Pelayanan Pengajuan', 'BELIAN', 'BATAM KOTA', 'Jl. Engku Putri No 1', 'FARREL RANGGAZA BRINADI', 'Ketua Instansti');

-- --------------------------------------------------------

--
-- Table structure for table `m_jenis_dokumen`
--

CREATE TABLE `m_jenis_dokumen` (
  `id` int NOT NULL,
  `kode_surat` varchar(10) NOT NULL,
  `nama_dokumen` varchar(100) NOT NULL,
  `deskripsi` text,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `m_jenis_dokumen`
--

INSERT INTO `m_jenis_dokumen` (`id`, `kode_surat`, `nama_dokumen`, `deskripsi`, `is_active`) VALUES
(1, 'SKD', 'Surat Keterangan Domisili', 'Surat keterangan tempat tinggal sementara.', 1),
(2, 'SKTM', 'Surat Keterangan Tidak Mampu', 'Untuk pengurusan beasiswa atau bantuan.', 1),
(3, 'SKU', 'Surat Keterangan Usaha', 'Untuk persyaratan bank/izin usaha.', 1),
(4, 'P-RT', 'Surat Pengantar RT/RW', 'Surat pengantar umum.', 1),
(5, 'IUMK', 'Izin Usaha Mikro Kecil', 'Izin resmi pelaku usaha mikro.', 1),
(7, 'SIM- C', 'Surat Izin Mengemudi', 'kendaraan roda 2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `m_syarat_lampiran`
--

CREATE TABLE `m_syarat_lampiran` (
  `id` int NOT NULL,
  `jenis_id` int NOT NULL,
  `nama_lampiran` varchar(100) NOT NULL,
  `is_required` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `m_syarat_lampiran`
--

INSERT INTO `m_syarat_lampiran` (`id`, `jenis_id`, `nama_lampiran`, `is_required`) VALUES
(1, 1, 'Scan KTP Asli', 1),
(2, 1, 'Scan Kartu Keluarga', 1),
(3, 1, 'Surat Pindah (Jika Ada)', 1),
(4, 2, 'Foto KTP Kepala Keluarga', 1),
(5, 2, 'Foto Kartu Keluarga', 1),
(6, 2, 'Foto Rumah Tampak Depan', 1),
(7, 3, 'Scan KTP Pemilik', 1),
(8, 3, 'Foto Tempat Usaha', 1),
(9, 3, 'Bukti Lunas PBB Terakhir', 1),
(10, 4, 'Foto KTP Asli', 1);

-- --------------------------------------------------------

--
-- Table structure for table `t_histori`
--

CREATE TABLE `t_histori` (
  `id` int NOT NULL,
  `pengajuan_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status_lama` varchar(20) DEFAULT NULL,
  `status_baru` varchar(20) NOT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_histori`
--

INSERT INTO `t_histori` (`id`, `pengajuan_id`, `user_id`, `status_lama`, `status_baru`, `catatan`, `created_at`) VALUES
(1, 1, 3, NULL, 'Draft', 'Draft disimpan', '2025-12-17 13:11:52'),
(2, 2, 3, NULL, 'Draft', 'Draft disimpan', '2025-12-17 13:12:32'),
(3, 2, 3, NULL, 'Pending', 'Diajukan ke Petugas', '2025-12-17 13:12:36'),
(4, 1, 3, NULL, 'Pending', 'Diajukan ke Petugas', '2025-12-17 13:16:06'),
(5, 2, 2, NULL, 'Proses', '', '2025-12-17 13:16:26'),
(6, 3, 3, NULL, 'Draft', 'Draft disimpan', '2025-12-18 10:34:29'),
(7, 3, 3, NULL, 'Pending', 'Diajukan ke Petugas', '2025-12-18 10:34:44'),
(8, 3, 2, NULL, 'Disetujui', 'ok', '2025-12-18 10:35:41'),
(9, 4, 3, NULL, 'Draft', 'Draft disimpan', '2025-12-21 05:25:02'),
(10, 5, 3, NULL, 'Draft', 'Draft disimpan', '2025-12-21 06:08:23'),
(11, 4, 3, NULL, 'Pending', 'Diajukan ke Petugas', '2025-12-21 06:20:31'),
(12, 4, 2, NULL, 'Revisi', 'revisi foto ktp', '2025-12-21 06:23:47'),
(13, 6, 3, NULL, 'Draft', 'Draft disimpan', '2025-12-21 06:56:03'),
(14, 7, 4, NULL, 'Draft', 'Draft disimpan', '2026-01-05 15:25:11'),
(15, 7, 4, NULL, 'Pending', 'Diajukan ke Petugas', '2026-01-05 15:25:16'),
(16, 7, 2, NULL, 'Disetujui', '', '2026-01-05 15:25:31'),
(17, 8, 4, NULL, 'Draft', 'Draft disimpan', '2026-01-05 15:41:48'),
(18, 8, 4, NULL, 'Pending', 'Diajukan ke Petugas', '2026-01-05 15:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `t_lampiran`
--

CREATE TABLE `t_lampiran` (
  `id` int NOT NULL,
  `pengajuan_id` int NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `tipe_lampiran` varchar(50) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_lampiran`
--

INSERT INTO `t_lampiran` (`id`, `pengajuan_id`, `nama_file`, `tipe_lampiran`, `uploaded_at`) VALUES
(1, 1, '1_96d7c9e02d5b5ee08e3a158039a63a64.jpg', 'Scan KTP', '2025-12-17 13:11:52'),
(2, 1, '1_3fe83eb68adf28a60ad0a23ba02fa6c4.jpg', 'Scan KK', '2025-12-17 13:11:52'),
(3, 1, '1_aeb2cec42f58773eebd00c8e836a9cc1.jpg', 'Pas Foto 4x6', '2025-12-17 13:11:52'),
(4, 1, '1_8626e90740b11be442c7fc505f71ac3b.jpg', 'Foto Kegiatan Usaha', '2025-12-17 13:11:52'),
(5, 2, '2_86a896489ab5a4e87ef4ac4c06e4563c.jpg', 'Foto KTP Asli', '2025-12-17 13:12:32'),
(6, 3, '3_23bb71fd721299330c7fe2773cf4228a.jpg', 'Foto KTP Asli', '2025-12-18 10:34:29'),
(7, 4, '4_7b25bd84eb8f14bff714595575192549.jpg', 'Foto KTP Asli', '2025-12-21 05:25:02'),
(8, 5, '5_02399c62e80b2371b8eed50c5caddffb.jpg', 'Foto KTP Asli', '2025-12-21 06:08:23'),
(9, 6, '6_8d71f520c6c72768b438b5d6d47d4332.jpg', 'Foto KTP Asli', '2025-12-21 06:56:03'),
(10, 7, '7_a815bc597680b4703034c47b13718ec2.png', 'Scan KTP Pemilik', '2026-01-05 15:25:11'),
(11, 7, '7_97da3faa2ab85c10ae84ca9df6c6465c.png', 'Foto Tempat Usaha', '2026-01-05 15:25:11'),
(12, 7, '7_def3955cb8b6405fd135c87970f2fc95.png', 'Bukti Lunas PBB Terakhir', '2026-01-05 15:25:11');

-- --------------------------------------------------------

--
-- Table structure for table `t_pengajuan`
--

CREATE TABLE `t_pengajuan` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `jenis_id` int NOT NULL,
  `no_pengajuan` varchar(50) DEFAULT NULL,
  `status` enum('Draft','Pending','Proses','Disetujui','Ditolak','Revisi') DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_pengajuan`
--

INSERT INTO `t_pengajuan` (`id`, `user_id`, `jenis_id`, `no_pengajuan`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 5, 'REG/2025/3/696', 'Pending', '2025-12-17 13:11:52', '2025-12-21 06:57:26', '2025-12-21 13:57:26'),
(2, 3, 4, 'REG/2025/3/807', 'Proses', '2025-12-17 13:12:32', '2025-12-17 13:16:26', NULL),
(3, 3, 4, 'REG/2025/3/160', 'Disetujui', '2025-12-18 10:34:29', '2025-12-18 10:35:41', NULL),
(4, 3, 4, 'REG/2025/3/554', 'Revisi', '2025-12-21 05:25:02', '2025-12-21 06:23:47', NULL),
(5, 3, 4, 'REG/2025/3/299', 'Draft', '2025-12-21 06:08:23', '2025-12-21 06:43:25', '2025-12-21 13:43:25'),
(6, 3, 4, 'REG/2025/3/726', 'Draft', '2025-12-21 06:56:03', '2025-12-21 06:56:19', '2025-12-21 13:56:19'),
(7, 4, 3, 'REG/2026/4/676', 'Disetujui', '2026-01-05 15:25:11', '2026-01-05 15:25:31', NULL),
(8, 4, 7, 'REG/2026/4/829', 'Pending', '2026-01-05 15:41:48', '2026-01-05 15:41:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_pengajuan_detail`
--

CREATE TABLE `t_pengajuan_detail` (
  `id` int NOT NULL,
  `pengajuan_id` int NOT NULL,
  `nama_field` varchar(100) NOT NULL,
  `isi_field` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_pengajuan_detail`
--

INSERT INTO `t_pengajuan_detail` (`id`, `pengajuan_id`, `nama_field`, `isi_field`) VALUES
(1, 1, 'Nama Pemohon', 'Farrl Ranggaza'),
(2, 1, 'Nama Usaha', 'Air Kelapa Murni'),
(3, 1, 'Bentuk Usaha', 'Kuliner'),
(4, 1, 'Modal Usaha (Rp)', '10000'),
(5, 1, 'Sarana Usaha', 'Stand'),
(6, 1, 'Alamat Usaha', 'Jl hang Tuah 1'),
(7, 2, 'Nama Lengkap', 'Farrl Ranggaza'),
(8, 2, 'NIK', '2134543213453213'),
(9, 2, 'No. HP / WA', '085765030851'),
(10, 2, 'Keperluan Pengurusan', 'Pengurusan Domisili'),
(11, 3, 'Nama Lengkap', 'Farrl Ranggaza'),
(12, 3, 'NIK', '2134543213453213'),
(13, 3, 'No. HP / WA', '085765030851'),
(14, 3, 'Keperluan Pengurusan', 'acara'),
(15, 4, 'Nama Lengkap', 'Farrl Ranggaza Brinadi'),
(16, 4, 'NIK', '2134543213453213'),
(17, 4, 'No. HP / WA', '085765030851'),
(18, 4, 'Keperluan Pengurusan', 'Pindah rumah'),
(19, 5, 'Nama Lengkap', 'Farrl Ranggaza Brinadi'),
(20, 5, 'NIK', '2134543213453213'),
(21, 5, 'No. HP / WA', '085765030851'),
(22, 5, 'Keperluan Pengurusan', '3244t'),
(23, 6, 'Nama Lengkap', 'Farrl Ranggaza Brinadi'),
(24, 6, 'NIK', '2134543213453213'),
(25, 6, 'No. HP / WA', '085765030851'),
(26, 6, 'Keperluan Pengurusan', 'aaa'),
(27, 7, 'Nama Pemilik', 'Muhammad Bintang Syahrul Putra Basupi'),
(28, 7, 'Nama Usaha', 'Warungku'),
(29, 7, 'Bidang Usaha', 'asq'),
(30, 7, 'Alamat Lokasi Usaha', 'qw'),
(31, 7, 'Omzet Bulanan (Rp)', '123'),
(32, 8, 'nama', 'Muhammad Bintang Syahrul Putra Basupi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `alamat` text,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `role` enum('warga','petugas','admin') DEFAULT 'warga',
  `pekerjaan` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `no_hp`, `nik`, `nama_lengkap`, `alamat`, `tempat_lahir`, `tanggal_lahir`, `role`, `pekerjaan`, `is_active`, `created_at`) VALUES
(1, 'admin', '$2y$10$B1EDtJNtNAyodPcPv/zMy.UCSGxACPdMP3C2deogA4hgTT94J5tJO', 'admin@ecivitas.com', NULL, NULL, 'Administrator', NULL, NULL, NULL, 'admin', NULL, 1, '2025-12-01 15:13:35'),
(2, 'petugas01', '$2y$10$ngDC8lzgNb2Cxgt28feJYOYlq52g96de/9Ff62krvj.crgeHXVZTe', 'Zacky12@ecivitas.com', '08809009089', NULL, 'Zacky Ramadhan Saputra', NULL, NULL, NULL, 'petugas', NULL, 1, '2025-12-01 15:13:35'),
(3, 'warga01', '$2y$10$.xkS3nMmi0CWCRyIZywpnuYrD.PmTOyzYh9r0uHmiUJSgNu.djGNO', 'farrl@gmail.com', '085765030851', '2134543213453213', 'Farrl Ranggaza Brinadi', 'Batam centre, Bukit Surya Indah Block A2 No 23', 'Tanjung Uban', '2004-09-25', 'warga', 'Programmer', 1, '2025-12-01 15:13:35'),
(4, 'Bintang', '$2y$10$u6P0LvdYmFiCdIqoeOXAyOe5k.WChrt7hqjKiohccJE5HNGuHnX3.', 'Bintang@Ecivitas.com', '0895603632238', '1234567890000016', 'Muhammad Bintang Syahrul Putra Basupi', 'Perum Prima Garden Blok M no. 42', 'Batam', '2004-08-27', 'warga', 'Pengangguran', 1, '2025-12-03 04:22:12'),
(5, 'zacky', '$2y$10$XkERJNDVBmG2UEHUHK6G9O3yPZcldoR20NckrxSFw80P6PQ3ikznO', 'Zacky@Ecivitas.com', NULL, NULL, 'Zacky Ramadhan Saputra', NULL, NULL, NULL, 'warga', NULL, 1, '2025-12-03 05:41:44'),
(6, 'Ali', '$2y$10$kh4Fjtio1PjQY6gCnKS2GeR8qKyj.H7d.JHbhwWli9ob.RFEL0/Ye', 'Ali@Ecivitas.com', NULL, NULL, 'Fahrizal Ali Pradana', NULL, NULL, NULL, 'petugas', NULL, 1, '2025-12-03 05:43:17'),
(10, 'user1', '$2y$10$f4WF8OoEJ6uzKJaBwC/kF./0X1GyZVi9..gqZl3lD5C.5CPeqFy.m', 'user1@contoh.com', NULL, '1234567890123456', 'budi', NULL, NULL, NULL, 'warga', NULL, 1, '2025-12-29 13:13:30'),
(11, 'brinadi', '$2y$10$mbrytL8CP20RXD/chNfe4umbG1Bj4CP0wx0GfKDhEaiKG/jJOCLA2', 'brinadi@hello.com', NULL, '2222222222222222', 'Brinadi', NULL, NULL, NULL, 'warga', NULL, 1, '2026-01-05 14:44:21'),
(12, 'ibra', '$2y$10$/.mYdQNDkOUK45e.Pib3Je8h5xbFyUtMCS2FwTqURCpLZoPOjfMyq', 'ibra@hello.com', NULL, '1122334455667788', 'IBRA', NULL, NULL, NULL, 'warga', NULL, 1, '2026-01-05 14:50:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_form_template`
--
ALTER TABLE `m_form_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jenis_id` (`jenis_id`);

--
-- Indexes for table `m_instansi`
--
ALTER TABLE `m_instansi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_jenis_dokumen`
--
ALTER TABLE `m_jenis_dokumen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_syarat_lampiran`
--
ALTER TABLE `m_syarat_lampiran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jenis_id` (`jenis_id`);

--
-- Indexes for table `t_histori`
--
ALTER TABLE `t_histori`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `t_lampiran`
--
ALTER TABLE `t_lampiran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`);

--
-- Indexes for table `t_pengajuan`
--
ALTER TABLE `t_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `jenis_id` (`jenis_id`);

--
-- Indexes for table `t_pengajuan_detail`
--
ALTER TABLE `t_pengajuan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_form_template`
--
ALTER TABLE `m_form_template`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `m_jenis_dokumen`
--
ALTER TABLE `m_jenis_dokumen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `m_syarat_lampiran`
--
ALTER TABLE `m_syarat_lampiran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `t_histori`
--
ALTER TABLE `t_histori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `t_lampiran`
--
ALTER TABLE `t_lampiran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `t_pengajuan`
--
ALTER TABLE `t_pengajuan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `t_pengajuan_detail`
--
ALTER TABLE `t_pengajuan_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `m_form_template`
--
ALTER TABLE `m_form_template`
  ADD CONSTRAINT `m_form_template_ibfk_1` FOREIGN KEY (`jenis_id`) REFERENCES `m_jenis_dokumen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `m_syarat_lampiran`
--
ALTER TABLE `m_syarat_lampiran`
  ADD CONSTRAINT `m_syarat_lampiran_ibfk_1` FOREIGN KEY (`jenis_id`) REFERENCES `m_jenis_dokumen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_histori`
--
ALTER TABLE `t_histori`
  ADD CONSTRAINT `t_histori_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `t_pengajuan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_histori_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `t_lampiran`
--
ALTER TABLE `t_lampiran`
  ADD CONSTRAINT `t_lampiran_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `t_pengajuan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_pengajuan`
--
ALTER TABLE `t_pengajuan`
  ADD CONSTRAINT `t_pengajuan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `t_pengajuan_ibfk_2` FOREIGN KEY (`jenis_id`) REFERENCES `m_jenis_dokumen` (`id`);

--
-- Constraints for table `t_pengajuan_detail`
--
ALTER TABLE `t_pengajuan_detail`
  ADD CONSTRAINT `t_pengajuan_detail_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `t_pengajuan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
