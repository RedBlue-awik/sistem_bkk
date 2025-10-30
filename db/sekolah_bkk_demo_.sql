-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 11:29 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sekolah_bkk(demo)`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `kode_admin` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telepon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `id_alumni` int(11) NOT NULL,
  `kode_alumni` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nisn` varchar(255) NOT NULL,
  `jurusan` varchar(255) NOT NULL,
  `tahun_lulus` varchar(255) NOT NULL,
  `telepon` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`id_alumni`, `kode_alumni`, `nama`, `nisn`, `jurusan`, `tahun_lulus`, `telepon`, `alamat`) VALUES
(1, 'S001', 'Ahmad Rizki', '1234567890', 'RPL', '2023', '081234567890', 'Jl. Merdeka No. 10, Jakarta'),
(2, 'S002', 'Siti Nurhaliza', '1234567891', 'BUSANA', '2023', '081234567891', 'Jl. Kemerdekaan No. 20, Bandung'),
(3, 'S003', 'Budi Santoso', '1234567892', 'ATPH', '2024', '081234567892', 'Jl. Diponegoro No. 30, Surabaya'),
(4, 'S004', 'Dewi Lestari', '1234567893', 'KULINER', '2024', '081234567893', 'Jl. Gajah Mada No. 40, Semarang'),
(5, 'S005', 'Rina Wijaya', '1234567894', 'RPL', '2023', '081234567894', 'Jl. Sudirman No. 50, Jakarta'),
(6, 'S006', 'Fajar Pratama', '1234567895', 'BUSANA', '2024', '081234567895', 'Jl. Asia Afrika No. 60, Bandung'),
(7, 'S007', 'Maya Sari', '1234567896', 'ATPH', '2024', '081234567896', 'Jl. Pahlawan No. 70, Surabaya'),
(8, 'S008', 'Hendra Gunawan', '1234567897', 'KULINER', '2023', '081234567897', 'Jl. Thamrin No. 80, Jakarta'),
(9, 'S009', 'Lina Marlina', '1234567898', 'RPL', '2024', '081234567898', 'Jl. Merdeka No. 90, Semarang'),
(10, 'S010', 'Rizky Ramadhan', '1234567899', 'BUSANA', '2023', '081234567899', 'Jl. Kemerdekaan No. 100, Bandung');

-- --------------------------------------------------------

--
-- Table structure for table `backup_db`
--

