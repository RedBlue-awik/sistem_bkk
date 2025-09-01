<?php
// Syarat untuk menggunakan session
session_start();

require './src/functions.php';

// Cek apakah sudah ada session login, jika sudah kembalikan
if (isset($_SESSION['id_pengguna'])) {
    echo "
        <script>
            document.location.href = './pages/public/index.php';
        </script>
    ";
}

include './src/controller/lupapw.php';

// Gabungkan logika login dari pages/view/index.php ke sini
if (isset($_POST["login"])) {
    $username = htmlspecialchars($_POST['username']);
    $password = md5(htmlspecialchars($_POST['password']));

    // Cek di tabel admin
    $tabel_admin = "SELECT * FROM user u
        INNER JOIN admin a ON a.`kode_admin`=u.`kode_pengguna`
        WHERE username='" . $username . "' AND password='" . $password . "' LIMIT 1";
    $cek_tabel_admin = mysqli_query($conn, $tabel_admin);
    $admin = mysqli_num_rows($cek_tabel_admin);

    // Cek di tabel alumni
    $tabel_alumni = "SELECT * FROM user u
        INNER JOIN alumni s ON s.`kode_alumni`=u.`kode_pengguna`
        WHERE username='" . $username . "' AND password='" . $password . "' LIMIT 1";
    $cek_tabel_alumni = mysqli_query($conn, $tabel_alumni);
    $alumni = mysqli_num_rows($cek_tabel_alumni);

    if ($admin > 0) {
        $row = mysqli_fetch_assoc($cek_tabel_admin);
        $_SESSION["id_pengguna"] = $row["id_user"];
        $_SESSION["kode_pengguna"] = $row["kode_pengguna"];
        $_SESSION["nama"] = $row["nama"];
        $_SESSION["username"] = $row["username"];
        $_SESSION["level"] = $row["level"];
        $_SESSION["gambar"] = "";
        $id_user = $_SESSION['id_pengguna'];
        mysqli_query($conn, "INSERT INTO log_login (id_user, waktu_login, ip_address, user_agent) 
                        VALUES ('$id_user', NOW(), '{$_SERVER['REMOTE_ADDR']}', '{$_SERVER['HTTP_USER_AGENT']}')");
        mysqli_query($conn, "INSERT INTO online_users (id_user, last_activity)
                        VALUES ('$id_user', NOW())
                            ON DUPLICATE KEY UPDATE last_activity = NOW()");
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Welcome!",
                    text: "Selamat Datang ' . $_SESSION["nama"] . ', Kamu Berhasil Login!",
                    icon: "success",
                }).then(function() {
                    window.location.href = "./pages/public/index.php";
                });
            });
        </script>';
        exit();
    } else if ($alumni > 0) {
        $row = mysqli_fetch_assoc($cek_tabel_alumni);
        $_SESSION["id_pengguna"] = $row["id_user"];
        $_SESSION["kode_pengguna"] = $row["kode_pengguna"];
        $_SESSION["nama"] = $row["nama"];
        $_SESSION["username"] = $row["username"];
        $_SESSION["level"] = $row["level"];
        $_SESSION["gambar"] = "";
        $id_user = $_SESSION['id_pengguna'];
        mysqli_query($conn, "INSERT INTO log_login (id_user, waktu_login, ip_address, user_agent) 
                        VALUES ('$id_user', NOW(), '{$_SERVER['REMOTE_ADDR']}', '{$_SERVER['HTTP_USER_AGENT']}')");
        mysqli_query($conn, "INSERT INTO online_users (id_user, last_activity)
                        VALUES ('$id_user', NOW())
                            ON DUPLICATE KEY UPDATE last_activity = NOW()");
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Welcome!",
                    text: "Selamat Datang ' . $_SESSION["nama"] . ', Kamu Berhasil Login!",
                    icon: "success",
                }).then(function() {
                    window.location.href = "./pages/public/index.php";
                });
            });
        </script>';
        exit();
    } else {
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: "Username Atau Password Salah!",
                            confirmButtonText: "OK"
                        }).then(function() {
                            window.location.href = "index.php";
                        });
                    });
                </script>';
        exit();
    }
}

