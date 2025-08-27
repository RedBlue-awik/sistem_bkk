<?php
// Syarat untuk menggunakan session
session_start();

require '../../src/functions.php';

// Cek apakah sudah ada session login, jika sudah kembalikan
if (!isset($_SESSION['id_pengguna'])) {
    echo "
        <script>
            document.location.href = '../../index.php';
        </script>
    ";
}


function getLoker()
{
    global $conn;

    $query = "SELECT lowongan.*, perusahaan.nama_perusahaan, perusahaan.logo, perusahaan.alamat, perusahaan.bidang_usaha
              FROM lowongan 
              JOIN perusahaan ON lowongan.id_perusahaan = perusahaan.id_perusahaan 
              WHERE tanggal_ditutup >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
              ORDER BY lowongan.id_lowongan DESC";

    $result = mysqli_query($conn, $query);

    $loker = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Format gaji langsung
        $angka = str_replace(['.', ','], ['', '.'], $row['gaji']);
        $row['gaji_full'] = $row['mata_uang'] . ' ' . formatUangSingkat($angka) . '/' . $row['kpn_gaji_diberi'];

        // Ubah persyaratan ke array
        if (is_string($row['persyaratan'])) {
            $row['persyaratan'] = explode(',', $row['persyaratan']);
        }

        $loker[] = $row;
    }

    return $loker;
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
    LIMIT 6
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
<!--begin::Head-->

<?php
$title = "Dashboard";
include '../../src/template/headers.php';
?>

