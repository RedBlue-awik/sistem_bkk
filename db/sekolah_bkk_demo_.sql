-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Sep 2025 pada 15.05
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `kode_admin` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telepon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `kode_admin`, `nama`, `email`, `telepon`) VALUES
(34, 'A001', 'admin', 'admin@gmail.com', '0818-7866-7658');

-- --------------------------------------------------------

--
-- Struktur dari tabel `alumni`
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
-- Dumping data untuk tabel `alumni`
--

INSERT INTO `alumni` (`id_alumni`, `kode_alumni`, `nama`, `nisn`, `jurusan`, `tahun_lulus`, `telepon`, `alamat`) VALUES
(1, 'S001', 'Sari Nirmala', '1234567890', 'Teknik Komputer dan Jaringan', '2022', '0812-3456-7890', 'Jl. Merdeka No. 123, Jakarta'),
(2, 'S002', 'Budi Santoso', '1234567891', 'Multimedia', '2021', '0813-4567-8901', 'Jl. Sudirman No. 45, Bandung'),
(3, 'S003', 'Dewi Handayani', '1234567892', 'Akuntansi', '2023', '0814-5678-9012', 'Jl. Gatot Subroto No. 67, Surabaya'),
(4, 'S004', 'Riko Pratama', '1234567893', 'Teknik Komputer dan Jaringan', '2022', '0815-6789-0123', 'Jl. Thamrin No. 89, Semarang'),
(5, 'S005', 'Lina Wati', '1234567894', 'Multimedia', '2023', '0816-7890-1234', 'Jl. Asia Afrika No. 12, Yogyakarta'),
(6, 'S006', 'Andi Wijaya', '1234567895', 'Teknik Komputer dan Jaringan', '2021', '0817-8901-2345', 'Jl. Ahmad Yani No. 78, Jakarta'),
(7, 'S007', 'Maya Sari', '1234567896', 'Akuntansi', '2022', '0818-9012-3456', 'Jl. Diponegoro No. 34, Bandung'),
(8, 'S008', 'David Gunawan', '1234567897', 'Multimedia', '2023', '0819-0123-4567', 'Jl. Gajah Mada No. 56, Surabaya'),
(9, 'S009', 'Sinta Dewi', '1234567898', 'Teknik Komputer dan Jaringan', '2021', '0820-1234-5678', 'Jl. Hayam Wuruk No. 90, Semarang'),
(10, 'S010', 'Fajar Rahman', '1234567899', 'Akuntansi', '2022', '0821-2345-6789', 'Jl. Pahlawan No. 23, Yogyakarta'),
(11, 'S011', 'Rina Amelia', '1234567800', 'Multimedia', '2023', '0822-3456-7890', 'Jl. Merdeka No. 67, Malang'),
(12, 'S012', 'Agus Setiawan', '1234567801', 'Teknik Komputer dan Jaringan', '2022', '0823-4567-8901', 'Jl. Sudirman No. 45, Bogor'),
(13, 'S013', 'Diana Putri', '1234567802', 'Akuntansi', '2021', '0824-5678-9012', 'Jl. Gajah Mada No. 34, Medan'),
(14, 'S014', 'Ryan Hermawan', '1234567803', 'Multimedia', '2023', '0825-6789-0123', 'Jl. Thamrin No. 78, Bali'),
(15, 'S015', 'Nina Sari', '1234567804', 'Teknik Komputer dan Jaringan', '2022', '0826-7890-1234', 'Jl. Merdeka No. 56, Makassar'),
(16, 'S016', 'Aldo Pratama', '1234567805', 'Akuntansi', '2021', '0827-8901-2345', 'Jl. Sudirman No. 89, Palembang'),
(17, 'S017', 'Sari Melati', '1234567806', 'Multimedia', '2023', '0828-9012-3456', 'Jl. Asia Afrika No. 12, Lombok'),
(18, 'S018', 'Reza Firmansyah', '1234567807', 'Teknik Komputer dan Jaringan', '2022', '0829-0123-4567', 'Jl. Pahlawan No. 34, Batam');

-- --------------------------------------------------------

--
-- Struktur dari tabel `backup_db`
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
-- Struktur dari tabel `lamaran`
--