$tahun_sekarang = date('Y');

$sql = "
    SELECT MONTHNAME(waktu_login) AS bulan, COUNT(DISTINCT id_user) AS jumlah
    FROM log_login
    WHERE YEAR(waktu_login) = '$tahun_sekarang'
    GROUP BY MONTH(waktu_login)
    ORDER BY MONTH(waktu_login)
";
$result = mysqli_query($conn, $sql);

$tahun_sekarang = date('Y');

$sql = "
    SELECT MONTHNAME(waktu_login) AS bulan, COUNT(DISTINCT id_user) AS jumlah
    FROM log_login
    WHERE YEAR(waktu_login) = '$tahun_sekarang'
    GROUP BY MONTH(waktu_login)
    ORDER BY MONTH(waktu_login)
";
$result = mysqli_query($conn, $sql);

$daftar_bulan = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
];

// Default semua bulan 0
$jumlah_final = array_fill_keys($daftar_bulan, 0);

while ($row = mysqli_fetch_assoc($result)) {
    $jumlah_final[$row['bulan']] = (int)$row['jumlah'];
}

$bulan_labels = array_keys($jumlah_final);
$jumlah_data  = array_values($jumlah_final);

mysqli_query($conn, "DELETE FROM log_login WHERE waktu_login < NOW() - INTERVAL 1 YEAR");

