-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Sep 2025 pada 15.57
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
-- Database: `sekolah_bkk`
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
(7, 'S001', 'Daffa', '293385934', 'rpl', '2025-04-30', '0819-3584-7682', 'Lowayu'),
(8, 'S002', 'Yazid', '318426324', 'rpl', '2025-06-01', '0874-5397-8236', 'Banyurip'),
(10, 'S003', 'Sauqi', '04935873', 'kuliner', '2025-02-05', '0896-4587-6546', 'Banyurip'),
(25, 'S004', 'Awik', '123456789', 'rpl', '2000-02-20', '0893-4587-8340', 'Ujung Pangkah');

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
(29, 8, 30, '2025-08-08', 'Diterima Kerja'),
(30, 7, 74, '2025-08-11', 'Tidak Diterima Kerja'),
(31, 8, 74, '2025-08-24', 'Menunggu'),
(32, 8, 79, '2025-08-24', 'Menunggu'),
(33, 7, 79, '2025-08-26', 'Menunggu');

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

--
-- Dumping data untuk tabel `log_login`
--

INSERT INTO `log_login` (`id`, `id_user`, `waktu_login`, `ip_address`, `user_agent`) VALUES
(2, 40, '2025-08-13 01:55:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(5, 35, '2025-08-13 02:05:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(6, 40, '2025-08-13 02:14:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(7, 35, '2025-08-13 05:52:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(8, 35, '2025-08-13 05:55:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(9, 35, '2025-08-13 06:27:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(10, 40, '2025-08-13 06:27:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(11, 35, '2025-08-15 13:48:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(12, 35, '2025-08-17 11:07:47', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(13, 35, '2025-08-17 11:21:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(14, 38, '2025-08-17 12:15:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(15, 35, '2025-08-17 13:45:40', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36'),
(16, 40, '2025-08-17 14:26:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(17, 35, '2025-08-17 14:26:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(18, 40, '2025-08-17 14:31:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(19, 35, '2025-08-18 21:09:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(20, 35, '2025-08-18 21:14:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(21, 35, '2025-08-18 21:29:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(22, 35, '2025-08-18 21:39:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(23, 35, '2025-08-18 21:44:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(24, 35, '2025-08-18 21:45:39', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(25, 35, '2025-08-18 21:49:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(26, 35, '2025-08-18 21:51:44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(27, 35, '2025-08-22 12:29:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(28, 35, '2025-08-22 12:30:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(29, 35, '2025-08-22 12:34:47', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(30, 35, '2025-08-22 12:40:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(31, 35, '2025-08-22 12:46:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(32, 35, '2025-08-23 22:10:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(33, 40, '2025-08-24 16:40:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(34, 35, '2025-08-24 18:38:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(35, 35, '2025-08-26 11:45:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(36, 35, '2025-08-26 11:46:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(37, 40, '2025-08-26 11:47:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(38, 35, '2025-08-26 13:37:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(39, 40, '2025-08-26 13:38:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(40, 40, '2025-08-26 14:49:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(41, 38, '2025-08-26 14:49:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(42, 35, '2025-08-28 00:02:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(43, 35, '2025-08-31 15:13:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(44, 40, '2025-08-31 15:18:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(45, 35, '2025-08-31 15:56:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(46, 40, '2025-08-31 15:57:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(47, 35, '2025-09-01 14:01:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(48, 35, '2025-09-01 20:38:08', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36'),
(49, 35, '2025-09-01 20:40:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36'),
(50, 40, '2025-09-01 20:54:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');

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
(30, 2, 'Dev', 'Harus semangat', 'Lulusan IT,Bisa HTML CSS dan JS', 'Rp', '2.500.000', 'B', '2025-08-02', '2025-08-13'),
(74, 6, 'Kasir', 'Tidak ada deskripsi', 'Tidak ada persyaratan', 'Rp', '1.000.000', 'B', '2025-08-11', '2025-09-05'),
(79, 6, 'devDigital', 'Tidak ada deskripsi', 'Tidak ada persyaratan', 'Rp', '1.000.000', 'M', '2025-08-24', '2025-09-05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `online_users`
--

CREATE TABLE `online_users` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `online_users`
--

INSERT INTO `online_users` (`id`, `id_user`, `last_activity`) VALUES
(40, 38, '2025-08-26 14:49:33'),
(49, 40, '2025-09-01 20:54:30');

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
(28, 'Selamat! Lamaran Diterima', 'Lamaran anda untuk posisi <b>Dev</b> telah <b>DITERIMA</b>.', '2025-08-09 15:36:02', 'khusus', 8),
(29, 'Lowongan Baru Dibuka', 'Telah dibuka Lowongan baru di Perusahaan <b>Indomaret</b> dengan Judul : <b>Kasir</b>.', '2025-08-11 18:20:48', 'semua', NULL),
(30, 'Maaf, Lamaran Tidak Diterima', 'Lamaran anda untuk posisi <b>Kasir</b> <b>TIDAK DITERIMA</b>.', '2025-08-11 18:27:24', 'khusus', 7),
(31, 'Lowongan Baru Dibuka', 'Telah dibuka Lowongan baru di Perusahaan <b>PT Teknologi Indonesia</b> dengan Judul : <b>Iure quisquam labore</b>.', '2025-08-18 21:10:27', 'semua', NULL),
(39, 'Lowongan Baru Dibuka', 'Telah dibuka Lowongan baru di Perusahaan <b>Indomaret</b> dengan Judul : <b>devDigital</b>.', '2025-08-24 18:39:46', 'semua', NULL);

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

--
-- Dumping data untuk tabel `pengumuman_viewed`
--

INSERT INTO `pengumuman_viewed` (`id`, `id_pengumuman`, `id_user`, `id_siswa`, `tanggal_dibaca`) VALUES
(184, 28, 40, 8, '2025-08-09 17:15:09'),
(185, 29, 35, NULL, '2025-08-11 18:20:59'),
(187, 29, 40, 8, '2025-08-11 18:24:08'),
(188, 29, 38, 7, '2025-08-11 18:24:13'),
(194, 31, 35, NULL, '2025-08-18 21:14:51'),
(216, 39, 35, NULL, '2025-08-24 18:39:51'),
(219, 31, 40, 8, '2025-08-24 18:39:55'),
(220, 39, 40, 8, '2025-08-24 18:39:55'),
(222, 30, 38, 7, '2025-08-26 14:50:02'),
(223, 31, 38, 7, '2025-08-26 14:50:02'),
(224, 39, 38, 7, '2025-08-26 14:50:02');

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
(2, 'PT Teknologi Indonesia', 'perusahaan@gmail.com', '0929-4935-7487', 'Jalan Cengger Ayam 1, Tulusrejo', -7.9459049, 112.6303017, 'Teknologi &amp; Marketing', '681d9a91e5865.jpg'),
(6, 'Indomaret', 'perusahaan2@gmail.com', '0843-6398-5673', 'Jalan Pendidikan, Pangkah Kulon', -6.9172202, 112.5544697, 'Marketing', '681c93f322ab6.png');

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
(35, 'A001', 'admin', '0192023a7bbd73250516f069df18b500', 'admin'),
(38, 'S001', 'daffa', '7b1e852330575c92c8d918377b30726a', 'alumni'),
(40, 'S002', 'Yazid', '837ae4833bde0dc2f5825bbdf0bd646b', 'alumni'),
(42, 'S003', 'Sauqi', '25d55ad283aa400af464c76d713c07ad', 'alumni'),
(74, 'S004', 'awik', '472dd949c980256c6359a5df33743973', 'alumni');

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
  MODIFY `id_alumni` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  MODIFY `id_lamaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `log_login`
--
ALTER TABLE `log_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id_lowongan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT untuk tabel `online_users`
--
ALTER TABLE `online_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `pengumuman_viewed`
--
ALTER TABLE `pengumuman_viewed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id_perusahaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

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