CREATE TABLE `lamaran` (
  `id_lamaran` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_lowongan` int(11) NOT NULL,
  `tanggal_lamar` date NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lamaran`
--

INSERT INTO `lamaran` (`id_lamaran`, `id_siswa`, `id_lowongan`, `tanggal_lamar`, `status`) VALUES
(1, 1, 1, '2025-09-10', 'Diterima'),
(2, 1, 2, '2025-09-12', 'Diproses'),
(3, 4, 1, '2025-09-15', 'Diproses'),
(4, 6, 1, '2025-09-18', 'Ditolak'),
(5, 9, 3, '2025-11-12', 'Diterima'),
(6, 12, 3, '2025-11-15', 'Diproses'),
(7, 15, 4, '2025-12-03', 'Diproses'),
(8, 18, 4, '2025-12-05', 'Diterima'),
(9, 1, 16, '2025-09-28', 'Diterima'),
(10, 4, 17, '2025-11-20', 'Diproses'),
(11, 6, 18, '2025-12-10', 'Diproses'),
(12, 9, 16, '2025-12-12', 'Ditolak'),
(13, 2, 5, '2025-09-11', 'Diterima'),
(14, 5, 5, '2025-09-16', 'Diterima'),
(15, 8, 6, '2025-11-22', 'Diterima'),
(16, 11, 6, '2025-11-25', 'Diproses'),
(17, 14, 7, '2025-11-14', 'Diterima'),
(18, 17, 7, '2025-11-16', 'Diproses'),
(19, 2, 8, '2025-12-07', 'Diproses'),
(20, 5, 8, '2025-12-09', 'Diterima'),
(21, 3, 9, '2025-09-13', 'Diproses'),
(22, 7, 9, '2025-09-17', 'Ditolak'),
(23, 10, 10, '2025-10-10', 'Diterima'),
(24, 13, 10, '2025-10-12', 'Diproses'),
(25, 16, 11, '2025-11-18', 'Diterima'),
(26, 3, 12, '2025-12-12', 'Diproses'),
(27, 7, 12, '2025-12-14', 'Ditolak'),
(28, 3, 13, '2025-09-14', 'Ditolak'),
(29, 10, 13, '2025-09-21', 'Diterima'),
(30, 13, 14, '2025-11-10', 'Diproses'),
(31, 16, 15, '2025-12-14', 'Diproses'),
(32, 4, 19, '2025-10-08', 'Diproses'),
(33, 9, 20, '2025-10-06', 'Diterima'),
(34, 12, 21, '2025-12-05', 'Diproses'),
(35, 15, 19, '2025-12-08', 'Ditolak'),
(36, 7, 22, '2025-10-11', 'Diterima'),
(37, 10, 23, '2025-10-13', 'Diproses'),
(38, 13, 24, '2025-12-08', 'Diterima'),
(39, 8, 25, '2025-10-16', 'Diterima'),
(40, 11, 26, '2025-10-19', 'Diproses'),
(41, 14, 27, '2025-12-11', 'Diproses'),
(42, 9, 28, '2025-10-21', 'Diproses'),
(43, 12, 29, '2025-12-13', 'Diterima'),
(44, 10, 30, '2025-10-26', 'Diterima'),
(45, 13, 31, '2025-10-29', 'Diproses'),
(46, 16, 32, '2025-12-16', 'Diproses'),
(47, 11, 33, '2025-11-02', 'Diterima'),
(48, 14, 34, '2025-11-06', 'Diproses'),
(49, 17, 35, '2025-12-09', 'Diterima'),
(50, 6, 36, '2025-11-26', 'Diproses'),
(51, 7, 37, '2025-11-29', 'Diterima'),
(52, 8, 38, '2025-12-02', 'Diproses'),
(53, 9, 39, '2025-12-05', 'Ditolak'),
(54, 10, 40, '2025-12-08', 'Diproses'),
(55, 1, 41, '2025-12-06', 'Ditolak'),
(56, 2, 42, '2025-12-11', 'Diproses'),
(57, 3, 43, '2025-12-16', 'Diterima'),
(58, 4, 44, '2025-12-21', 'Diproses'),
(59, 5, 45, '2025-12-26', 'Diterima');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_login`
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
-- Struktur dari tabel `lowongan`
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
  `tanggal_ditutup` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lowongan`
--

INSERT INTO `lowongan` (`id_lowongan`, `id_perusahaan`, `judul`, `deskripsi`, `persyaratan`, `mata_uang`, `gaji`, `kpn_gaji_diberi`, `tanggal_dibuka`, `tanggal_ditutup`) VALUES
(1, 1, 'Junior Web Developer', 'Membuat dan mengembangkan website perusahaan serta aplikasi web internal. Bekerja dalam tim yang dinamis dan inovatif.', 'Lulusan SMK TKJ/DKV, Menguasai HTML, CSS, JavaScript, PHP', 'RP', '4.000.000 - 6.000.000', 'B', '2025-09-01', '2025-10-15'),
(2, 1, 'IT Support', 'Memberikan dukungan teknis untuk karyawan, maintenance hardware dan software, troubleshooting jaringan.', 'Lulusan SMK TKJ, Menguasai jaringan komputer, troubleshooting', 'RP', '3.500.000 - 4.500.000', 'B', '2025-09-05', '2025-10-20'),
(3, 1, 'Network Administrator', 'Mengelola jaringan perusahaan, troubleshooting, maintenance server dan sistem keamanan.', 'Lulusan SMK TKJ, Menguasai jaringan, server, troubleshooting', 'RP', '5.500.000 - 7.000.000', 'B', '2025-11-10', '2026-01-20'),
(4, 1, 'Database Administrator', 'Mengelola dan maintenance database perusahaan, backup data, optimasi query.', 'Lulusan SMK TKJ, Menguasai MySQL, PostgreSQL, database management', 'RP', '5.000.000 - 6.500.000', 'B', '2025-12-01', '2026-02-15'),
(5, 2, 'Graphic Designer', 'Membuat desain untuk media sosial, brosur, banner, dan materi promosi lainnya. Bekerja dengan tim kreatif.', 'Lulusan SMK Multimedia, Menguasai Adobe Photoshop, Illustrator', 'RP', '4.000.000 - 5.000.000', 'B', '2025-09-10', '2025-10-25'),
(6, 2, 'Video Editor', 'Mengedit video untuk iklan, konten sosial media, corporate video dan dokumentasi.', 'Lulusan SMK Multimedia, Menguasai Premiere Pro, After Effects', 'RP', '4.500.000 - 6.000.000', 'B', '2025-11-20', '2026-02-10'),
(7, 2, 'Social Media Specialist', 'Mengelola media sosial perusahaan, membuat konten, analisis engagement dan campaign.', 'Lulusan SMK Multimedia, Menguasai Instagram, TikTok, Facebook', 'RP', '4.000.000 - 5.000.000', 'B', '2025-11-12', '2026-01-25'),
(8, 2, 'UI/UX Designer', 'Mendesain pengalaman pengguna yang optimal untuk aplikasi mobile dan web. Membuat prototype dan user flow.', 'Lulusan SMK Multimedia, Menguasai Figma, Adobe XD, design thinking', 'RP', '4.500.000 - 6.000.000', 'B', '2025-12-05', '2026-02-20'),
(9, 3, 'Customer Service', 'Melayani nasabah, memberikan informasi produk, menangani keluhan, dan menjaga hubungan baik dengan nasabah.', 'Lulusan SMK Akuntansi/Multimedia, Komunikasi baik, ramah', 'RP', '3.500.000 - 4.500.000', 'B', '2025-09-15', '2025-10-30'),
(10, 3, 'Teller Bank', 'Melayani transaksi tunai, setoran, penarikan, pembukaan rekening dan layanan perbankan.', 'Lulusan SMK Akuntansi, Jujur, teliti, komunikasi baik', 'RP', '3.800.000 - 4.800.000', 'B', '2025-10-08', '2025-11-28'),
(11, 3, 'Back Office Bank', 'Mengelola data nasabah, administrasi kredit, laporan keuangan dan dokumen perbankan.', 'Lulusan SMK Akuntansi, Teliti, jujur, menguasai Excel', 'RP', '4.000.000 - 5.000.000', 'B', '2025-11-15', '2026-01-30'),
(12, 3, 'Marketing Officer', 'Menawarkan produk perbankan, mencari nasabah baru, mencapai target penjualan produk.', 'Lulusan SMK Apapun, Komunikasi baik, target oriented', 'RP', '3.500.000 - 5.000.000', 'B', '2025-12-10', '2026-02-28'),
(13, 4, 'Kasir', 'Melayani transaksi pembayaran, mengelola kas, dan memberikan pelayanan terbaik kepada pelanggan.', 'Lulusan SMK Akuntansi, Jujur, teliti, komunikasi baik', 'RP', '3.000.000 - 4.000.000', 'B', '2025-09-20', '2025-11-05'),
(14, 4, 'Supervisor Toko', 'Mengawasi operasional toko, training karyawan, laporan penjualan dan inventory.', 'Lulusan SMK, Leadership, pengalaman retail, komunikasi', 'RP', '4.200.000 - 5.200.000', 'B', '2025-11-08', '2026-01-15'),
(15, 4, 'Staff Gudang Retail', 'Mengelola inventory barang, penerimaan dan pengiriman barang, sistem gudang toko.', 'Lulusan SMK Akuntansi/TKJ, Jujur, fisik sehat, memahami sistem gudang', 'RP', '3.200.000 - 4.200.000', 'B', '2025-12-12', '2026-02-25'),
(16, 5, 'Frontend Developer', 'Mengembangkan interface website dan aplikasi web menggunakan teknologi modern. Bekerja dalam tim yang agile.', 'Lulusan SMK TKJ/Multimedia, Menguasai React/Vue.js, HTML5, CSS3', 'RP', '5.000.000 - 7.000.000', 'B', '2025-09-25', '2025-11-10'),
(17, 5, 'Backend Developer', 'Mengembangkan sistem backend, API, database, dan integrasi sistem dengan teknologi terbaru.', 'Lulusan SMK TKJ, Menguasai PHP/Laravel, MySQL, API', 'RP', '5.000.000 - 7.000.000', 'B', '2025-11-18', '2026-02-05'),
(18, 5, 'Mobile Developer', 'Mengembangkan aplikasi mobile untuk iOS dan Android menggunakan React Native/Flutter.', 'Lulusan SMK TKJ/Multimedia, Menguasai React Native/Flutter', 'RP', '5.500.000 - 7.500.000', 'B', '2025-12-08', '2026-02-20'),
(19, 6, 'Site Supervisor', 'Mengawasi pekerjaan konstruksi di lapangan, memastikan kualitas dan keselamatan kerja.', 'Lulusan SMK Teknik Bangunan, Memahami gambar teknik, leadership', 'RP', '4.500.000 - 5.500.000', 'B', '2025-10-01', '2025-11-20'),
(20, 6, 'Quality Control', 'Memeriksa kualitas material dan pekerjaan konstruksi, membuat laporan inspeksi harian.', 'Lulusan SMK Teknik Bangunan, Teliti, jujur, memahami standar quality', 'RP', '4.000.000 - 5.000.000', 'B', '2025-10-05', '2025-11-25'),
(21, 6, 'Drafter AutoCAD', 'Membuat gambar teknik, shop drawing, dan dokumentasi proyek konstruksi menggunakan AutoCAD.', 'Lulusan SMK Teknik Gambar Bangunan, Menguasai AutoCAD', 'RP', '4.200.000 - 5.200.000', 'B', '2025-12-03', '2026-02-15'),
(22, 7, 'Administrasi Rumah Sakit', 'Mengelola administrasi pasien, rekam medis, dan pendaftaran rawat jalan dan inap.', 'Lulusan SMK Administrasi Perkantoran, Komputerisasi, komunikasi baik', 'RP', '3.500.000 - 4.500.000', 'B', '2025-10-10', '2025-11-30'),
(23, 7, 'Apoteker Assistant', 'Membantu apoteker dalam penyiapan obat, pelayanan resep, dan inventory obat.', 'Lulusan SMK Farmasi, Teliti, bertanggung jawab, memahami obat', 'RP', '3.500.000 - 4.500.000', 'B', '2025-10-12', '2025-12-05'),
(24, 7, 'Perawat Pendamping', 'Membantu perawat dalam perawatan pasien, monitoring vital sign, dan dokumentasi medis.', 'Lulusan SMK Keperawatan, Sabar, teliti, empati tinggi', 'RP', '3.800.000 - 4.800.000', 'B', '2025-12-06', '2026-02-20'),
(25, 8, 'Guru Bimbingan Komputer', 'Mengajar kursus komputer untuk siswa, membuat materi pembelajaran, evaluasi siswa.', 'Lulusan SMK TKJ, Menguasai Office, programming dasar, sabar mengajar', 'RP', '4.000.000 - 5.000.000', 'B', '2025-10-15', '2025-12-10'),
(26, 8, 'Administrasi Pendidikan', 'Mengelola administrasi siswa, pembayaran SPP, dan data akademik serta keuangan.', 'Lulusan SMK Akuntansi, Teliti, jujur, menguasai komputer', 'RP', '3.500.000 - 4.500.000', 'B', '2025-10-18', '2025-12-15'),
(27, 8, 'Tutor Matematika', 'Mengajar matematika untuk siswa SD-SMP, membuat modul, evaluasi belajar siswa.', 'Lulusan SMK, Menguasai matematika, sabar, komunikasi baik', 'RP', '3.500.000 - 4.500.000', 'B', '2025-12-09', '2026-02-25'),
(28, 9, 'Staff Gudang Logistik', 'Mengelola inventory barang, penerimaan dan pengiriman barang, sistem gudang modern.', 'Lulusan SMK Akuntansi/TKJ, Jujur, fisik sehat, memahami sistem gudang', 'RP', '3.200.000 - 4.200.000', 'B', '2025-10-20', '2025-12-20'),
(29, 9, 'Admin Logistik', 'Mengelola dokumen pengiriman, tracking barang, koordinasi dengan kurir dan customer.', 'Lulusan SMK Administrasi Perkantoran, Teliti, komputerisasi, komunikasi', 'RP', '3.500.000 - 4.500.000', 'B', '2025-12-11', '2026-02-28'),
(30, 10, 'Front Office Staff', 'Melayani check-in/check-out tamu, reservation, customer service hotel yang profesional.', 'Lulusan SMK Perhotelan/Akuntansi, Bahasa Inggris, komunikasi baik', 'RP', '3.500.000 - 4.500.000', 'B', '2025-10-25', '2025-12-28'),
(31, 10, 'Housekeeping Supervisor', 'Mengawasi kebersihan kamar, training housekeeping, quality control kamar dan area hotel.', 'Lulusan SMK Perhotelan, Leadership, perhatian detail, jujur', 'RP', '4.000.000 - 5.000.000', 'B', '2025-10-28', '2025-12-30'),
(32, 10, 'Wait Staff Restaurant', 'Melayani tamu di restaurant hotel, mengambil order, serving makanan dan minuman.', 'Lulusan SMK Perhotelan/Jasa Boga, Ramah, komunikasi baik, penampilan rapi', 'RP', '3.200.000 - 4.200.000', 'B', '2025-12-14', '2026-02-20'),
(33, 11, 'Mekanik Motor', 'Melakukan service dan perbaikan motor, maintenance bengkel, pelayanan customer yang ramah.', 'Lulusan SMK Teknik Otomotif, Menguasai mesin motor, jujur, ramah', 'RP', '3.800.000 - 4.800.000', 'B', '2025-11-01', '2026-01-05'),
(34, 11, 'Sales Motor', 'Melakukan penjualan motor, konsultasi produk, follow up customer dan mencapai target.', 'Lulusan SMK, Komunikasi baik, target oriented, pengalaman sales', 'RP', '3.000.000 - 5.000.000', 'B', '2025-11-05', '2026-01-10'),
(35, 11, 'Service Advisor', 'Melayani customer service motor, membuat estimasi biaya perbaikan, koordinasi dengan mekanik.', 'Lulusan SMK Teknik Otomotif, Komunikasi baik, memahami teknis motor', 'RP', '4.000.000 - 5.000.000', 'B', '2025-12-07', '2026-02-15'),
(36, 6, 'Pekerja Harian Konstruksi', 'Bekerja di proyek konstruksi dengan sistem harian. Cocok untuk yang butuh kerja fleksibel.', 'Sehat jasmani, disiplin, siap kerja fisik', 'RP', '80.000 - 120.000', 'H', '2025-11-25', '2026-01-31'),
(37, 9, 'Driver Harian Logistik', 'Mengantar barang dengan sistem harian. Bisa pilih hari kerja sendiri, rute dalam kota.', 'SIM C/A, sehat, jujur, mengenal jalan Jakarta', 'RP', '75.000 - 100.000', 'H', '2025-11-28', '2026-02-15'),
(38, 4, 'Sales Part Time Harian', 'Penjualan dengan sistem komisi dan gaji harian. Jam kerja fleksibel, bisa mahasiswa.', 'Komunikasi baik, percaya diri, target oriented', 'RP', '50.000 - 150.000', 'H', '2025-12-01', '2026-02-20'),
(39, 10, 'Banquet Staff Harian', 'Bekerja di event dan banquet hotel dengan sistem harian. Jam kerja sesuai event.', 'Lulusan SMK, fisik sehat, komunikasi baik', 'RP', '70.000 - 100.000', 'H', '2025-12-04', '2026-02-25'),
(40, 2, 'Freelance Designer Harian', 'Membuat desain project based dengan sistem harian. Bekerja remote atau onsite.', 'Lulusan SMK Multimedia, portfolio design', 'RP', '100.000 - 200.000', 'H', '2025-12-07', '2026-03-05'),
(41, 1, 'IT Manager', 'Memimpin tim IT, mengelola proyek teknologi, dan strategi digital perusahaan. Minimal pengalaman 3 tahun.', 'Pengalaman 3 tahun, leadership, manajemen proyek, sertifikasi IT', 'RP', '60.000.000 - 84.000.000', 'T', '2025-12-05', '2026-02-28'),
(42, 3, 'Assistant Branch Manager', 'Membantu mengelola cabang bank, supervise tim, pencapaian target cabang. Pengalaman 2 tahun di perbankan.', 'Pengalaman 2 tahun, leadership, memahami perbankan, analisis keuangan', 'RP', '48.000.000 - 72.000.000', 'T', '2025-12-10', '2026-03-15'),
(43, 10, 'Hotel Operation Manager', 'Mengelola operasional hotel, supervise semua departemen, quality control dan standard service.', 'Pengalaman 3 tahun hospitality, leadership kuat, manajemen operasional', 'RP', '54.000.000 - 78.000.000', 'T', '2025-12-15', '2026-03-31'),
(44, 4, 'Store Manager', 'Mengelola operasional toko retail, supervise 20+ karyawan, target penjualan dan profit toko.', 'Pengalaman 3 tahun retail, leadership, analisis bisnis, manajemen toko', 'RP', '42.000.000 - 60.000.000', 'T', '2025-12-20', '2026-03-20'),
(45, 6, 'Project Manager Konstruksi', 'Memimpin proyek konstruksi dari planning sampai execution, manage budget dan timeline project.', 'Pengalaman 4 tahun konstruksi, manajemen proyek, leadership, Sertifikasi PM', 'RP', '66.000.000 - 90.000.000', 'T', '2025-12-25', '2026-03-25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `online_users`
--

CREATE TABLE `online_users` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
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
-- Dumping data untuk tabel `pengumuman`
--

INSERT INTO `pengumuman` (`id_pengumuman`, `judul`, `isi`, `tanggal`, `ditujukan`, `id_siswa`) VALUES
(1, 'Penerimaan Karyawan Baru Tech Solution', 'Bagi alumni yang berminat bekerja di Tech Solution Indonesia, segera daftar sebelum tanggal 15 Oktober 2025. Persiapkan CV dan portofolio dengan baik. Lowongan tersedia untuk Junior Web Developer dan IT Support.', '2025-09-15 10:00:00', 'semua', NULL),
(2, 'Pelatihan Wawancara Kerja', 'Akan diadakan pelatihan wawancara kerja pada tanggal 25 September 2025 pukul 09.00 - 15.00 WIB di Aula Sekolah. Daftar segera ke bagian BKK sekolah. Gratis untuk alumni aktif.', '2025-09-12 14:30:00', 'semua', NULL),
(3, 'Lowongan Prioritas untuk Jurusan TKJ', 'Tech Solution membuka lowongan khusus untuk lulusan TKJ dengan gaji yang kompetitif RP 4jt - 6jt / B. Minat? Hubungi BKK segera. Kuota terbatas!', '2025-09-18 09:15:00', 'khusus', 1),
(4, 'Pengumuman Kelulusan Seleksi', 'Selamat kepada Sari Nirmala (S001) yang telah diterima di Tech Solution Indonesia sebagai Junior Web Developer dengan gaji RP 5.5jt / B. Semoga sukses!', '2025-09-20 11:00:00', 'khusus', 1),
(5, 'Info Magang Creative Design Studio', 'Buka kesempatan magang untuk jurusan Multimedia. Durasi 3 bulan dengan kemungkinan diterima sebagai karyawan tetap. Pendaftaran sampai 25 Oktober 2025.', '2025-09-16 13:45:00', 'semua', NULL),
(6, 'Job Fair Nasional 2025', 'Akan diadakan Job Fair Nasional di Convention Hall pada tanggal 15-17 November 2025. 50+ perusahaan ternama akan hadir. Daftarkan diri Anda melalui BKK sekolah sebelum 10 November.', '2025-10-05 09:00:00', 'semua', NULL),
(7, 'Lowongan Prioritas Digital Maker', 'Digital Maker Studio membuka lowongan khusus untuk lulusan Multimedia dengan skill design yang baik. Gaji RP 4.5jt - 6jt / B. Segera daftar!', '2025-10-08 11:30:00', 'khusus', 8),
(8, 'Pelatihan Coding Gratis', 'Bekerjasama dengan Tech Solution, akan diadakan pelatihan coding gratis selama 2 minggu. Materi: HTML, CSS, JavaScript, PHP. Minat? Hubungi BKK. Terbatas untuk 20 peserta.', '2025-10-12 14:00:00', 'semua', NULL),
(9, 'Pengumuman Kelulusan Seleksi Build Construction', 'Selamat kepada David Gunawan (S008) yang diterima di Build Construction sebagai Site Supervisor dengan gaji RP 5jt / B. Selamat bekerja!', '2025-10-18 10:45:00', 'khusus', 8),
(10, 'Info Magang Health Care Hospital', 'Buka kesempatan magang 6 bulan di rumah sakit dengan allowance RP 2jt / B. Jurusan Farmasi dan Administrasi diprioritaskan. Daftar sampai 30 November.', '2025-10-22 13:20:00', 'semua', NULL),
(11, 'Workshop CV dan Interview', 'Workshop bagaimana membuat CV yang menarik dan teknik interview yang baik. Gratis untuk alumni aktif. Hari: Sabtu, 25 Oktober 2025. Tempat: Lab Komputer.', '2025-10-25 15:30:00', 'semua', NULL),
(12, 'Lowongan Hotel Grand Paradise', 'Dibutuhkan segera Front Office dan Housekeeping untuk hotel bintang 4. Gaji kompetitif RP 3.5jt - 5jt / B + tips + tunjangan. Fasilitas lengkap.', '2025-10-28 16:00:00', 'semua', NULL),
(13, 'Pengumuman Penting untuk Alumni 2023', 'Bagi alumni lulusan 2023 yang belum memiliki pekerjaan, segera daftar di program penempatan kerja BKK. Kami bantu sampai dapat kerja!', '2025-11-01 08:45:00', 'khusus', 3),
(14, 'Success Story Alumni', 'Budi Santoso (S002) berhasil menjadi Head Designer di Creative Design Studio setelah 2 tahun bekerja. Dari gaji RP 4jt sekarang menjadi RP 8jt / B. Inspirasi untuk kita semua!', '2025-11-05 12:15:00', 'semua', NULL),
(15, 'Lowongan Logistik Express', 'Dibutuhkan driver dan staff gudang untuk ekspansi cabang baru. SIM C dan kesehatan fisik menjadi syarat utama. Gaji harian RP 75rb - 100rb / H.', '2025-11-08 14:50:00', 'semua', NULL),
(16, 'Pengumuman Diterima di Multiple Perusahaan', 'Selamat kepada Rina Amelia (S011) yang diterima di 3 perusahaan sekaligus! Creative Design, Digital Maker, dan Retail Plus. Pencapaian yang membanggakan.', '2025-11-12 11:20:00', 'khusus', 11),
(17, 'Info Beasiswa Kuliah Lanjutan', 'Tersedia beasiswa S1 untuk alumni berprestasi. Pendaftaran dibuka sampai 30 Desember 2025. Jurusan: Teknik Informatika, Desain Komunikasi Visual, Manajemen.', '2025-11-15 09:30:00', 'semua', NULL),
(18, 'Lowongan Mekanik Auto Motor', 'Auto Motor Indonesia membuka lowongan untuk mekanik berpengalaman. Gaji menarik RP 3.8jt - 4.8jt / B + bonus service. Benefit: BPJS, uang makan, seragam.', '2025-11-18 13:45:00', 'semua', NULL),
(19, 'Pengumuman Kelulusan Health Care', 'Selamat kepada Diana Putri (S013) yang diterima di Health Care Hospital sebagai Administrasi Rumah Sakit dengan gaji RP 4.2jt / B. Sukses selalu!', '2025-11-20 10:15:00', 'khusus', 13),
(20, 'Job Fair Akhir Tahun 2025', 'Akan diadakan Job Fair Akhir Tahun di Mall Central Park. 100+ perusahaan dari berbagai bidang. Daftar melalui BKK sebelum 10 Desember. Free konsultasi karir.', '2025-11-22 14:00:00', 'semua', NULL),
(21, 'Pelatihan Digital Marketing', 'Gratis! Pelatihan digital marketing selama 3 hari untuk meningkatkan skill pemasaran online. Materi: SEO, Facebook Ads, Instagram Marketing. Daftar sekarang!', '2025-11-25 16:30:00', 'semua', NULL),
(22, 'Lowongan Backend Developer', 'Digital Maker Studio mencari backend developer muda yang bersemangat. Fresh graduate dipersilahkan. Gaji RP 5jt - 7jt / B. Remote work possible.', '2025-11-28 11:00:00', 'khusus', 9),
(23, 'Pengumuman Penerimaan Manager', 'Selamat kepada Fajar Rahman (S010) yang diterima sebagai Assistant Branch Manager di Global Finance Bank dengan gaji RP 60jt / T. Prestasi yang luar biasa!', '2025-12-02 15:20:00', 'khusus', 10),
(24, 'Info Lowongan Konstruksi Harian', 'Build Construction membuka lowongan pekerja harian untuk proyek gedung 20 lantai. Gaji RP 80rb - 120rb / H. Makan siang disediakan. Daftar langsung ke BKK.', '2025-12-05 10:45:00', 'semua', NULL),
(25, 'Penutupan Pendaftaran Beasiswa', 'Pendaftaran beasiswa S1 ditutup pada 30 Desember 2025. Bagi yang belum daftar, segera lengkapi berkas. Pengumuman penerima 15 Januari 2026.', '2025-12-08 13:10:00', 'semua', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman_viewed`
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
-- Struktur dari tabel `perusahaan`
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
-- Dumping data untuk tabel `perusahaan`
--

INSERT INTO `perusahaan` (`id_perusahaan`, `nama_perusahaan`, `email`, `telepon`, `alamat`, `latitude`, `longitude`, `bidang_usaha`, `logo`) VALUES
(1, 'Tech Solution Indonesia', 'hr@techsolution.co.id', '021-1234567', 'Jl. HR Rasuna Said Kav. C-22, Jakarta Selatan', -6.2088000, 106.8456000, 'Teknologi Informasi', 'tech_solution.png'),
(2, 'Creative Design Studio', 'info@creativedesign.com', '022-7654321', 'Jl. Dago No. 189, Bandung', -6.9175000, 107.6191000, 'Desain dan Kreatif', 'creative_design.png'),
(3, 'Global Finance Bank', 'recruitment@globalfinance.com', '031-9876543', 'Jl. Tunjungan No. 101, Surabaya', -7.2575000, 112.7521000, 'Perbankan dan Keuangan', 'global_finance.png'),
(4, 'Retail Plus Supermarket', 'hrd@retailplus.co.id', '024-1239876', 'Jl. Pahlawan No. 56, Semarang', -6.9667000, 110.4167000, 'Ritel dan Perdagangan', 'retail_plus.png'),
(5, 'Digital Maker Studio', 'career@digitalmaker.com', '021-5551234', 'Jl. Kemang Raya No. 12, Jakarta Selatan', -6.2663000, 106.8137000, 'Digital Agency', 'digital_maker.png'),
(6, 'Build Construction', 'hrd@buildcon.co.id', '022-4445678', 'Jl. Soekarno Hatta No. 234, Bandung', -6.9344000, 107.6346000, 'Konstruksi', 'build_con.png'),
(7, 'Health Care Hospital', 'recruitment@healthcarehospital.com', '031-3337890', 'Jl. Raya Darmo No. 89, Surabaya', -7.2892000, 112.7344000, 'Kesehatan', 'health_care.png'),
(8, 'Education Center Indonesia', 'info@educenter.id', '024-2224567', 'Jl. Sisingamangaraja No. 56, Semarang', -6.9861000, 110.4091000, 'Pendidikan', 'edu_center.png'),
(9, 'Logistik Express', 'hr@logistikexpress.co.id', '021-7778888', 'Jl. Bekasi Raya No. 123, Jakarta Timur', -6.2349000, 106.9896000, 'Logistik', 'logistik_express.png'),
(10, 'Hotel Grand Paradise', 'career@hotelgrand.com', '0274-5556666', 'Jl. Malioboro No. 78, Yogyakarta', -7.7956000, 110.3695000, 'Hospitality', 'hotel_grand.png'),
(11, 'Auto Motor Indonesia', 'hr@automotor.co.id', '021-9990000', 'Jl. MT Haryono No. 234, Jakarta Selatan', -6.2456000, 106.8523000, 'Otomotif', 'auto_motor.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `kode_pengguna` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `kode_pengguna`, `username`, `password`, `level`) VALUES
(2, 'S001', 'sari_nirmala', '0192023a7bbd73250516f069df18b500', 'alumni'),
(3, 'S002', 'budi_santoso', '0192023a7bbd73250516f069df18b500', 'alumni'),
(4, 'S003', 'dewi_handayani', '0192023a7bbd73250516f069df18b500', 'alumni'),
(5, 'S004', 'riko_pratama', '0192023a7bbd73250516f069df18b500', 'alumni'),
(6, 'S005', 'lina_wati', '0192023a7bbd73250516f069df18b500', 'alumni'),
(7, 'S006', 'andi_wijaya', '0192023a7bbd73250516f069df18b500', 'alumni'),
(8, 'S007', 'maya_sari', '0192023a7bbd73250516f069df18b500', 'alumni'),
(9, 'S008', 'david_gunawan', '0192023a7bbd73250516f069df18b500', 'alumni'),
(10, 'S009', 'sinta_dewi', '0192023a7bbd73250516f069df18b500', 'alumni'),
(11, 'S010', 'fajar_rahman', '0192023a7bbd73250516f069df18b500', 'alumni'),
(12, 'S011', 'rina_amelia', '0192023a7bbd73250516f069df18b500', 'alumni'),
(13, 'S012', 'agus_setiawan', '0192023a7bbd73250516f069df18b500', 'alumni'),
(14, 'S013', 'diana_putri', '0192023a7bbd73250516f069df18b500', 'alumni'),
(15, 'S014', 'ryan_hermawan', '0192023a7bbd73250516f069df18b500', 'alumni'),
(16, 'S015', 'nina_sari', '0192023a7bbd73250516f069df18b500', 'alumni'),
(17, 'S016', 'aldo_pratama', '0192023a7bbd73250516f069df18b500', 'alumni'),
(18, 'S017', 'sari_melati', '0192023a7bbd73250516f069df18b500', 'alumni'),
(19, 'S018', 'reza_firmansyah', '0192023a7bbd73250516f069df18b500', 'alumni'),
(34, 'A001', 'admin', 'admin123', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `kode_admin` (`kode_admin`);

--
-- Indeks untuk tabel `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`id_alumni`),
  ADD KEY `kode_alumni` (`kode_alumni`);

--
-- Indeks untuk tabel `backup_db`
--
ALTER TABLE `backup_db`
  ADD PRIMARY KEY (`id_backup`);

--
-- Indeks untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  ADD PRIMARY KEY (`id_lamaran`),
  ADD KEY `id_siswa` (`id_siswa`,`id_lowongan`),
  ADD KEY `id_lowongan` (`id_lowongan`);

--
-- Indeks untuk tabel `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`id_lowongan`),
  ADD KEY `id_perusahaan` (`id_perusahaan`);

--
-- Indeks untuk tabel `online_users`
--
ALTER TABLE `online_users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id_pengumuman`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indeks untuk tabel `pengumuman_viewed`
--
ALTER TABLE `pengumuman_viewed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_pengumuman` (`id_pengumuman`,`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id_perusahaan`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `kode_pengguna` (`kode_pengguna`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `alumni`
--
ALTER TABLE `alumni`
  MODIFY `id_alumni` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `backup_db`
--
ALTER TABLE `backup_db`
  MODIFY `id_backup` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  MODIFY `id_lamaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `log_login`
--
ALTER TABLE `log_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id_lowongan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `online_users`
--
ALTER TABLE `online_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `pengumuman_viewed`
--
ALTER TABLE `pengumuman_viewed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id_perusahaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengumuman_viewed`
--
ALTER TABLE `pengumuman_viewed`
  ADD CONSTRAINT `pengumuman_viewed_ibfk_1` FOREIGN KEY (`id_pengumuman`) REFERENCES `pengumuman` (`id_pengumuman`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengumuman_viewed_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
