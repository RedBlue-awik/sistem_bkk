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
$total_T_online = $row_online['total_online'];

function formatMax99($number)
{
    return $number > 99 ? '99+' : $number;
}

$total_T_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE level = 'alumni'"))['total'];
$total_T_admins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE level = 'admin'"))['total'];
$total_T_perusahaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM perusahaan"))['total'];
$total_T_loker = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lowongan"))['total'];
$total_T_pelamar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lamaran"))['total'];
$total_T_history = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lowongan WHERE tanggal_ditutup < DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY lowongan.tanggal_ditutup DESC"))['total'];
$total_T_sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM backup_db"))['total'];

$total_online = formatMax99($total_T_online);

$total_users = formatMax99($total_T_users);
$total_admins = formatMax99($total_T_admins);
$total_perusahaan = formatMax99($total_T_perusahaan);
$total_loker = formatMax99($total_T_loker);
$total_pelamar = formatMax99($total_T_pelamar);
$total_history = formatMax99($total_T_history);
$total_sql = formatMax99($total_T_sql);

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
    LIMIT 20
");

// Query untuk mendapatkan kategori loker paling populer (berdasarkan jumlah lamaran)
$kategori_popular = mysqli_query($conn, "
SELECT * FROM (
    SELECT l.*, p.bidang_usaha, COUNT(lam.id_lamaran) as jumlah_lamaran,
    ROW_NUMBER() OVER (PARTITION BY p.bidang_usaha ORDER BY COUNT(lam.id_lamaran) DESC) as rn
    FROM lowongan l
    LEFT JOIN lamaran lam ON l.id_lowongan = lam.id_lowongan
    JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan
    WHERE l.tanggal_dibuka <= CURDATE() AND l.tanggal_ditutup >= CURDATE()
    GROUP BY l.id_lowongan
) as ranked
WHERE rn = 1
ORDER BY jumlah_lamaran DESC
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
<!--begin::Head-->

<?php
$title = "Dashboard";
include '../../src/template/headers.php';
?>
<style>
    .app-footer {
        border-top: 4px solid rgba(14, 91, 158, .8);
        color: #fff;
        padding: 1rem 0;
    }

    .footer-title {
        font-weight: 600;
        position: relative;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        color: #fff;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background-color: white;
    }

    .social-icons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        margin-right: 8px;
        background-color: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .social-icons a:hover {
        background-color: white !important;
        transform: translateY(-3px);
    }

    .contact-info {
        display: flex;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .contact-info i {
        color: white;
        margin-right: 10px;
        margin-top: 4px;
        width: 16px;
    }

    .logo-section {
        background-color: rgba(14, 91, 158, .3);
        border-radius: 0.75rem;
        padding: 2rem;
        margin-top: 2rem;
        text-align: center;
    }

    .logo-section img {
        max-height: 150px;
        margin-bottom: 1.5rem;
    }

    .mitra-card {
        background-color: rgb(255, 255, 255);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        filter: saturate(180%);
        border: 1px solid rgba(225, 225, 225, 0.3);
        border-radius: 0.5rem;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        transition: box-shadow 0.3s ease;
    }

    .mitra-card img {
        max-height: 100px;
    }

    .copyright {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1.5rem;
        margin-top: 2rem;
        color: #adb5bd;
    }

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
    }

    @media (max-width: 1000px) {
        .logo1 {
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
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .kategori-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 0.5rem;
        height: 100%;
        cursor: pointer;
    }

    .kategori-card:hover {
        transform: translateY(-2px);
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

    /* Responsive Footer Styles */
    @media (max-width: 991.98px) {
        .app-footer {
            padding: 1.5rem 0;
        }

        .footer-title {
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .contact-info {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .logo-section {
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .logo-section img {
            max-height: 100px;
        }

        .logo-section h5 {
            font-size: 1.1rem;
        }

        .mitra-card img {
            max-height: 120px;
        }

        .copyright {
            padding-top: 1rem;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 767.98px) {
        .app-footer {
            padding: 1.5rem 0;
        }

        .footer-title {
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .contact-info {
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
        }

        .social-icons a {
            width: 32px;
            height: 32px;
            margin-right: 6px;
        }

        .logo-section {
            padding: 1.25rem;
            margin-top: 1rem;
        }

        .logo-section img {
            max-height: 90px;
            margin-bottom: 1rem;
        }

        .logo-section h5 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .mitra-card {
            padding: 0.75rem;
        }

        .mitra-card img {
            max-height: 110px;
        }

        .copyright {
            padding-top: 0.75rem;
            margin-top: 1rem;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 575.98px) {
        .app-footer {
            padding: 1rem 0;
        }

        .footer-title {
            margin-bottom: 0.4rem;
            font-size: 0.95rem;
        }

        .contact-info {
            margin-bottom: 0.3rem;
            font-size: 0.85rem;
        }

        .social-icons a {
            width: 30px;
            height: 30px;
            margin-right: 5px;
        }

        .logo-section {
            padding: 1rem;
            margin-top: 0.75rem;
        }

        .logo-section img {
            max-height: 80px;
            margin-bottom: 0.75rem;
        }

        .logo-section h5 {
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
        }

        .mitra-card {
            padding: 0.5rem;
        }

        .mitra-card img {
            max-height: 90px;
        }

        .copyright {
            padding-top: 0.5rem;
            margin-top: 0.75rem;
            font-size: 0.8rem;
        }
    }

    .card-data {
        cursor: pointer;
        transition: all .5s ease;
    }

    .card-data:hover {
        transform: translateY(-2%);
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
        <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'alumni') : ?>
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

                        <div class="col-12" style="margin-top: 2rem;">
                            <form id="searchForm" method="POST" action="../../pages/public/loker.php" class="w-100 d-flex justify-content-center">
                                <input type="search" id="searchLoker" name="search" class="form-control search-clearable" style="max-width:50rem;" placeholder="Cari loker, perusahaan, bidang-usaha, dll" autocomplete="off">
                                <button type="submit" class="btn text-light ms-2" style="background: #072757;">Cari</button>
                            </form>
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
                                                    <a data-bs-target="#modalSyarat<?= $loker['id_lowongan']; ?>"  data-bs-toggle="modal"
                                                        class="btn btn-sm btn-outline-primary w-100">Lamar</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle me-2"></i> Belum ada lowongan kerja yang tersedia
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-md-6 col-lg-4">
                                    <div data-id="all" class="card card-click-all bg-secondary-subtle loker-card h-100 shadow-sm">
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

                        <!-- Kategori Loker Paling Populer Section -->
                        <div class="fw-semibold text-center fs-4 p-3 bg-info bg-opacity-10 border border-info border-start-0 border-end-0" style="margin-top: 3.5rem;">
                            <span>Kategori Paling Populer</span>
                        </div>

                        <div class="container mt-4 mb-5">
                            <div class="row g-3">
                                <?php if (mysqli_num_rows($kategori_popular) > 0): ?>
                                    <?php while ($kategori = mysqli_fetch_assoc($kategori_popular)): ?>
                                        <div class="col">
                                            <a href="#" onclick="return setKategoriFilter('<?= htmlspecialchars($kategori['bidang_usaha']) ?>')" class="text-decoration-none">
                                                <div data-bidang="<?= $kategori['id_lowongan'] ?>" class="card card-click kategori-card h-100 shadow-sm">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <h4 class="text-nowarp text-dark">
                                                                <?= $kategori['bidang_usaha'] ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle me-2"></i> Belum ada Kategori yang tersedia
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::App Content-->
            </main>

        <?php elseif (isset($_SESSION['level']) && $_SESSION['level'] == 'admin') : ?>
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
                            <div class="col-6 col-sm-3 col-lg-3">
                                <div class="card shadow-sm border-0 text-center card-data" onclick="window.location.href='../../pages/public/data-siswa.php'">
                                    <div class="card-body">
                                        <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#4e73df;">
                                            <i class="fas fa-users text-white fa-lg"></i>
                                        </div>
                                        <p class="text-muted mb-1">Users</p>
                                        <h4 class="fw-bold"><?= $total_users; ?></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div class="card shadow-sm border-0 text-center card-data" onclick="window.location.href='../../pages/public/data-admin.php'">
                                    <div class="card-body">
                                        <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#1cc88a;">
                                            <i class="fas fa-user-shield text-white fa-lg"></i>
                                        </div>
                                        <p class="text-muted mb-1">Admin</p>
                                        <h4 class="fw-bold"><?= $total_admins; ?></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div class="card shadow-sm border-0 text-center card-data" onclick="window.location.href='../../pages/public/data-perusahaan.php'">
                                    <div class="card-body">
                                        <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#36b9cc;">
                                            <i class="fas fa-building text-white fa-lg"></i>
                                        </div>
                                        <p class="text-muted mb-1">Perusahaan</p>
                                        <h4 class="fw-bold"><?= $total_perusahaan; ?></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div class="card shadow-sm border-0 text-center card-data" onclick="window.location.href='../../pages/public/loker.php'">
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
                            <div class="col-lg-6 col-md-6 text-center ">
                                <div class="card p-3 flex-column align-items-center justify-content-center">
                                    <img src="../../src/assets/img/logoBKK.png" alt="Logo SMK" class="logo1 mb-n4 mt-n4">
                                    <h2 class="fw-bold text-muted sm: fs-4">SMK MAMBA'UL IHSAN</h2>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
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

                        <div class="row d-flex justify-content-between mt-4">
                            <div class="col-6 col-sm-3 col-lg-3">
                                <div class="card shadow-sm border-0 text-center card-data" onclick="window.location.href='../../pages/public/lamaran.php'">
                                    <div class="card-body">
                                        <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#4e73df;">
                                            <i class="fas fa-users text-white fa-lg"></i>
                                        </div>
                                        <p class="text-muted mb-1">Pelamar</p>
                                        <h4 class="fw-bold"><?= $total_pelamar; ?></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="card shadow-sm border-0 text-center">
                                    <div class="card-body">
                                        <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-2 bg-secondary" style="width:80px; height:60px;">
                                            <i class="fas fa-database text-white fa-lg"></i>
                                        </div>
                                        <h4 class="fw-bold"><?= $total_sql; ?></h4>
                                        <p class="text-muted mb-1">Backup Database</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-secondary btn-sm" id="backupButton">Backup</button>
                                            <a href="./daftar_backup.php" class="btn btn-info btn-sm">Lihat Daftar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-3 col-lg-3">
                                <div class="card shadow-sm border-0 text-center card-data" onclick="window.location.href='../../pages/public/history-loker.php'">
                                    <div class="card-body">
                                        <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#f6c23e;">
                                            <i class="fas fa-briefcase text-white fa-lg"></i>
                                        </div>
                                        <p class="text-muted mb-1">History</p>
                                        <h4 class="fw-bold"><?= $total_history; ?></h4>
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
        <?php include '../../src/template/app-footer.php'; ?>
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
                if (e.target.closest('a')) return;
                window.location.href = `./loker.php`;
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchLoker');
            const searchButton = document.querySelector('button[type="button"]');

            // Event listener untuk tombol cari
            searchButton.addEventListener('click', function() {
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    // Arahkan ke halaman loker dengan parameter pencarian
                    window.location.href = './loker.php?search=' + encodeURIComponent(searchTerm);
                } else {
                    // Jika kosong, arahkan ke halaman index tanpa parameter
                    window.location.href = 'index.php';
                }
            });

            // Event listener untuk tombol Enter di input pencarian
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchButton.click();
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchLoker');

            searchForm.addEventListener('submit', function(e) {
                const searchTerm = searchInput.value.trim();

                // Cegah submit jika input kosong
                if (!searchTerm) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
    <script>
        function setKategoriFilter(kategori) {
            console.log('Setting kategori filter:', kategori);

            // Simpan kategori ke session menggunakan AJAX
            $.ajax({
                url: '../../src/config/set_kategori.php',
                type: 'POST',
                data: {
                    kategori: kategori
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Response from server:', response);
                    if (response.success) {
                        // Redirect ke halaman loker
                        window.location.href = './loker.php';
                    } else {
                        console.error('Gagal menyimpan kategori:', response.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menyaring kategori: ' + response.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error setting kategori filter:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyaring kategori',
                    });
                }
            });

            // Mencegah default behavior link
            return false;
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangani tombol backup
            document.getElementById('backupButton').addEventListener('click', function() {
                // Generate nama file otomatis
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const filename = `BKK-Tahun(${year}).sql`;

                // Konfirmasi sebelum backup
                Swal.fire({
                    title: 'Konfirmasi Backup',
                    text: `Apakah Anda yakin ingin melakukan backup database`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, backup sekarang!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan loading
                        Swal.fire({
                            title: 'Memproses Backup',
                            text: 'Mohon tunggu, proses backup sedang berjalan...',
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Kirim request ke server untuk backup
                        fetch(`../../src/config/backup_database.php?filename=${encodeURIComponent(filename)}`, {
                                method: 'GET'
                            })
                            .then(response => {
                                // Tutup loading
                                Swal.close();

                                // Tampilkan notifikasi sukses
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Backup Berhasil',
                                    text: 'Database berhasil di backup.',
                                    timer: 3000,
                                    showConfirmButton: true
                                });

                                // Refresh halaman untuk menampilkan backup terbaru di daftar
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Backup Gagal',
                                    text: 'Terjadi kesalahan saat melakukan backup database.',
                                });
                            });
                    }
                });
            });
            // Cek jika ada parameter backup=success di URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('backup') === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Backup Berhasil',
                    text: 'Database berhasil di backup.',
                    timer: 3000,
                    showConfirmButton: true
                });

                // Hapus parameter dari URL
                window.history.href = herf;
            }
        });
    </script>

    <!--end::Script-->
</body>
<!--end::Body-->

</html>