<style>
    body {
        font-family: "Poppins", sans-serif;
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
        max-width: 150px;
        max-height: 150px;
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
    }

    @media (max-width: 991.98px) {

        .dashboard-row,
        .dashboard-card {
            min-height: 180px;
        }
    }

    @media (max-width: 767.98px) {

        .dashboard-row,
        .dashboard-card {
            min-height: 140px;
        }
    }

    @media (max-width: 575.98px) {

        .dashboard-row,
        .dashboard-card {
            min-height: 100px;
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
<!--end::Head-->
<!--begin::Body-->



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
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img
                                src="../../src/assets/img/logo.png"
                                class="user-image rounded-circle shadow"
                                alt="User Image" />
                            <span class="d-none d-md-inline"><?= $_SESSION["nama"]; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <!--begin::User Image-->
                            <li class="user-header bg-secondary-subtle">
                                <?php if ($_SESSION["gambar"] !== "") : ?>
                                    <img
                                        src="../../dist/assets/img/user2-160x160.jpg"
                                        class="rounded-circle shadow"
                                        alt="User Image" />
                                <?php endif; ?>
                                <p class="fw-semibold text-light">Nama</p>
                                <span class="badge bg-warning-subtle p-2 fs-5 px-3 mb-1"><?= $_SESSION["nama"]; ?></span>
                                <p class="fw-semibold text-light">Status</p>
                                <?php $kondisi = ($_SESSION["level"] == "admin") ? 'bg-info-subtle' : 'bg-success-subtle' ?>
                                <span class="badge <?= $kondisi ?> p-2 fs-6 px-3 mb-1"><?= $_SESSION["level"]; ?></span>
                            </li>
                            <!--end::User Image-->
                            <!--begin::Menu Footer-->
                            <li class="user-footer">
                                <a href="./pengumuman-all.php" class="btn btn-default btn-flat" data-bs-trigger="hover" data-bs-placement="right" data-bs-custom-class="custom-tooltip-Bell" data-bs-title="Pengumuman"><i class="bi bi-bell"></i><span class="badge bg-danger float-end d-none badgePengumuman">0</span></a>
                                <a href="../../logout.php" class="btn btn-default btn-flat float-end btn-logout" data-bs-trigger="hover" data-bs-placement="left" data-bs-custom-class="custom-tooltip-logout" data-bs-title="LogOut ( Keluar )"><i class="fas fa-arrow-right-from-bracket"></i></a>
                            </li>
                            <!--end::Menu Footer-->
                        </ul>
                    </li>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
            </div>
            <!--end::Container-->
        </nav>
        <!--end::Header-->
        <!--begin::Sidebar-->
        <?php include('../../src/template/menu.php'); ?>
        <!--end::Sidebar-->
        <!--begin::App Main-->
        <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'alumni') :?>
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
                                <img src="../../src/assets/img/logoBKK.png" alt="Logo BKK" class="img-fluid mb-3" style="width: 310px; max-width: 100%; display: block; margin-left: auto; margin-right: auto;">
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
                </div>
                <div class="row g-3 mt-4">

                    <div class="col-sm-6 col-lg-3">
                        <div class="card shadow-sm border-0 text-center logo-card h-100">
                            <div class="card-body">
                                <div class="logo-wrapper big">
                                    <img src="../../src/assets/img/logo/vokasi-l.png" alt="Vokasi">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card shadow-sm border-0 text-center logo-card h-100">
                            <div class="card-body">
                                <div class="logo-wrapper big">
                                    <img src="../../src/assets/img/logo/smk-hebat.png" alt="SMK Hebat">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card shadow-sm border-0 text-center logo-card">
                            <div class="card-body">
                                <div class="logo-wrapper">
                                    <img src="../../src/assets/img/logo/smk_pk_logo.png" alt="SMK PK">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card shadow-sm border-0 text-center logo-card">
                            <div class="card-body">
                                <div class="logo-wrapper">
                                    <img src="../../src/assets/img/logo/dudi.png" alt="DUDI">
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
                                                <img src="../../src/assets/img/perusahaan/logo/<?= $loker['logo'] ?? 'default.png' ?>"
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
                                            <a href="" data-bs-toggle="modal" data-bs-target="#modalSyarat<?= $loker['id_lowongan']; ?>"
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

        <?php elseif (isset($_SESSION['level']) && $_SESSION['level'] == 'admin') :?>
         <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Dashboard</h3>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <div class="text-center">
                        <h1 class="fw-bold text-muted mt-4 sm: fs-3">APLIKASI BURSA KERJA KHUSUS</h1>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card shadow-sm border-0 text-center">
                                <div class="card-body">
                                    <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#4e73df;">
                                        <i class="fas fa-users text-white fa-lg"></i>
                                    </div>
                                    <p class="text-muted mb-1">Users</p>
                                    <h4 class="fw-bold"><?= $total_users; ?></h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card shadow-sm border-0 text-center">
                                <div class="card-body">
                                    <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#1cc88a;">
                                        <i class="fas fa-user-shield text-white fa-lg"></i>
                                    </div>
                                    <p class="text-muted mb-1">Admin</p>
                                    <h4 class="fw-bold"><?= $total_admins; ?></h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card shadow-sm border-0 text-center">
                                <div class="card-body">
                                    <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#36b9cc;">
                                        <i class="fas fa-building text-white fa-lg"></i>
                                    </div>
                                    <p class="text-muted mb-1">Perusahaan</p>
                                    <h4 class="fw-bold"><?= $total_perusahaan; ?></h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card shadow-sm border-0 text-center">
                                <div class="card-body">
                                    <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#f6c23e;">
                                        <i class="fas fa-briefcase text-white fa-lg"></i>
                                    </div>
                                    <p class="text-muted mb-1">Loker</p>
                                    <h4 class="fw-bold"><?= $total_loker; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--begin::Row-->
                    <div class="row g-3 mt-3">
                        <div class="col-lg-6 col-md-auto text-center ">
                            <div class="card p-3 flex-column align-items-center justify-content-center">
                                <img src="../../src/assets/img/logoBKK.png" alt="Logo SMK" class="logo1 mb-n4 mt-n4">
                                <h2 class="fw-bold text-muted sm: fs-4">SMK MAMBA'UL IHSAN</h2>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-auto">
                            <div class="card p-3 flex-column align-items-center justify-content-center">
                                <div id="chart-container">
                                    <canvas id="lamaranChart"></canvas>
                                </div>
                                <div class="text-center mt-2">
                                    <button class="btn btn-primary btn-sm" onclick="toggleChart()">
                                        <i class="fa-solid fa-arrows-rotate"></i> Toggle Pie/Doughnut
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-4">

                        <div class="col-sm-6 col-lg-3">
                            <div class="card shadow-sm border-0 text-center logo-card h-100">
                                <div class="card-body">
                                    <div class="logo-wrapper big">
                                        <img src="../../src/assets/img/logo/vokasi-l.png" alt="Vokasi">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card shadow-sm border-0 text-center logo-card h-100">
                                <div class="card-body">
                                    <div class="logo-wrapper big">
                                        <img src="../../src/assets/img/logo/smk-hebat.png" alt="SMK Hebat">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card shadow-sm border-0 text-center logo-card">
                                <div class="card-body">
                                    <div class="logo-wrapper">
                                        <img src="../../src/assets/img/logo/smk_pk_logo.png" alt="SMK PK">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card shadow-sm border-0 text-center logo-card">
                                <div class="card-body">
                                    <div class="logo-wrapper">
                                        <img src="../../src/assets/img/logo/dudi.png" alt="DUDI">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-round mt-4">
                        <div class="card-body">
                            <i class="fa-solid fa-users-between-lines position-absolute top-0 end-0 m-3 fs-3"></i>
                            <h2 class="mb-2"><?= $total_online ?></h2>
                            <p class="text-muted">Users online</p>
                            <i class="fas fa-users-box"></i>
                            <div id="chart-container">
                                <canvas id="loginChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::App Content-->
                <!--end::App Content-->
            </div>
        </main>

        <?php endif; ?>
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
    <!-- Modal -->
    <?php
    $daftarLoker = getLoker();
    ?>

    <?php foreach ($daftarLoker as $loker) : ?>

        <!--begin::Modal Syarat -->
        <div class="modal fade" id="modalSyarat<?= $loker['id_lowongan']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <form action="../../src/config/proses-lamaran.php?id=<?= $loker['id_lowongan'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Lamar Lowongan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_lowongan" value="<?= $loker['id_lowongan']; ?>">
                            <input type="hidden" name="id_alumni" value="<?= $_SESSION['id_pengguna'] ?>">
                            <div class="mb-3">
                                <label for="cv">Upload CV</label>
                                <input type="file" name="cv" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="kirim" class="btn btn-primary">Kirim Lamaran</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End::Modal Syarat -->

    <?php endforeach; ?>
    <!--begin::Script-->

    <?php
    include '../../src/template/footer.php';
    ?>

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

        // Chart Doughnut/Pie Lamaran
        const lamaranLabels = <?= json_encode($labels) ?>;
        const lamaranData = <?= json_encode($data) ?>;
        const colors = lamaranLabels.map((_, i) => `hsl(${i * 40}, 70%, 60%)`);

        let lamaranChart = new Chart(document.getElementById('lamaranChart'), {
            type: 'doughnut',
            data: {
                labels: lamaranLabels,
                datasets: [{
                    data: lamaranData,
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        function toggleChart() {
            lamaranChart.destroy();
            lamaranChart = new Chart(document.getElementById('lamaranChart'), {
                type: lamaranChart.config.type === 'doughnut' ? 'pie' : 'doughnut',
                data: {
                    labels: lamaranLabels,
                    datasets: [{
                        data: lamaranData,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    </script>

    <!-- Begin::Details -->
    <script>
        document.querySelectorAll('.card-click').forEach(card => {
            card.addEventListener('click', function(e) {
                // Cegah navigasi kalau klik tombol (yang ada <a> di dalamnya)
                if (e.target.closest('a')) return;

                const id = this.getAttribute('data-id');
                window.open(`./detail_loker.php?id_lowongan=${id}`);
            });
        });
    </script>
    <!-- End::Details -->

    <script>
        document.querySelectorAll('.card-click-all').forEach(card => {
            card.addEventListener('click', function(e) {
                // Cegah navigasi kalau klik tombol (yang ada <a> di dalamnya)
                if (e.target.closest('a')) return;
                window.location.href = `./loker.php`;
            });
        });
    </script>

    <!--end::Script-->
</body>
<!--end::Body-->

</html>