// Hitung yang aktif dalam 60 menit terakhir
$sql_online = mysqli_query($conn, "
    SELECT COUNT(*) AS total_online
    FROM online_users
    WHERE last_activity >= NOW() - INTERVAL 60 MINUTE
");

$row_online = mysqli_fetch_assoc($sql_online);
$total_online = $row_online['total_online'];

$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE level = 'alumni'"))['total'];
$total_admins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE level = 'admin'"))['total'];
$total_perusahaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM perusahaan"))['total'];
$total_loker = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lowongan"))['total'];

// Query untuk mendapatkan loker paling populer (berdasarkan jumlah lamaran)
$query_popular = mysqli_query($conn, "
    SELECT l.*, p.nama_perusahaan, p.logo, COUNT(lam.id_lamaran) as jumlah_lamaran
    FROM lowongan l
    LEFT JOIN lamaran lam ON l.id_lowongan = lam.id_lowongan
    JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan
    WHERE l.tanggal_dibuka <= CURDATE() AND l.tanggal_ditutup >= CURDATE()
    GROUP BY l.id_lowongan
    HAVING jumlah_lamaran > 0
    ORDER BY jumlah_lamaran DESC, l.tanggal_dibuka DESC
    LIMIT 8
");
// Panggil lamaran
$query = mysqli_query($conn, "
    SELECT p.nama_perusahaan, COUNT(*) AS total
    FROM lamaran lam
    JOIN lowongan l ON lam.id_lowongan = l.id_lowongan
    JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan
    GROUP BY p.id_perusahaan
");
$labels = [];
$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $labels[] = $row['nama_perusahaan'];
    $data[] = (int)$row['total'];
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>BKK | SMK MI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="./src/assets/img/favicon.png">
    <meta name="title" content="BKK | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS." />
    <meta name="keywords" content="bootstrap 5, bootstrap, admin dashboard, charts, datatable, colorlibhq" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="./src/fontawesome-free-6.7.2-web/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./src/assets/css/adminlte.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        .swal2-popup {
            font-family: "Poppins", sans-serif;
        }

        .p-b {
            padding: 3px;
        }

        .notification-badge {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 10px;
            font-size: 10px;
            height: 13px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .dashboard-row {
            min-height: 240px;
        }

        .dashboard-card {
            min-height: 240px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border-radius: 1rem;
        }

        .logo-wrapper {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }

        .logo-wrapper img {
            max-width: 100%;
            max-height: 100%;
        }

        .logo-wrapper.big img {
            max-width: 145px;
            max-height: 145px;
        }

        .logo1 {
            width: 350px;
            width: 350px;
        }

        @media (max-width: 1000px) {
            .logo1 {
                width: 200px;
                width: 200px;
            }

            .logo-wrapper.big img {
                max-width: 135px !important;
                max-height: 135px !important;
            }
        }

        @media (max-width: 991.98px) {

            .dashboard-row,
            .dashboard-card {
                min-height: 180px;
            }

            .logo-wrapper.big img {
                max-width: 120px !important;
                max-height: 120px !important;
            }
        }

        @media (max-width: 767.98px) {

            .dashboard-row,
            .dashboard-card {
                min-height: 140px;
            }

            .logo-wrapper.big img {
                max-width: 120px !important;
                max-height: 120px !important;
            }
        }

        @media (max-width: 680px) {
            .logo-wrapper.big img {
                max-width: 110px !important;
                max-height: 110px !important;
            }
        }

        @media (max-width: 575.98px) {

            .dashboard-row,
            .dashboard-card {
                min-height: 100px;
            }

            .logo-wrapper.big img {
                max-width: 125px !important;
                max-height: 125px !important;
            }

            .dashboard-card img {
                max-width: 250px !important;
                max-height: 250px !important;
            }
        }


        .loker-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 0.75rem;
            height: 100%;
            cursor: pointer;
        }

        .loker-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .loker-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 5px;
        }

        .loker-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .loker-company {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .loker-desc {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 0.9rem;
            color: #495057;
        }

        .loker-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .loker-date {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar sticky-top navbar-expand bg-primary-subtle shadow" data-bs-theme="dark">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list" style="color: white;"></i>
                        </a>
                    </li>
                </ul>
                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">

                    <!--begin::User Menu Dropdown-->
                    <button data-bs-toggle="modal" data-bs-target="#Modaldaftar" class="btn btn-outline-light ps-2 fw-medium d-flex align-items-center justify-content-center text-center" style="height: 30px; font-size: 13px;"><i class="fa-solid fa-pen-to-square me-2"></i>Daftar</button>
                    <button data-bs-toggle="modal" data-bs-target="#Modallogin" class="btn btn-outline-light ps-2 mx-2 fw-medium d-flex align-items-center justify-content-center text-center" style="height: 30px; font-size: 13px;"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</button>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
            </div>
            <!--end::Container-->
        </nav>
        <!--end::Header-->
        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-primary-subtle shadow" data-bs-theme="dark">
            <div class="sidebar-brand d-flex justify-content-start">
                <a href="index.php" class="brand-link ms-2">
                    <img src="./src/assets/img/logo.png" alt="SMK MI Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-bold ">SMK MI</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link "><i class="bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./pages/public/chart.php" class="nav-link"><i class="fa-solid fa-chart-line"></i>
                                <p>Statistic</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./pages/public/loker.php" class="nav-link"><i class="bi bi-briefcase-fill"></i>
                                <p>Lowongan Kerja</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./pages/public/pengumuman-all.php" class="nav-link mt-2 d-flex align-items-center">
                                <i class="bi bi-bell-fill"></i>
                                <p class="p-b">Pengumuman <span class="badgePengumuman badge bg-danger float-end d-none">0</span></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="modal" data-bs-target="#Modaldaftar" class="nav-link mt-3 d-flex align-items-center" style="cursor: pointer;">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <p>Daftar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="modal" data-bs-target="#Modallogin" class="nav-link  mt-2 d-flex align-items-center" style="cursor: pointer;">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <p>Login</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!--end::Sidebar-->
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content-->
            <div class="container-fluid">
                <div class="text-center">
                    <h1 class="fw-bold text-muted mt-4">APLIKASI BURSA KERJA KHUSUS</h1>
                </div>
                <div class="row g-3 mt-4 align-items-stretch dashboard-row">
                    <div class="col-12 col-md-6 col-lg-6 d-flex">
                        <div class="card shadow-sm border-0 text-center d-flex justify-content-center align-items-center flex-fill dashboard-card rounded-4 py-4">
                            <div class="w-100 d-flex flex-column align-items-center justify-content-center">
                                <img src="./src/assets/img/logoBKK.png" alt="Logo BKK" class="img-fluid mb-3" style="width: 310px; max-width: 100%; display: block; margin-left: auto; margin-right: auto;">
                                <div class="fw-bold text-muted fs-5">SMK MAMBA'UL IHSAN</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 d-flex">
                        <div class="card shadow-sm border-0 text-center p-4 flex-fill dashboard-card rounded-4 h-100">
                            <div class="row h-100">
                                <div class="col-6 d-flex align-items-center justify-content-center">
                                    <div class="bg-primary-subtle rounded-3 p-3 w-100 mx-1">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-2 mx-auto" style="width:38px;height:38px;">
                                            <i class="fas fa-users text-white"></i>
                                        </div>
                                        <div class="fw-bold fs-5"><?= $total_users; ?></div>
                                        <div class="text-secondary">Users</div>
                                    </div>
                                </div>
                                <div class="col-6 d-flex align-items-center justify-content-center">
                                    <div class="bg-success-subtle rounded-3 p-3 w-100 mx-1">
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center mb-2 mx-auto" style="width:38px;height:38px;">
                                            <i class="fas fa-user-shield text-white"></i>
                                        </div>
                                        <div class="fw-bold fs-5"><?= $total_admins; ?></div>
                                        <div class="text-secondary">Admin</div>
                                    </div>
                                </div>
                                <div class="col-6 d-flex align-items-center justify-content-center mt-3">
                                    <div class="bg-info-subtle rounded-3 p-3 w-100 mx-1">
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center mb-2 mx-auto" style="width:38px;height:38px;">
                                            <i class="fas fa-building text-white"></i>
                                        </div>
                                        <div class="fw-bold fs-5"><?= $total_perusahaan; ?></div>
                                        <div class="text-secondary">Perusahaan</div>
                                    </div>
                                </div>
                                <div class="col-6 d-flex align-items-center justify-content-center mt-3">
                                    <div class="bg-warning-subtle rounded-3 p-3 w-100 mx-1">
                                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center mb-2 mx-auto" style="width:38px;height:38px;">
                                            <i class="fas fa-briefcase text-white"></i>
                                        </div>
                                        <div class="fw-bold fs-5"><?= $total_loker; ?></div>
                                        <div class="text-secondary">Loker</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Loker Paling Populer Section -->
                    <div class="fw-semibold text-center fs-4 p-3 bg-info bg-opacity-10 border border-info border-start-0 border-end-0 mt-4">
                        <span>Loker Paling Populer</span>
                    </div>
                    <div class="container mt-4">
                        <div class="row g-4">
                            <?php if (mysqli_num_rows($query_popular) > 0): ?>
                                <?php while ($loker = mysqli_fetch_assoc($query_popular)): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div data-id="<?= $loker['id_lowongan'] ?>" class="card card-click loker-card h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <img src="./src/assets/img/perusahaan/logo/<?= $loker['logo'] ?? 'default.png' ?>"
                                                        alt="<?= $loker['nama_perusahaan'] ?>"
                                                        class="loker-img rounded me-3">
                                                    <div>
                                                        <h6 class="loker-title mb-0"><?= $loker['judul'] ?></h6>
                                                        <div class="loker-company"><?= $loker['nama_perusahaan'] ?></div>
                                                    </div>
                                                </div>
                                                <p class="loker-desc"><?= $loker['deskripsi'] ?></p>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <span class="badge bg-primary loker-badge"><?= $loker['jumlah_lamaran'] ?> Pelamaran</span>
                                                    <small class="loker-date"><?= date('d M Y', strtotime($loker['tanggal_dibuka'])) ?></small>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <a data-bs-target="#Modallogin" data-bs-toggle="modal"
                                                    class="btn btn-sm btn-outline-primary w-100">Lamar</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle me-2"></i> Belum ada lowongan kerja tersedia
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-6 col-lg-4">
                                <div data-id="<?= $loker['id_lowongan'] ?>" class="card card-click-all bg-secondary-subtle loker-card h-100 shadow-sm">
                                    <div class="card-body d-flex flex-column justify-content-center">
                                        <div class="d-flex flex-column align-items-center mb-3">
                                            <span class="mb-1" style="font-size: 90px;"><i class="bi bi-briefcase-fill"></i></span>
                                            <h5 class="">Loker Lainnya <i class="fa-solid fa-arrow-right ms-1"></i></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-4">

                    <div class="fw-semibold text-center fs-4 p-3 bg-success-subtle bg-opacity-10 border border-success border-start-0 border-end-0 mt-4">
                        <span>Mitra SMK</span>
                    </div>

                    <div class="col-6 col-sm-3 col-lg-3">
                        <div class="card shadow-sm border-0 text-center logo-card h-100">
                            <div class="card-body">
                                <div class="logo-wrapper big">
                                    <img src="./src/assets/img/logo/vokasi-l.png" alt="Vokasi">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-3 col-lg-3">
                        <div class="card shadow-sm border-0 text-center logo-card h-100">
                            <div class="card-body">
                                <div class="logo-wrapper big">
                                    <img src="./src/assets/img/logo/smk-hebat.png" alt="SMK Hebat">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-3 col-lg-3">
                        <div class="card shadow-sm border-0 text-center logo-card">
                            <div class="card-body">
                                <div class="logo-wrapper">
                                    <img src="./src/assets/img/logo/smk_pk_logo.png" alt="SMK PK">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-3 col-lg-3">
                        <div class="card shadow-sm border-0 text-center logo-card">
                            <div class="card-body">
                                <div class="logo-wrapper">
                                    <img src="./src/assets/img/logo/dudi.png" alt="DUDI">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Online Card -->
                <div class="card card-round mt-4">
                    <div class="card-body">
                        <i class="fa-solid fa-users-between-lines position-absolute top-0 end-0 m-3 fs-3"></i>
                        <h2 class="mb-2"><?= $total_online ?></h2>
                        <p class="text-muted">Users online</p>
                        <div id="chart-container">
                            <canvas id="loginChart" style="height:260px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Row-->
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <footer class="app-footer">
            <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <!--end::To the end-->
            <!--begin::Copyright-->
            <strong>
                <?php
                $tahun_sekarang = date("Y");
                ?>
                Copyright &copy; <?= $tahun_sekarang; ?>&nbsp;
                <a href="" class="text-decoration-none">SMK MAMBA'UL IHSAN</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
        </footer>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!-- begin::Modal -->

    <?php include './src/template/modalForm.php'; ?>

    <!-- end::Modal -->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
        src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
        crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="./src/assets/js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!-- sortablejs -->
    <script
        src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
        integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ="
        crossorigin="anonymous"></script>
    <!-- sortablejs -->
    <script>
        const connectedSortables = document.querySelectorAll('.connectedSortable');
        connectedSortables.forEach((connectedSortable) => {
            let sortable = new Sortable(connectedSortable, {
                group: 'shared',
                handle: '.card-header',
            });
        });

        const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
        cardHeaders.forEach((cardHeader) => {
            cardHeader.style.cursor = 'move';
        });
    </script>
    <!-- apexcharts -->
    <script
        src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
        integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
        crossorigin="anonymous"></script>
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart Login Alumni
        const loginData = {
            labels: <?= json_encode($bulan_labels); ?>,
            datasets: [{
                label: 'Jumlah Alumni Login',
                data: <?= json_encode($jumlah_data); ?>,
                data: <?= json_encode($jumlah_data); ?>,
                borderColor: 'rgba(23, 125, 255, 1)',
                backgroundColor: 'rgba(23, 125, 255, 0.2)',
                pointBackgroundColor: 'rgba(23, 125, 255, 1)',
                pointRadius: 5,
                pointBackgroundColor: 'rgba(23, 125, 255, 1)',
                pointRadius: 5,
                fill: true,
                tension: 0.4
            }]
        };

        const loginConfig = {
            type: 'line',
            data: loginData,
            maintainAspectRatio: false,
            options: {
                responsive: true,
                responsive: true,
                animations: {
                    y: {
                        easing: 'easeInOutElastic',
                        from: (ctx) => {
                            if (ctx.type === 'data') {
                                if (ctx.mode === 'default' && !ctx.dropped) {
                                    ctx.dropped = true;
                                    return 0;
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        suggestedMin: 0,
                        suggestedMax: 10
                    }
                }
            }
        };

        new Chart(document.getElementById('loginChart'), loginConfig);
    </script>
    <script
        src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
        integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y="
        crossorigin="anonymous"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
        integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY="
        crossorigin="anonymous"></script>
    <!-- jsvectormap -->
    <script>
        const visitorsData = {
            US: 398, // USA
            SA: 400, // Saudi Arabia
            CA: 1000, // Canada
            DE: 500, // Germany
            FR: 760, // France
            CN: 300, // China
            AU: 700, // Australia
            BR: 600, // Brazil
            IN: 800, // India
            GB: 320, // Great Britain
            RU: 3000, // Russia
        };

        // World map by jsVectorMap (gunakan pengecekan agar tidak error)
        const worldMapEl = document.querySelector('#world-map');
        if (worldMapEl) {
            const map = new jsVectorMap({
                selector: '#world-map',
                map: 'world',
            });
        }
    </script>

    <!-- begin::SweetAlertKonfirmasi -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleksi semua tombol hapus
            const deleteButtons = document.querySelectorAll('.btn-hapus');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Mencegah langsung ke link

                    const adminId = this.dataset.id;
                    const href = this.getAttribute('href');

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data akan hilang permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect ke URL hapus
                            window.location.href = href;
                        }
                    });
                });
            });
        });
    </script>
    <!-- end::SweetAlertKonfirmasi -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // SweetAlert untuk button logout
        // Ambil semua elemen dengan class btn-logout
        document.querySelectorAll('.btn-logout').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah tautan langsung
                const href = this.getAttribute('href'); // Ambil tautan href

                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: "Apakah Anda yakin ingin logout?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Arahkan ke tautan jika dikonfirmasi
                        window.location.href = href;
                    }
                });
            });
        });
    </script>

    <script src="./src/assets/js/jquery-3.7.1.min.js"></script>
    <!-- jQuery (paling atas) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Badge -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk update badge
            function updateBadge() {
                fetch('./src/config/proses-pengumuman.php?t=' + Date.now(), {
                        cache: "no-store"
                    })
                    .then(response => response.json())
                    .then(function(data) { // perbaiki: tambahkan function di sini
                        const badges = document.querySelectorAll('.badgePengumuman');
                        badges.forEach(badge => {
                            if (data.jumlah > 0) {
                                badge.classList.remove('d-none');
                                badge.textContent = data.jumlah;
                                badge.classList.add('notification-badge');
                            } else {
                                badge.classList.add('d-none');
                                badge.textContent = '0';
                                badge.classList.remove('notification-badge');
                            }
                        });
                    });
            }

            // Update badge setiap 30 detik
            updateBadge();
            setInterval(updateBadge, 30000);

            // Tangani klik pada semua link pengumuman
            document.querySelectorAll('a[href*="pengumuman-all.php"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Kirim request untuk menandai sudah dilihat
                    fetch('./src/config/proses-pengumuman.php?viewed=true&t=' + Date.now())
                        .then(() => updateBadge());

                    // Jika ini link di sidebar, biarkan navigasi tetap berjalan
                    if (this.closest('.app-sidebar')) {
                        return true;
                    }

                    // Jika di tempat lain, bisa ditambahkan logika khusus
                    e.preventDefault();
                    window.location.href = this.href;
                });
            });
        });
    </script>
    <!-- end::Badge -->

    <!--begin::Validation-->
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (() => {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
    <!--end::Validation-->

    <!-- Begin::Details -->
    <script>
        document.querySelectorAll('.card-click').forEach(card => {
            card.addEventListener('click', function(e) {
                // Cegah navigasi kalau klik tombol (yang ada <a> di dalamnya)
                if (e.target.closest('a')) return;

                const id = this.getAttribute('data-id');
                window.open(`./pages/public/detail_loker.php?id_lowongan=${id}`);
            });
        });
    </script>
    <!-- End::Details -->

    <script>
        document.querySelectorAll('.card-click-all').forEach(card => {
            card.addEventListener('click', function(e) {
                // Cegah navigasi kalau klik tombol (yang ada <a> di dalamnya)
                if (e.target.closest('a')) return;
                window.location.href = `./pages/public/loker.php`;
            });
        });
    </script>

</body>

</html>