CREATE TABLE `backup_db` (
  `id_backup` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `backup_data` longtext NOT NULL,
  `tanggal_backup` datetime NOT NULL DEFAULT current_timestamp(),
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lamaran`
--

CREATE TABLE `lamaran` (
  `id_lamaran` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_lowongan` int(11) NOT NULL,
  `tanggal_lamar` date NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lamaran`
--

INSERT INTO `lamaran` (`id_lamaran`, `id_siswa`, `id_lowongan`, `tanggal_lamar`, `status`, `cv`) VALUES
(1, 1, 1, '2025-01-16', 'diterima', 'cv_ahmad_rizki.pdf'),
(2, 1, 2, '2025-02-02', 'diproses', 'cv_ahmad_rizki.pdf'),
(3, 2, 3, '2025-01-21', 'ditolak', 'cv_siti_nurhaliza.pdf'),
(4, 2, 4, '2025-03-02', 'diproses', 'cv_siti_nurhaliza.pdf'),
(5, 3, 5, '2025-02-11', 'diterima', 'cv_budi_santoso.pdf'),
(6, 3, 6, '2025-01-26', 'diproses', 'cv_budi_santoso.pdf'),
(7, 4, 7, '2025-03-16', 'ditolak', 'cv_dewi_lestari.pdf'),
(8, 4, 8, '2025-04-02', 'diproses', 'cv_dewi_lestari.pdf'),
(9, 5, 9, '2025-02-21', 'diproses', 'cv_rina_wijaya.pdf'),
(10, 5, 1, '2025-01-16', 'diterima', 'cv_rina_wijaya.pdf'),
(11, 6, 3, '2025-01-21', 'ditolak', 'cv_fajar_pratama.pdf'),
(12, 6, 4, '2025-03-02', 'diproses', 'cv_fajar_pratama.pdf'),
(13, 7, 5, '2025-02-11', 'diterima', 'cv_maya_sari.pdf'),
(14, 7, 11, '2026-01-11', 'diproses', 'cv_maya_sari.pdf'),
(15, 8, 7, '2025-03-16', 'diproses', 'cv_hendra_gunawan.pdf'),
(16, 8, 12, '2026-02-02', 'ditolak', 'cv_hendra_gunawan.pdf'),
(17, 9, 2, '2025-02-02', 'diproses', 'cv_lina_marlina.pdf'),
(18, 9, 10, '2025-03-11', 'diterima', 'cv_lina_marlina.pdf'),
(19, 10, 3, '2025-01-21', 'diproses', 'cv_rizky_ramadhan.pdf'),
(20, 10, 4, '2025-03-02', 'ditolak', 'cv_rizky_ramadhan.pdf'),
(21, 1, 6, '2025-01-26', NULL, NULL),
(22, 2, 8, '2025-04-02', NULL, NULL),
(23, 3, 10, '2025-03-11', NULL, NULL),
(24, 4, 6, '2025-01-26', NULL, NULL),
(25, 5, 8, '2025-04-02', NULL, NULL),
(26, 6, 10, '2025-03-11', NULL, NULL),
(27, 7, 6, '2025-01-26', NULL, NULL),
(28, 8, 8, '2025-04-02', NULL, NULL),
(29, 9, 10, '2025-03-11', NULL, NULL),
(30, 10, 6, '2025-01-26', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `log_login`
--

CREATE TABLE `log_login` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `waktu_login` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lowongan`
--

CREATE TABLE `lowongan` (
  `id_lowongan` int(11) NOT NULL,
  `id_perusahaan` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `persyaratan` varchar(255) NOT NULL,
  `mata_uang` varchar(255) NOT NULL,
  `gaji` varchar(255) NOT NULL,
  `kpn_gaji_diberi` varchar(11) NOT NULL,
  `tanggal_dibuka` date NOT NULL,
  `tanggal_ditutup` date NOT NULL,
  `status_kerjasama` enum('bekerja_sama','tidak_bekerja_sama') DEFAULT 'tidak_bekerja_sama'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lowongan`
--

INSERT INTO `lowongan` (`id_lowongan`, `id_perusahaan`, `judul`, `deskripsi`, `persyaratan`, `mata_uang`, `gaji`, `kpn_gaji_diberi`, `tanggal_dibuka`, `tanggal_ditutup`, `status_kerjasama`) VALUES
(1, 1, 'Programmer Web', 'Mengembangkan aplikasi web menggunakan PHP, JavaScript, dan MySQL', 'Lulusan RPL,memahami OOP,HTML,CSS,JavaScript', 'RP', '7500000', 'B', '2025-01-15', '2025-12-18', 'bekerja_sama'),
(2, 5, 'Mobile Developer', 'Membuat aplikasi mobile Android dan iOS', 'Lulusan RPL,pengalaman Flutter/React Native', 'RP', '8000000', 'B', '2025-10-29', '2025-12-24', 'bekerja_sama'),
(3, 2, 'Desainer Busana', 'Mendesain pakaian dan mengawasi produksi', 'Lulusan BUSANA,kreatif,mengikuti trend fashion', 'RP', '6000000', 'B', '2026-01-05', '2026-09-17', 'bekerja_sama'),
(4, 2, 'Penjahit Profesional', 'Menjahit pakaian sesuai desain dan standar kualitas', 'Lulusan BUSANA,terampil menjahit,teliti', 'RP', '4500000', 'B', '2026-01-06', '2026-09-22', 'tidak_bekerja_sama'),
(5, 3, 'Teknisi Pertanian', 'Mengoperasikan dan merawat alat-alat pertanian', 'Lulusan ATPH,memahami mesin pertanian', 'RP', '5000000', 'B', '2026-01-01', '2026-10-27', 'bekerja_sama'),
(6, 3, 'Supervisor Kebun', 'Mengawasi proses produksi tanaman', 'Lulusan ATPH,leadership,memahami budidaya', 'RP', '6500000', 'B', '2025-11-19', '2026-02-26', 'tidak_bekerja_sama'),
(7, 4, 'Koki', 'Memasak dan menyiapkan makanan untuk restoran', 'Lulusan KULINER,kreatif,higienis', 'RP', '5500000', 'B', '2025-11-01', '2026-02-05', 'bekerja_sama'),
(8, 4, 'Baker', 'Membuat roti dan kue untuk toko roti', 'Lulusan KULINER,terampil baking,kreatif', 'RP', '4800000', 'B', '2025-04-01', '2025-11-25', 'tidak_bekerja_sama'),
(9, 1, 'IT Support', 'Memberikan dukungan teknis hardware dan software', 'Lulusan RPL,komunikatif,problem solving', 'RP', '6000000', 'B', '2025-02-20', '2026-01-28', 'bekerja_sama'),
(10, 5, 'UI/UX Designer', 'Mendesain interface dan experience aplikasi', 'Lulusan RPL,memahami Figma/Adobe XD', 'RP', '7000000', 'B', '2025-03-10', '2025-12-31', 'tidak_bekerja_sama'),
(11, 3, 'Analis Hasil Pertanian', 'Menganalisis kualitas hasil pertanian', 'Lulusan ATPH, teliti, memahami standar mutu', 'RP', '5800000', 'B', '2026-01-10', '2026-02-10', 'bekerja_sama'),
(12, 4, 'Manager Restoran', 'Mengelola operasional restoran', 'Lulusan KULINER, leadership, pengalaman 1 tahun', 'RP', '8500000', 'B', '2026-02-01', '2026-03-01', 'bekerja_sama');

-- --------------------------------------------------------

--
-- Table structure for table `online_users`
--

CREATE TABLE `online_users` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id_pengumuman` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `ditujukan` enum('semua','khusus') DEFAULT 'semua',
  `id_siswa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id_pengumuman`, `judul`, `isi`, `tanggal`, `ditujukan`, `id_siswa`) VALUES
(1, 'Lowongan Kerja PT Teknologi Indonesia', 'PT Teknologi Indonesia membuka lowongan untuk posisi Programmer Web dan IT Support khusus lulusan RPL. Bagi alumni yang berminat dapat mengirimkan lamaran sebelum 15 Februari 2025.', '2025-01-10 09:00:00', 'semua', NULL),
(2, 'Kerjasama dengan CV Fashion Makmur', 'Sekolah telah menjalin kerjasama dengan CV Fashion Makmur untuk penempatan kerja alumni BUSANA. Terbuka lowongan untuk Desainer Busana dan Penjahit Profesional.', '2025-01-18 10:30:00', 'semua', NULL),
(3, 'Pelatihan Wawancara Kerja', 'Akan diadakan pelatihan teknik wawancara kerja bagi alumni pada tanggal 25 Januari 2025. Daftar segera ke bagian BKK.', '2025-01-20 14:00:00', 'semua', NULL),
(4, 'Lowongan dari PT Agro Sejahtera', 'PT Agro Sejahtera membuka lowongan untuk Teknisi Pertanian dan Supervisor Kebun khusus lulusan ATPH. Meskipun tidak bekerjasama langsung, alumni dapat melamar secara mandiri.', '2025-02-28 11:00:00', 'semua', NULL),
(5, 'Pengumuman Khusus untuk Ahmad Rizki', 'Selamat! Lamaran Anda untuk posisi Programmer Web di PT Teknologi Indonesia telah diterima. Silakan hubungi perusahaan untuk informasi lebih lanjut.', '2025-01-20 08:00:00', 'khusus', 1),
(6, 'Pengumuman Khusus untuk Siti Nurhaliza', 'Mohon maaf, lamaran Anda untuk posisi Desainer Busana di CV Fashion Makmur belum dapat diterima. Jangan menyerah dan terus mencoba!', '2025-01-25 09:30:00', 'khusus', 2),
(7, 'Pengumuman Khusus untuk Budi Santoso', 'Selamat! Anda diterima sebagai Teknisi Pertanian di PT Agro Sejahtera. Silakan melakukan kontak dengan perusahaan untuk proses selanjutnya.', '2025-02-15 10:15:00', 'khusus', 3),
(8, 'Info Lowongan Restoran Sederhana 2026', 'Restoran Sederhana akan membuka lowongan untuk Manager Restoran pada Februari 2026. Persiapkan diri dari sekarang!', '2025-12-15 13:45:00', 'semua', NULL),
(9, 'Pengumuman Khusus untuk Lina Marlina', 'Lamaran Anda untuk posisi UI/UX Designer di PT Software House telah diterima. Silakan menghubungi perusahaan untuk jadwal wawancara.', '2025-03-15 11:20:00', 'khusus', 9),
(10, 'Peringatan Kelengkapan Berkas', 'Bagi alumni yang akan melamar kerja, pastikan CV dan dokumen pendukung sudah lengkap. CV yang tidak lengkap akan mengurangi peluang diterima.', '2025-01-12 16:00:00', 'semua', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman_viewed`
--

CREATE TABLE `pengumuman_viewed` (
  `id` int(11) NOT NULL,
  `id_pengumuman` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `tanggal_dibaca` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id_perusahaan` int(11) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telepon` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `bidang_usaha` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perusahaan`
--

INSERT INTO `perusahaan` (`id_perusahaan`, `nama_perusahaan`, `email`, `telepon`, `alamat`, `latitude`, `longitude`, `bidang_usaha`, `logo`) VALUES
(1, 'PT Teknologi Indonesia', 'hr@tekindo.co.id', '021-1234567', 'Jl. Sudirman No. 123, Jakarta', -6.2088000, 106.8456000, 'Teknologi Informasi', 'default-logo.png'),
(2, 'CV Fashion Makmur', 'info@fashionmakmur.com', '022-7654321', 'Jl. Asia Afrika No. 45, Bandung', -6.9175000, 107.6191000, 'Garmen dan Busana', 'default-logo.png'),
(3, 'PT Agro Sejahtera', 'recruitment@agrosejahtera.co.id', '031-8889999', 'Jl. Pahlawan No. 67, Surabaya', -7.2504000, 112.7688000, 'Agribisnis', 'default-logo.png'),
(4, 'Restoran Sederhana', 'hr@restoransederhana.com', '024-5556666', 'Jl. Gajah Mada No. 89, Semarang', -6.9667000, 110.4167000, 'Kuliner', 'default-logo.png'),
(5, 'PT Software House', 'career@softhouse.com', '021-3334444', 'Jl. Thamrin No. 234, Jakarta', -6.1865000, 106.8227000, 'Pengembangan Software', 'default-logo.png');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `kode_pengguna` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `kode_pengguna`, `username`, `password`, `level`) VALUES
(35, 'S001', 'ahmadrizki', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(36, 'S002', 'sitinaru', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(37, 'S003', 'budisantoso', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(38, 'S004', 'dewilestari', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(39, 'S005', 'rinawijaya', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(40, 'S006', 'fajarpratama', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(41, 'S007', 'mayasari', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(42, 'S008', 'hendragunawan', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(43, 'S009', 'linamarlina', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(44, 'S010', 'rizkyramadhan', '25d55ad283aa400af464c76d713c07ad', 'alumni');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `kode_admin` (`kode_admin`);

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`id_alumni`),
  ADD KEY `kode_alumni` (`kode_alumni`);

--
-- Indexes for table `backup_db`
--
ALTER TABLE `backup_db`
  ADD PRIMARY KEY (`id_backup`);

--
-- Indexes for table `lamaran`
--
ALTER TABLE `lamaran`
  ADD PRIMARY KEY (`id_lamaran`),
  ADD KEY `id_siswa` (`id_siswa`,`id_lowongan`),
  ADD KEY `id_lowongan` (`id_lowongan`);

--
-- Indexes for table `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`id_lowongan`),
  ADD KEY `id_perusahaan` (`id_perusahaan`);

--
-- Indexes for table `online_users`
--
ALTER TABLE `online_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id_pengumuman`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `pengumuman_viewed`
--
ALTER TABLE `pengumuman_viewed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_pengumuman` (`id_pengumuman`,`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id_perusahaan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `kode_pengguna` (`kode_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `alumni`
--
ALTER TABLE `alumni`
  MODIFY `id_alumni` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `backup_db`
--
ALTER TABLE `backup_db`
  MODIFY `id_backup` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lamaran`
--
ALTER TABLE `lamaran`
  MODIFY `id_lamaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id_lowongan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `online_users`
--
ALTER TABLE `online_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pengumuman_viewed`
--
ALTER TABLE `pengumuman_viewed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id_perusahaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pengumuman_viewed`
--
ALTER TABLE `pengumuman_viewed`
  ADD CONSTRAINT `pengumuman_viewed_ibfk_1` FOREIGN KEY (`id_pengumuman`) REFERENCES `pengumuman` (`id_pengumuman`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengumuman_viewed_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
