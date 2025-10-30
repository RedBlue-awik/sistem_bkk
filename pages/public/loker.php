<?php
// Syarat untuk menggunakan session
session_start();
// Ambil filter dari session
$kategoriFilter = isset($_SESSION['kategori_filter']) ? $_SESSION['kategori_filter'] : '';
$filterBidang = isset($_SESSION['filter_bidang']) ? $_SESSION['filter_bidang'] : '';

// Hapus session setelah digunakan
if (isset($_SESSION['kategori_filter'])) {
    unset($_SESSION['kategori_filter']);
}
if (isset($_SESSION['filter_bidang'])) {
    unset($_SESSION['filter_bidang']);
}

require '../../src/functions.php';
include '../../src/controller/LoginF.php';
include '../../src/controller/lupapw.php';

// Handle clear kategori
if (isset($_GET['clear_kategori'])) {
    if (isset($_SESSION['kategori_filter'])) {
        unset($_SESSION['kategori_filter']);
    }
    if (isset($_SESSION['filter_bidang'])) {
        unset($_SESSION['filter_bidang']);
    }
    header("Location: " . str_replace('?clear_kategori=1', '', $_SERVER['REQUEST_URI']));
    exit();
}

// Cek apakah tombol tambah di klik
if (isset($_POST['tambah'])) {
    if (tambahLoker($_POST) > 0) {
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data Lowongan Kerja berhasil ditambahkan!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '../../pages/public/loker.php';
                });
            });
        </script>";
    } else {
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Data Lowongan Kerja gagal ditambahkan!',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}

// Cek apakah tombol edit di klik
if (isset($_POST['edit'])) {
    if (editLoker($_POST) !== false) {
        echo "
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Lowongan Kerja berhasil diubah!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '../../pages/public/loker.php';
            });
        });
        </script>";
    } else {
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Data Lowongan Kerja gagal diubah!',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}

function getLoker()
{
    global $conn;
    global $kategoriFilter;

    $query = "SELECT lowongan.*, perusahaan.nama_perusahaan, perusahaan.logo, perusahaan.alamat, perusahaan.bidang_usaha, perusahaan.email, perusahaan.telepon AS telepon
              FROM lowongan 
              JOIN perusahaan ON lowongan.id_perusahaan = perusahaan.id_perusahaan 
              WHERE tanggal_ditutup >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";

    // Tambahkan filter kategori jika ada
    if (!empty($kategoriFilter)) {
        $query .= " AND LOWER(perusahaan.bidang_usaha) = LOWER('" . mysqli_real_escape_string($conn, $kategoriFilter) . "')";
    }

    $query .= " ORDER BY lowongan.id_lowongan DESC";

    $result = mysqli_query($conn, $query);

    $loker = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $gaji_parts = explode('-', $row['gaji']);
        $gaji_minimal = isset($gaji_parts[0]) ? str_replace(['.', ','], ['', '.'], trim($gaji_parts[0])) : 0;
        $gaji_maksimal = isset($gaji_parts[1]) ? str_replace(['.', ','], ['', '.'], trim($gaji_parts[1])) : $gaji_minimal;

        // Format tampilan gaji
        if ($gaji_minimal == $gaji_maksimal) {
            $row['gaji_full'] = $row['mata_uang'] . ' ' . formatUangSingkat($gaji_minimal) . '/' . $row['kpn_gaji_diberi'];
        } else {
            $row['gaji_full'] = $row['mata_uang'] . ' ' . formatUangSingkat($gaji_minimal) . ' - ' . formatUangSingkat($gaji_maksimal) . '/' . $row['kpn_gaji_diberi'];
        }

        // Ubah persyaratan ke array
        if (is_string($row['persyaratan'])) {
            $row['persyaratan'] = explode(',', $row['persyaratan']);
        }

        $loker[] = $row;
    }

    return $loker;
}

$daftarperusahaan = getPerusahaan();

/**
 * Cari file CV berdasarkan id_alumni dan id_lowongan
 * @param int $id_alumni
 * @param int $id_lowongan
 * @return string|null  // path relatif jika ada, null jika tidak ada
 */
function getCvFile($id_alumni, $id_lowongan)
{
    $folder = "../../src/assets/persyaratan/cv/";
    $allowed_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    foreach ($allowed_ext as $ext) {
        $filename = "cv_{$id_alumni}_{$id_lowongan}.{$ext}";
        $filepath = $folder . $filename;
        if (file_exists($filepath)) {
            return $filepath;
        }
    }
    return null;
}

// Cek apakah ada pencarian dari halaman index.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = trim($_POST['search']);
    if (!empty($searchTerm)) {
        // Simpan kata kunci pencarian di session
        $_SESSION['search_from_index'] = $searchTerm;
    }
    // Redirect ke halaman ini dengan method GET agar tidak ada POST data
    header("Location: " . basename(__FILE__));
    exit();
}

// Ambil kata kunci dari session jika ada
$searchFromIndex = isset($_SESSION['search_from_index']) ? $_SESSION['search_from_index'] : '';

// Hapus session pencarian setelah diambil agar tidak muncul di refresh berikutnya
if (isset($_SESSION['search_from_index'])) {
    unset($_SESSION['search_from_index']);
}

// Inisialisasi variabel untuk cek lamaran alumni
$id_alumni = null;
if (isset($_SESSION['level']) && $_SESSION['level'] === 'alumni' && isset($_SESSION['id_pengguna'])) {
    $id_user = $_SESSION['id_pengguna'];
    // Ambil kode alumni dari user
    $query_user = mysqli_query($conn, "SELECT kode_pengguna FROM user WHERE id_user = '$id_user'");
    if ($query_user && mysqli_num_rows($query_user) > 0) {
        $data_user = mysqli_fetch_assoc($query_user);
        $kode_alumni = $data_user['kode_pengguna'];
        // Ambil id_alumni
        $query_alumni = mysqli_query($conn, "SELECT id_alumni FROM alumni WHERE kode_alumni = '$kode_alumni'");
        if ($query_alumni && mysqli_num_rows($query_alumni) > 0) {
            $data_alumni = mysqli_fetch_assoc($query_alumni);
            $id_alumni = $data_alumni['id_alumni'];
        }
    }
}
?>
<!doctype html>
<html lang="en">
<!--begin::Head-->

<?php
$title = "Lowongan-Kerja";
include '../../src/template/headers.php'
?>

<style>
    body {
        font-family: "Poppins", sans-serif;
        min-height: 100vh;
        background-color: #f8f9fa;
        scroll-behavior: smooth;
    }

    .swal2-popup {
        font-family: "Poppins", sans-serif;
    }

    /* Konten centering: no offset on mobile, offset on desktop */
    .content {
        padding: 1.5rem;
    }

    .mapsLink {
        font-size: 14px;
    }

    .linkMaps {
        text-decoration: none;
    }

    @media (max-width: 1330px) {

        .loker-card,
        .loker-card-admin {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 1170px) {
        .mapsLink {
            font-size: 13px;
        }
    }

    @media (min-width: 768px) {
        .content {
            margin-left: 220px;
        }
    }

    /* Container inside content always centered */
    .content .container {
        max-width: 1260px;
        margin: 0 auto;
    }

    .job-card {
        transition: transform .2s, box-shadow .2s;
        cursor: pointer;
    }

    .job-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
    }

    .add {
        font-size: 6rem;
        max-width: 100vh;
    }

    .text-add {
        font-size: 2rem;
        font-weight: bold;
    }

    /* Tambahan agar tombol X tidak terlalu besar dan rapi di input */
    #clearSearch {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: 0;
    }

    .search-clearable {
        position: relative;
    }

    .search-clearable::-webkit-search-cancel-button {
        -webkit-appearance: none;
        height: 1.2em;
        width: 1.2em;
        background: url('data:image/svg+xml;utf8,<svg fill="gray" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M2.146 2.146a.5.5 0 0 1 .708 0L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854a.5.5 0 0 1 0-.708z"/></svg>') no-repeat center center;
        cursor: pointer;
    }

    .search-clearable::-ms-clear {
        display: none;
    }

    .pagination-lg .page-link {
        font-size: 1rem;
        padding: 0.55rem .90rem;
    }

    .pagination {
        justify-content: end;
    }

    @media (max-width: 705px) {

        .loker-card,
        .loker-card-admin {
            flex: 100%;
            max-width: 100%;
        }

        .pagination-lg .page-link {
            font-size: .95rem;
            padding: 0.4rem .85rem;
        }

        .perP {
            justify-content: center;
        }

        .pagination {
            justify-content: center;
        }

        #perPage {
            width: 100%;
            max-width: 100%;
        }
    }

    /* Filter Sidebar Styles */
    .filter-sidebar {
        position: fixed;
        top: 0;
        right: -400px;
        width: 350px;
        height: 100vh;
        background: white;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1060;
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .filter-sidebar.show {
        right: 0;
    }

    .filter-sidebar-header {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: between;
        align-items: center;
        background: #f8f9fa;
    }

    .filter-sidebar-body {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
    }

    .filter-sidebar-footer {
        padding: 1rem;
        border-top: 1px solid #dee2e6;
        background: #f8f9fa;
    }

    .filter-group {
        margin-bottom: 1.5rem;
    }

    .filter-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #495057;
        margin-bottom: 0.5rem;
        display: block;
    }

    .filter-options {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.375rem;
    }

    .filter-options .form-check {
        margin-bottom: 0.5rem;
    }

    .filter-options .form-check:last-child {
        margin-bottom: 0;
    }

    .filter-sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1055;
        display: none;
    }

    .filter-sidebar-overlay.show {
        display: block;
    }

    /* Dropdown filter styles */
    #mainFilterDropdown .dropdown-menu {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Active filters badge */
    #activeFilterCount {
        font-size: 0.7rem;
        padding: 0.25rem 0.4rem;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .filter-sidebar {
            width: 300px;
        }

        .filter-controls .d-flex {
            flex-direction: column;
            gap: 0.5rem;
        }
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
                    <?php if (isset($_SESSION['level'])) : ?>
                        <!--begin::User Menu Dropdown-->
                        <li class="nav-item dropdown user-menu">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img src="../../src/assets/img/logo.png" class="user-image rounded-circle shadow" alt="User Image" />
                                <span class="d-none d-md-inline"><?= $_SESSION["nama"]; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                                <!--begin::User Image-->
                                <li class="user-header bg-secondary-subtle">
                                    <?php if ($_SESSION["gambar"] !== "") : ?>
                                        <img src="../../dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image" />
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
                                    <a href="./pengumuman-all.php" class="btn btn-default btn-flat" data-bs-trigger="hover" data-bs-placement="right" data-bs-custom-class="custom-tooltip-Bell" data-bs-title="Pengumuman">
                                        <i class="bi bi-bell"></i><span class="badge bg-danger float-end d-none badgePengumuman">0</span>
                                    </a>
                                    <a href="../../logout.php" class="btn btn-default btn-flat float-end btn-logout" data-bs-trigger="hover" data-bs-placement="left" data-bs-custom-class="custom-tooltip-logout" data-bs-title="LogOut ( Keluar )">
                                        <i class="fas fa-arrow-right-from-bracket"></i>
                                    </a>
                                </li>
                                <!--end::Menu Footer-->
                            </ul>
                        </li>
                        <!--end::User Menu Dropdown-->
                    <?php else: ?>
                        <ul class="navbar-nav ms-auto">
                            <!--begin::User Menu Dropdown-->
                            <button data-bs-toggle="modal" data-bs-target="#Modaldaftar" class="btn btn-outline-light ps-2 fw-medium d-flex align-items-center justify-content-center text-center" style="height: 30px; font-size: 13px;">
                                <i class="fa-solid fa-pen-to-square me-2"></i>Daftar
                            </button>
                            <button data-bs-toggle="modal" data-bs-target="#Modallogin" class="btn btn-outline-light ps-2 mx-2 fw-medium d-flex align-items-center justify-content-center text-center" style="height: 30px; font-size: 13px;">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                            </button>
                            <!--end::User Menu Dropdown-->
                        </ul>
                    <?php endif; ?>
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
        <main class="app-main mb-5">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6 d-flex">
                            <h3 class="mb-0">Manejemen Loker</h3>
                        </div>
                    </div>
                    <!--end::Row-->

                    <!--begin::Search & Filter Controls-->
                    <div class="row mb-2 mt-4 align-items-center g-2">
                        <div class="col-12 col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="search" id="searchLoker" class="form-control search-clearable border-start-0" placeholder="Cari loker, perusahaan, bidang..." autocomplete="off" value="<?= htmlspecialchars($searchFromIndex) ?>">
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="d-flex align-items-center">
                                <!-- Per Page Select - DI LUAR FILTER -->
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="perPageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-list-ol me-2"></i>
                                        <span id="perPageText">6 data</span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="perPageDropdown">
                                        <li><a class="dropdown-item per-page-option" href="#" data-value="3">3 data</a></li>
                                        <li><a class="dropdown-item per-page-option active" href="#" data-value="6">6 data</a></li>
                                        <li><a class="dropdown-item per-page-option" href="#" data-value="12">12 data</a></li>
                                        <li><a class="dropdown-item per-page-option" href="#" data-value="24">24 data</a></li>
                                        <li><a class="dropdown-item per-page-option" href="#" data-value="48">48 data</a></li>
                                        <li><a class="dropdown-item per-page-option" href="#" data-value="100">100 data</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
                                <!-- Toggle Filter Sidebar -->
                                <button class="btn btn-outline-primary d-flex align-items-center" id="toggleFilterSidebar">
                                    <i class="fas fa-filter me-2"></i>Panel Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Search & Filter Controls-->
                    <!-- Filter Sidebar -->
                    <div id="filterSidebar" class="filter-sidebar">
                        <div class="filter-sidebar-header">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-sliders-h me-2"></i>Panel Filter
                            </h6>
                            <button type="button" class="btn-close" id="closeFilterSidebar"></button>
                        </div>

                        <div class="filter-sidebar-body">
                            <!-- Status Kerjasama -->
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="fas fa-handshake me-2 text-success"></i>Status Kerjasama
                                </label>
                                <div class="filter-options">
                                    <div class="form-check">
                                        <input class="form-check-input filter-field-sidebar" type="radio" name="filterKerjasamaSidebar" id="kerjasamaAll" value="all" checked>
                                        <label class="form-check-label" for="kerjasamaAll">Semua Status</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input filter-field-sidebar" type="radio" name="filterKerjasamaSidebar" id="kerjasamaYes" value="bekerja_sama">
                                        <label class="form-check-label" for="kerjasamaYes">
                                            <i class="fas fa-handshake text-success me-1"></i>Bekerja Sama
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input filter-field-sidebar" type="radio" name="filterKerjasamaSidebar" id="kerjasamaNo" value="tidak_bekerja_sama">
                                        <label class="form-check-label" for="kerjasamaNo">
                                            <i class="fas fa-ban text-secondary me-1"></i>Tidak Bekerja Sama
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Bidang Usaha -->
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="fas fa-industry me-2 text-info"></i>Bidang Usaha
                                </label>
                                <select class="form-select form-select-sm filter-field-sidebar" id="filterBidangSidebar">
                                    <option value="all">Semua Bidang</option>
                                    <?php
                                    $bidangQuery = mysqli_query($conn, "SELECT DISTINCT bidang_usaha FROM perusahaan WHERE bidang_usaha != '' ORDER BY bidang_usaha");
                                    while ($bidang = mysqli_fetch_assoc($bidangQuery)) {
                                        echo '<option value="' . htmlspecialchars(strtolower($bidang['bidang_usaha'])) . '">' . htmlspecialchars($bidang['bidang_usaha']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Perusahaan -->
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="fas fa-building me-2 text-warning"></i>Perusahaan
                                </label>
                                <select class="form-select form-select-sm filter-field-sidebar" id="filterPerusahaanSidebar">
                                    <option value="all">Semua Perusahaan</option>
                                    <?php
                                    $perusahaanQuery = mysqli_query($conn, "SELECT id_perusahaan, nama_perusahaan FROM perusahaan ORDER BY nama_perusahaan");
                                    while ($perusahaan = mysqli_fetch_assoc($perusahaanQuery)) {
                                        echo '<option value="' . $perusahaan['id_perusahaan'] . '">' . htmlspecialchars($perusahaan['nama_perusahaan']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="filter-sidebar-footer">
                            <button type="button" class="btn btn-sm btn-outline-danger w-100 mb-2" id="resetAllFiltersSidebar">
                                <i class="fas fa-redo me-1"></i>Reset Semua Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-success w-100" id="applyFiltersSidebar">
                                <i class="fas fa-check me-1"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>

                    <!-- Overlay untuk sidebar -->
                    <div class="filter-sidebar-overlay" id="filterSidebarOverlay"></div>


                    <!-- Indikator Kategori dari Index -->
                    <?php if (!empty($kategoriFilter)): ?>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info alert-dismissible fade show py-2" role="alert">
                                    <i class="fas fa-filter me-2"></i>
                                    <strong>Filter Aktif:</strong> Menampilkan kategori
                                    <span class="badge bg-primary ms-1"><?= htmlspecialchars($kategoriFilter) ?></span>
                                    <a href="?clear_kategori=1" class="btn-close position-absolute end-0 me-3" aria-label="Close" style="top: 50%; transform: translateY(-50%);"></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Filter Active Indicator
                    <div id="activeFilters" class="row mb-3" style="display: none;">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <small class="text-muted me-2">Filter Aktif:</small>
                                <div id="filterTags"></div>
                                <button class="btn btn-sm btn-outline-danger" onclick="resetFilters()">
                                    <i class="fas fa-times me-1"></i>Clear All
                                </button>
                            </div>
                        </div>
                    </div> -->
                    <!--end::Search & Filter Controls-->
                    <div id="kategoriIndicator" style="display: none;"></div>
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row g-3" id="lokerList">
                        <!-- Results Counter -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted" id="resultsCount">Memuat lowongan...</small>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Klik card untuk melihat detail
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php
                        $daftarLoker = getLoker();
                        ?>

                        <div class="row g-3 mt-1" id="lokerCards">
                            <?php if (isset($_SESSION['level']) && $_SESSION['level'] === 'admin') : ?>
                                <!-- Tombol Tambah Loker -->
                                <div class="col-sm-6 col-xl-4 loker-card-admin">
                                    <div class="card job-card h-100 bg-secondary-subtle" data-bs-toggle="modal" data-bs-target="#modalLoker">
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-muted">
                                            <div class="add h-90"><i class="fa-solid fa-plus"></i></div>
                                            <div class="text-add">Tambah Loker</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Daftar Loker -->
                            <?php foreach ($daftarLoker as $loker) :
                                $alamat = $loker['alamat'];
                                $isTutup = strtotime($loker['tanggal_ditutup']) < time();
                                $isBelumBuka = strtotime($loker['tanggal_dibuka']) > time();
                                $statusKerjasama = $loker['status_kerjasama'];
                                
                                // Cek apakah alumni sudah melamar lowongan ini
                                $sudah_melamar = false;
                                if ($id_alumni !== null) {
                                    $cek_lamaran = mysqli_query($conn, "SELECT 1 FROM lamaran WHERE id_siswa = '$id_alumni' AND id_lowongan = '{$loker['id_lowongan']}'");
                                    $sudah_melamar = mysqli_num_rows($cek_lamaran) > 0;
                                }
                            ?>
                                <div class="col-sm-6 col-xl-4 loker-card"
                                    data-judul="<?= htmlspecialchars(strtolower($loker['judul'])) ?>"
                                    data-perusahaan="<?= htmlspecialchars(strtolower($loker['nama_perusahaan'])) ?>"
                                    data-bidang="<?= htmlspecialchars(strtolower($loker['bidang_usaha'])) ?>"
                                    data-kerjasama="<?= $statusKerjasama ?>"
                                    data-perusahaan-id="<?= $loker['id_perusahaan'] ?>">
                                    <div data-id="<?= $loker['id_lowongan'] ?>" class="card-click card job-card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="d-flex align-items-center">
                                                    <h5 class="card-title mb-0"><?= $loker['judul']; ?></h5>
                                                </div>
                                                <span class="text-muted"><strong><?= $loker['gaji_full']; ?></strong></span>
                                            </div>

                                            <div class="mb-3">
                                                <span class="badge bg-success p-2 text-uppercase"><?= $loker['bidang_usaha'] ?></span>
                                                <!-- Badge Status Kerjasama -->
                                                <span class="badge <?= $statusKerjasama == 'bekerja_sama' ? 'bg-success' : 'bg-danger' ?> p-2">
                                                    <?php if ($statusKerjasama == 'bekerja_sama') : ?>
                                                        <i class="fas fa-handshake"></i>
                                                    <?php else : ?>
                                                        <i class="fas fa-ban"></i>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <ul class="list-unstyled flex-grow-1">
                                                <li class="mb-2">
                                                    <strong>Nama Perusahaan:</strong><br>
                                                    <span class="badge bg-primary"> <?= $loker['nama_perusahaan']; ?> </span>
                                                </li>
                                                <li class="mb-2">
                                                    <strong>Persyaratan:</strong><br>
                                                    <?php foreach ($loker['persyaratan'] as $persyaratan): ?>
                                                        <span class="badge text-bg-warning"><?= htmlspecialchars($persyaratan); ?></span>
                                                    <?php endforeach; ?>
                                                </li>
                                                <li class="mb-2">
                                                    <strong>Deskripsi:</strong><br> <?= $loker['deskripsi']; ?>
                                                </li>
                                            </ul>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <img src="../../src/assets/img/perusahaan/logo/<?= $loker['logo']; ?>" alt="Logo Perusahaan" class="img-thumbnail" style="max-width: 40px; max-height: 40px;">
                                                    <div class="mt-1 ms-2 d-flex flex-column">
                                                        <span class="mb-1 mapsLink">
                                                            <?= '<a class="linkMaps icon-link icon-link-hover" href="https://www.google.com/maps?q=' . urlencode($alamat) . '" target="_blank">' . $alamat . '</a>'; ?>
                                                        </span>
                                                        <div class="d-flex flex-column">
                                                            <?php if ($isTutup) : ?>
                                                                <?php
                                                                $tanggal_tutup = strtotime($loker['tanggal_ditutup']);
                                                                $hari_ini = strtotime(date('Y-m-d'));
                                                                $selisih_hari = ceil(($hari_ini - $tanggal_tutup) / 86400);
                                                                ?>
                                                                <span class="text-muted" style="font-size: 12px;">
                                                                    <strong>Sudah di Tutup <?= $selisih_hari ?> Hari Lalu</strong>
                                                                </span>
                                                            <?php elseif ($isBelumBuka) : ?>
                                                                <?php
                                                                $tanggal_dibuka = strtotime($loker['tanggal_dibuka']);
                                                                $hari_ini = strtotime(date('Y-m-d'));
                                                                $selisih_hari = ceil(($tanggal_dibuka - $hari_ini) / 86400);
                                                                ?>
                                                                <span class="text-muted" style="font-size: 12px;">
                                                                    <strong>Dibuka <?= $selisih_hari ?> Hari Lagi</strong>
                                                                </span>
                                                            <?php else : ?>
                                                                <span class="time" style="font-size: 10px;">
                                                                    <?= $loker['tanggal_dibuka'] ?> -- <?= $loker['tanggal_ditutup'] ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column align-items-center">
                                                    <?php if (isset($_SESSION['level']) && $_SESSION['level'] === 'admin') : ?>
                                                        <span class="d-flex p-3 me-n3">
                                                            <a href="" class="btn btn-sm btn-outline-success mb-1 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $loker['id_lowongan']; ?>" data-bs-trigger="hover" data-bs-placement="top" data-bs-custom-class="custom-tooltip-Edit" data-bs-title="Edit ( Ubah )">
                                                                <i class="fas fa-gear"></i>
                                                            </a>
                                                            <a href="../../src/config/hapus-dataloker.php?id=<?= $loker['id_lowongan'] ?>" class="btn btn-sm btn-outline-danger btn-hapus mb-1 ms-1" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip-Delete" data-bs-title="Delete ( Hapus )">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </span>
                                                    <?php elseif (isset($_SESSION['level']) && $_SESSION['level'] === 'alumni') : ?>
                                                        <?php if ($isTutup) : ?>
                                                            <a href="" class="btn btn-xs btn-primary" style="font-size:0.85rem;">Lainnya</a>
                                                        <?php elseif ($isBelumBuka) : ?>
                                                            <a href="" class="btn btn-xs btn-primary" style="font-size:0.85rem;">Lainnya</a>
                                                        <?php elseif ($sudah_melamar) : ?>
                                                            <button class="btn btn-sm px-4 btn-success" disabled>Lamar</button>
                                                        <?php else : ?>
                                                            <a href="" data-bs-toggle="modal" data-bs-target="#modalSyarat<?= $loker['id_lowongan']; ?>" class="btn btn-sm px-4 btn-outline-primary">Lamar</a>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        <a href="" data-bs-toggle="modal" data-bs-target="#Modallogin" class="btn btn-sm px-4 btn-outline-primary">Login</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Pesan jika tidak ada loker ditemukan -->
                        <div id="notFoundLoker" class="text-center text-muted my-5" style="display:none;">
                            <i class="bi bi-search" style="font-size:2rem;"></i><br>
                            <span class="fs-5">Loker tidak ditemukan</span>
                        </div>
                        <!--begin::Pagination Controls-->
                        <div class="row mt-3">
                            <div class="col-12">
                                <nav>
                                    <ul class="pagination mb-0 pagination-lg" id="paginationLoker">
                                        <!-- Pagination will be rendered here -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <!--end::Pagination Controls-->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <?php include '../../src/template/app-footer.php'; ?>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <?php include '../../src/template/modalForm.php' ?>

    <?php foreach ($daftarLoker as $loker) : ?>
        <!--begin::Modal Syarat -->
        <div class="modal fade" id="modalSyarat<?= $loker['id_lowongan']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <?php
                $statusKerjasama = $loker['status_kerjasama'];
                $formAttributes = $statusKerjasama == 'bekerja_sama' ? 'enctype="multipart/form-data"' : '';
                ?>

                <form action="../../src/config/proses-lamaran.php?id=<?= $loker['id_lowongan'] ?>" method="POST" <?= $formAttributes ?>>
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-paper-plane me-2"></i>
                                Lamar Lowongan - <?= htmlspecialchars($loker['judul']) ?>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_lowongan" value="<?= $loker['id_lowongan']; ?>">
                            <input type="hidden" name="id_alumni" value="<?= $_SESSION['id_pengguna'] ?>">

                            <!-- Info Status Kerjasama -->
                            <div class="alert <?= $statusKerjasama == 'bekerja_sama' ? 'alert-success' : 'alert-warning' ?> mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas <?= $statusKerjasama == 'bekerja_sama' ? 'fa-handshake' : 'fa-building' ?> fa-2x me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">
                                            <?= $statusKerjasama == 'bekerja_sama' ? 'Bekerja Sama dengan Sekolah' : 'Tidak Bekerja Sama' ?>
                                        </h6>
                                        <p class="mb-0 small">
                                            <?= $statusKerjasama == 'bekerja_sama'
                                                ? 'Lamaran Anda akan diproses melalui sistem sekolah. Upload CV Anda di bawah ini.'
                                                : 'Untuk lowongan ini, Anda perlu mengirimkan lamaran langsung ke perusahaan.' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <?php if ($statusKerjasama == 'bekerja_sama') : ?>
                                <!-- Untuk lowongan bekerja sama - upload CV -->
                                <div class="mb-4">
                                    <label for="cv_<?= $loker['id_lowongan']; ?>" class="form-label fw-semibold">
                                        <i class="fas fa-file-upload me-2 text-primary"></i>
                                        Upload CV <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="cv" id="cv_<?= $loker['id_lowongan']; ?>" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1 text-info"></i>
                                        Format: PDF, Word, Excel (Maks. 100MB)
                                    </div>
                                </div>
                            <?php else : ?>
                                <!-- Untuk lowongan tidak bekerja sama - informasi kontak perusahaan -->
                                <div class="mb-4">
                                    <div class="card border-warning shadow-sm">
                                        <div class="card-header bg-warning text-dark d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <strong>Kirim Lamaran Langsung ke Perusahaan</strong>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text text-muted mb-3">
                                                Silakan hubungi perusahaan melalui kontak di bawah ini untuk mengirimkan CV dan dokumen lamaran Anda.
                                            </p>

                                            <div class="row g-3">
                                                <!-- Informasi Perusahaan -->
                                                <div class="col-12">
                                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                                        <i class="fas fa-building fa-lg me-3 mt-4 text-primary mt-1"></i>
                                                        <div>
                                                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($loker['nama_perusahaan']) ?></h6>
                                                            <p class="text-muted mb-0"><?= htmlspecialchars($loker['bidang_usaha']) ?></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Alamat -->
                                                <div class="col-12">
                                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                                        <i class="fas fa-map-marker-alt fa-lg me-3 mt-4 text-danger mt-1"></i>
                                                        <div>
                                                            <h6 class="fw-bold mb-1">Alamat Perusahaan</h6>
                                                            <a href="https://www.google.com/maps?q=<?= urlencode($loker['alamat']) ?>" target="_blank" class="linkMaps">
                                                                <?= htmlspecialchars($loker['alamat']) ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Email -->
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-start p-3 bg-light rounded h-100">
                                                        <i class="fas fa-envelope fa-lg me-3 mt-4 text-success mt-1"></i>
                                                        <div>
                                                            <h6 class="fw-bold mb-1">Email</h6>
                                                            <p>
                                                                <?= htmlspecialchars($loker['email']) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Telepon -->
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-start p-3 bg-light rounded h-100">
                                                        <i class="fas fa-phone fa-lg me-3 mt-4 text-info mt-1"></i>
                                                        <div>
                                                            <h6 class="fw-bold mb-1">Telepon</h6>
                                                            <p>
                                                                <?= htmlspecialchars($loker['telepon']) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tips -->
                                            <div class="mt-4 p-3 bg-info bg-opacity-10 rounded">
                                                <h6 class="fw-bold text-info mb-2">
                                                    <i class="fas fa-lightbulb me-2"></i>Tips Melamar:
                                                </h6>
                                                <ul class="list-unstyled mb-0 small">
                                                    <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i> Siapkan CV dan surat lamaran yang profesional</li>
                                                    <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i> Hubungi perusahaan via telepon/email terlebih dahulu</li>
                                                    <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i> Konfirmasi proses rekrutmen yang berlaku</li>
                                                    <li class="mb-0"><i class="fas fa-check-circle text-success me-2"></i> Follow up lamaran setelah 3-5 hari</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Konfirmasi -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="confirm<?= $loker['id_lowongan'] ?>" required>
                                    <label class="form-check-label" for="confirm<?= $loker['id_lowongan'] ?>">
                                        Saya telah memahami bahwa untuk lowongan ini perlu mengirim lamaran langsung ke perusahaan
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Batal
                            </button>
                            <?php if ($statusKerjasama == 'bekerja_sama') : ?>
                                <button type="submit" name="kirim" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Kirim Lamaran
                                </button>
                            <?php else : ?>
                                <button type="submit" name="kirim" class="btn btn-success">
                                    <i class="fas fa-check-circle me-1"></i> Konfirmasi
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End::Modal Syarat -->
    <?php endforeach; ?>

    <!--begin::Modal Tambah Data-->
    <div class="modal fade" id="modalLoker" tabindex="-1" aria-labelledby="modalLokerLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLokerLabel">Tambah Lowongan Kerja</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" class="needs-validation" novalidate onsubmit="return validateGajiForm()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group my-3">
                                <div class="input-group-text px-3"><span class="fas fa-user-tie fa-lg"></span></div>
                                <div class="form-floating me-1">
                                    <input id="judul" type="judul" name="judul" class="form-control" placeholder="" required autocomplete="off" />
                                    <label for="judul" class="form-label">Judul</label>
                                </div>
                                <div class="form-floating">
                                    <select class="form-control" name="status_kerjasama" id="status_kerjasama" required>
                                        <option value="" selected disabled>Pilih Status Loker</option>
                                        <option value="tidak_bekerja_sama">Tidak Bekerja Sama</option>
                                        <option value="bekerja_sama">Bekerja Sama</option>
                                    </select>
                                    <label for="status_kerjasama" class="form-label">Status Loker</label>
                                </div>
                                <div class="input-group-text px-3"><span class="fas fa-handshake fa-lg"></span></div>
                            </div>
                            <div class="input-group my-3">
                                <button class="btn btn-outline-secondary dropdown-toggle fs-5" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="currencyType1">Rp</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="setMataUang('Rp', 1)">Rp - Rupiah</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setMataUang('$', 1)">$ - Dollar</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setMataUang('€', 1)">€ - Euro</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setMataUang('£', 1)">£ - Pound</a></li>
                                </ul>
                                <input type="hidden" name="mata_uang" id="mata_uang1" value="Rp">
                                <div class="form-floating">
                                    <input id="gaji" type="text" name="gaji" class="gaji form-control" placeholder="Masukkan Gaji" required autocomplete="off" />
                                    <label for="gaji" class="form-label">Gaji</label>
                                </div>
                                <div id="gajiAkhirContainer" style="width: 12rem; display: none;">
                                    <div class="input-group-text px-3"><span class="fw-semibold">-</span></div>
                                    <div class="form-floating">
                                        <input id="gaji_akhir" type="text" name="gaji_akhir" class="gaji_akhir form-control rounded-0" placeholder="Masukkan Gaji Akhir" autocomplete="off" />
                                        <label for="gaji_akhir" class="form-label">Gaji Akhir</label>
                                    </div>
                                </div>
                                <input type="hidden" name="kpn_gaji_diberi" id="kpn_gaji_diberi1" value="B">
                                <button class="btn btn-outline-secondary dropdown-toggle fs-5" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="currencyPeriod1">/B</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('H', 1)">/Hari</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('M', 1)">/Minggu</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('B', 1)">/Bulan</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('T', 1)">/Tahun</a></li>
                                </ul>
                            </div>
                            <div class="form-check ms-3 mt-2">
                                <input class="form-check-input" type="checkbox" id="rentangGaji" onchange="toggleGajiAkhir()">
                                <label class="form-check-label" for="rentangGaji">
                                    Gaji Rentang
                                </label>
                            </div>
                            <div class="input-group my-3">
                                <div class="input-group-text px-3"><span class="fas fa-building fa-lg"></span></div>
                                <div class="form-floating">
                                    <select class="form-control" name="perusahaan" id="perusahaan" placeholder="" required>
                                        <option value="" disabled selected> Pilih Perusahaan </option>
                                        <?php foreach ($daftarperusahaan as $perusahaan) : ?>
                                            <option value="<?= $perusahaan['id_perusahaan']; ?>">
                                                <?= $perusahaan['nama_perusahaan']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="perusahaan" class="form-label">Perusahaan</label>
                                </div>
                            </div>
                            <div class="input-group my-3">
                                <div class="form-floating">
                                    <div id="persyaratan-list">
                                        <!-- Input Persyaratan akan muncul di sini -->
                                    </div>
                                    <!-- Tombol untuk menambah persyaratan -->
                                    <button type="button" class="btn btn-primary" id="add-btn">+ Tambah Persyaratan</button>
                                    <br>
                                </div>
                            </div>
                            <div class="input-group my-3">
                                <div class="form-floating">
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" placeholder=""></textarea>
                                    <label for="deskripsi">Deskripsi</label>
                                </div>
                            </div>
                            <div class="input-group my-3">
                                <div class="form-floating flex-grow-1">
                                    <input type="date" class="form-control flatpickr" id="tanggal_dibuka" name="tanggal_dibuka" placeholder="" required autocomplete="off" data-date-format="Y-m-d">
                                    <label for="tanggal_dibuka" class="form-label">Tanggal Dibuka</label>
                                </div>
                                <div class="input-group-text mx-1 rounded"><label class="fas fa-business-time fa-lg"></label></div>
                                <div class="form-floating flex-grow-1">
                                    <input type="date" class="form-control flatpickr" id="tanggal_ditutup" name="tanggal_ditutup" placeholder="" required autocomplete="off" data-date-format="Y-m-d">
                                    <label for="tanggal_ditutup" class="form-label">Tanggal Ditutup</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End::Modal Tambah Data -->

    <!-- Modal Edit -->
    <?php foreach ($daftarLoker as $loker) : ?>
        <?php
        $gaji_parts = explode('-', $loker['gaji']);
        $gaji_minimal = isset($gaji_parts[0]) ? trim($gaji_parts[0]) : '';
        $gaji_maksimal = isset($gaji_parts[1]) ? trim($gaji_parts[1]) : '';
        $isRentang = !empty($gaji_maksimal) && $gaji_maksimal !== $gaji_minimal;
        ?>
        <div class="modal fade" id="modalEdit<?= $loker['id_lowongan']; ?>" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalEditLabel">Edit Lowongan Kerja</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post" class="needs-validation" novalidate onsubmit="return validateGajiFormEdit(<?= $loker['id_lowongan']; ?>)">
                        <input type="hidden" name="id_lowongan" value="<?= $loker['id_lowongan']; ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group my-3">
                                    <div class="input-group-text px-3"><span class="fas fa-user-tie fa-lg"></span></div>
                                    <div class="form-floating">
                                        <input id="judul" type="judul" name="judul" class="form-control" placeholder="" value="<?= $loker['judul'] ?>" autocomplete="off" />
                                        <label for="judul" class="form-label">Judul</label>
                                    </div>
                                    <div class="form-floating ms-1">
                                        <select class="form-control" name="status_kerjasama" id="status_kerjasama<?= $loker['id_lowongan']; ?>" required>
                                            <option value="" selected disabled>Pilih Status Loker</option>
                                            <option value="tidak_bekerja_sama" <?= $loker['status_kerjasama'] == 'tidak_bekerja_sama' ? 'selected' : '' ?>>Tidak Bekerja Sama</option>
                                            <option value="bekerja_sama" <?= $loker['status_kerjasama'] == 'bekerja_sama' ? 'selected' : '' ?>>Bekerja Sama</option>
                                        </select>
                                        <label for="status_kerjasama<?= $loker['id_lowongan']; ?>" class="form-label">Status Loker</label>
                                    </div>
                                    <div class="input-group-text px-3"><span class="fas fa-handshake fa-lg"></span></div>
                                </div>
                                <div class="input-group my-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle fs-5" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="currencyType<?= $loker['id_lowongan']; ?>">
                                        <?= $loker['mata_uang']; ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="setMataUang('Rp', <?= $loker['id_lowongan']; ?>)">Rp - Rupiah</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setMataUang('$', <?= $loker['id_lowongan']; ?>)">$ - Dollar</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setMataUang('€', <?= $loker['id_lowongan']; ?>)">€ - Euro</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setMataUang('£', <?= $loker['id_lowongan']; ?>)">£ - Pound</a></li>
                                    </ul>
                                    <input type="hidden" name="mata_uang" id="mata_uang<?= $loker['id_lowongan']; ?>" value="<?= $loker['mata_uang']; ?>">
                                    <div class="form-floating">
                                        <input id="gaji<?= $loker['id_lowongan']; ?>" type="text" name="gaji" class="gaji form-control" placeholder="Masukkan Gaji" value="<?= $gaji_minimal; ?>" autocomplete="off" />
                                        <label for="gaji<?= $loker['id_lowongan']; ?>" class="form-label">Gaji</label>
                                    </div>
                                    <div id="gajiAkhirContainer<?= $loker['id_lowongan']; ?>" style="width: 12rem; display: <?= $isRentang ? 'flex' : 'none'; ?>;">
                                        <div class="input-group-text px-3"><span class="fw-semibold">-</span></div>
                                        <div class="form-floating">
                                            <input id="gaji_akhir<?= $loker['id_lowongan']; ?>" type="text" name="gaji_akhir" class="gaji_akhir form-control rounded-0" placeholder="Masukkan Gaji Akhir" value="<?= $gaji_maksimal; ?>" autocomplete="off" />
                                            <label for="gaji_akhir<?= $loker['id_lowongan']; ?>" class="form-label">Gaji Akhir</label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="kpn_gaji_diberi" id="kpn_gaji_diberi<?= $loker['id_lowongan']; ?>" value="<?= $loker['kpn_gaji_diberi']; ?>">
                                    <button class="btn btn-outline-secondary dropdown-toggle fs-5" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="currencyPeriod<?= $loker['id_lowongan']; ?>">
                                        <?= '/' . $loker['kpn_gaji_diberi']; ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('H', <?= $loker['id_lowongan']; ?>)">/Hari</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('M', <?= $loker['id_lowongan']; ?>)">/Minggu</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('B', <?= $loker['id_lowongan']; ?>)">/Bulan</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('T', <?= $loker['id_lowongan']; ?>)">/Tahun</a></li>
                                    </ul>
                                </div>
                                <div class="form-check ms-3 mt-2">
                                    <input class="form-check-input" type="checkbox" id="rentangGaji<?= $loker['id_lowongan']; ?>" onchange="toggleGajiAkhir(<?= $loker['id_lowongan']; ?>)" <?= $isRentang ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="rentangGaji<?= $loker['id_lowongan']; ?>">
                                        Gaji Rentang
                                    </label>
                                </div>
                                <div class="input-group my-3">
                                    <div class="input-group-text px-3"><span class="fas fa-building fa-lg"></span></div>
                                    <div class="form-floating">
                                        <select class="form-control" name="perusahaan" id="perusahaan" placeholder="">
                                            <?php foreach ($daftarperusahaan as $perusahaan) : ?>
                                                <option value="<?= $perusahaan['id_perusahaan'] ?>" <?= $perusahaan['id_perusahaan'] == $loker['id_perusahaan'] ? 'selected' : '' ?>>
                                                    <?= $perusahaan['nama_perusahaan'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="perusahaan" class="form-label">Perusahaan</label>
                                    </div>
                                </div>
                                <div class="input-group my-3">
                                    <div class="form-floating">
                                        <div id="persyaratan-list-<?= $loker['id_lowongan']; ?>">
                                            <?php foreach ($loker['persyaratan'] as $index => $persyaratan) : ?>
                                                <div class="input-group mb-2" id="persyaratan-item-<?= $index; ?>">
                                                    <input type="text" name="persyaratan[]" class="form-control" value="<?= trim($persyaratan); ?>" placeholder="Tulis Persyaratan" required>
                                                    <button type="button" class="btn btn-danger" onclick="removePersyaratan(this)">Hapus</button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-primary mt-2" onclick="addPersyaratan(<?= $loker['id_lowongan']; ?>)">+ Tambah Persyaratan</button>
                                    </div>
                                </div>
                                <div class="input-group my-3">
                                    <div class="form-floating">
                                        <textarea name="deskripsi" id="deskripsi" class="form-control" placeholder=""><?= $loker['deskripsi'] ?></textarea>
                                        <label for="deskripsi">Deskripsi</label>
                                    </div>
                                </div>
                                <div class="input-group my-3">
                                    <div class="form-floating flex-grow-1">
                                        <input type="date" class="form-control flatpickr" id="tanggal_dibuka" name="tanggal_dibuka" placeholder="" value="<?= $loker['tanggal_dibuka'] ?>" autocomplete="off" data-date-format="Y-m-d">
                                        <label for="tanggal_dibuka" class="form-label">Tanggal Dibuka</label>
                                    </div>
                                    <div class="input-group-text mx-1 rounded"><label class="fas fa-business-time fa-lg"></label></div>
                                    <div class="form-floating flex-grow-1">
                                        <input type="date" class="form-control flatpickr" id="tanggal_ditutup" name="tanggal_ditutup" placeholder="" value="<?= $loker['tanggal_ditutup'] ?>" autocomplete="off" data-date-format="Y-m-d">
                                        <label for="tanggal_ditutup" class="form-label">Tanggal Ditutup</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit" class="btn btn-primary">Simpan!</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- End::Modal Edit Data -->

    <!--begin::Script-->
    <?php include '../../src/template/footer.php'; ?>

    <!-- OPTIONAL SCRIPTS -->
    <!--begin::Validation-->
    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
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

        const modalTambah = document.getElementById('modalLoker');
        modalTambah.addEventListener('hidden.bs.modal', function() {
            const form = modalTambah.querySelector('form');
            form.classList.remove('was-validated');
            form.reset();
            // Reset gaji akhir container
            const gajiAkhirContainer = document.getElementById('gajiAkhirContainer');
            gajiAkhirContainer.style.display = 'none';
            const rentangGaji = document.getElementById('rentangGaji');
            rentangGaji.checked = false;
        });
    </script>
    <!--end::Validation-->

    <!-- begin::Form -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modals = document.querySelectorAll('.modalLoker');
            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    const form = modal.querySelector('form');
                    if (form) {
                        form.reset();
                    }
                });
            });
        });
    </script>
    <!-- end::Form -->

    <!-- Script Persyaratan -->
    <script>
        document.getElementById('add-btn').addEventListener('click', function() {
            const persyaratanList = document.getElementById('persyaratan-list');
            const newPersyaratan = document.createElement('div');
            newPersyaratan.classList.add('input-container');
            newPersyaratan.innerHTML = `
                <div class="input-group">
                    <input type="text" name="persyaratan[]" class="form-control" placeholder="Tulis Persyaratan" required>
                    <button type="button" class="btn btn-danger" onclick="removePersyaratan(this)">Hapus</button>
                </div><br>
            `;
            persyaratanList.appendChild(newPersyaratan);
        });

        function removePersyaratan(button) {
            const persyaratanContainer = button.parentElement.parentElement;
            persyaratanContainer.remove();
        }

        function addPersyaratan(id) {
            const list = document.getElementById(`persyaratan-list-${id}`);
            const index = list.children.length;
            const newInput = document.createElement('div');
            newInput.classList.add('input-group', 'mb-2');
            newInput.id = `persyaratan-item-${index}`;
            newInput.innerHTML = `
                <input type="text" name="persyaratan[]" class="form-control" placeholder="Tulis Persyaratan" required>
                <button type="button" class="btn btn-danger" onclick="removePersyaratan(this)">Hapus</button>
            `;
            list.appendChild(newInput);
        }
    </script>
    <!-- End Sript Persyaratan -->

    <!--begin::Nominal Gaji-->
    <script>
        document.querySelectorAll('input[name="gaji"]').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                let formattedValue = '';
                for (let i = value.length - 1; i >= 0; i--) {
                    formattedValue = value[i] + formattedValue;
                    if ((value.length - i) % 3 === 0 && i !== 0) {
                        formattedValue = '.' + formattedValue;
                    }
                }
                e.target.value = formattedValue;
            });
        });

        document.querySelectorAll('input[name="gaji_akhir"]').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                let formattedValue = '';
                for (let i = value.length - 1; i >= 0; i--) {
                    formattedValue = value[i] + formattedValue;
                    if ((value.length - i) % 3 === 0 && i !== 0) {
                        formattedValue = '.' + formattedValue;
                    }
                }
                e.target.value = formattedValue;
            });
        });
    </script>
    <!--end::Nominal Gaji-->

    <!-- Date -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d",
                allowInput: true,
                disableMobile: true
            });
        });
    </script>

    <!-- Begin::Currency Dropdown -->
    <script>
        function setkpn_gaji_diberi(value, id) {
            document.getElementById('currencyPeriod' + id).textContent = '/' + value.charAt(0).toUpperCase();
            document.getElementById('kpn_gaji_diberi' + id).value = value;
        }

        function setMataUang(value, id) {
            document.getElementById('currencyType' + id).textContent = value;
            document.getElementById('mata_uang' + id).value = value;
        }
    </script>
    <!-- End::Currency Dropdown -->

    <!-- Begin::Toggle Gaji Akhir -->
    <script>
        function toggleGajiAkhir(id = null) {
            const containerId = id ? `gajiAkhirContainer${id}` : 'gajiAkhirContainer';
            const container = document.getElementById(containerId);
            const checkboxId = id ? `rentangGaji${id}` : 'rentangGaji';
            const checkbox = document.getElementById(checkboxId);

            if (checkbox.checked) {
                container.style.display = 'flex';
                // Set required attribute jika checkbox dicentang
                const gajiAkhirInput = container.querySelector('input[name="gaji_akhir"]');
                if (gajiAkhirInput) {
                    gajiAkhirInput.required = true;
                }
            } else {
                container.style.display = 'none';
                // Hapus required attribute dan clear value jika checkbox tidak dicentang
                const gajiAkhirInput = container.querySelector('input[name="gaji_akhir"]');
                if (gajiAkhirInput) {
                    gajiAkhirInput.required = false;
                    gajiAkhirInput.value = '';
                }
            }
        }

        // Fungsi untuk inisialisasi toggle saat modal dibuka
        function initGajiToggle(id = null) {
            const checkboxId = id ? `rentangGaji${id}` : 'rentangGaji';
            const checkbox = document.getElementById(checkboxId);
            if (checkbox) {
                toggleGajiAkhir(id);
            }
        }
    </script>
    <!-- End::Toggle Gaji Akhir -->

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

    <!-- Validasi Form Gaji -->
    <script>
        function validateGajiForm() {
            const rentangGajiChecked = document.getElementById('rentangGaji').checked;
            const gajiAkhirInput = document.getElementById('gaji_akhir');

            if (rentangGajiChecked && (!gajiAkhirInput.value || gajiAkhirInput.value.trim() === '')) {
                alert('Harap isi gaji akhir jika memilih rentang gaji');
                gajiAkhirInput.focus();
                return false;
            }

            return true;
        }

        function validateGajiFormEdit(id) {
            const rentangGajiChecked = document.getElementById('rentangGaji' + id).checked;
            const gajiAkhirInput = document.getElementById('gaji_akhir' + id);

            if (rentangGajiChecked && (!gajiAkhirInput.value || gajiAkhirInput.value.trim() === '')) {
                alert('Harap isi gaji akhir jika memilih rentang gaji');
                gajiAkhirInput.focus();
                return false;
            }

            return true;
        }

        // Inisialisasi saat modal dibuka
        document.addEventListener('DOMContentLoaded', function() {
            const modalLoker = document.getElementById('modalLoker');
            if (modalLoker) {
                modalLoker.addEventListener('shown.bs.modal', function() {
                    initGajiToggle();
                });
            }

            // Untuk modal edit
            const modalsEdit = document.querySelectorAll('[id^="modalEdit"]');
            modalsEdit.forEach(modal => {
                modal.addEventListener('shown.bs.modal', function() {
                    const id = this.id.replace('modalEdit', '');
                    initGajiToggle(id);
                });
            });
        });
    </script>

    <script>
        $(function() {
            // State management - INISIALISASI DENGAN FILTER DARI SESSION
            let filterState = {
                search: '<?= htmlspecialchars($searchFromIndex) ?>',
                kerjasama: 'all',
                bidang: '<?= !empty($filterBidang) ? htmlspecialchars(strtolower($filterBidang)) : "all" ?>',
                perusahaan: 'all',
                perPage: 6,
                currentPage: 1
            };

            // Initialize
            function init() {
                bindEvents();
                updateFilterCount();
                applySessionFilters(); // Terapkan filter dari session
                filterAndPaginate();
            }

            // Terapkan filter dari session
            function applySessionFilters() {
                // Set nilai dropdown bidang jika ada filter dari session
                if (filterState.bidang !== 'all') {
                    $('#filterBidang').val(filterState.bidang);
                    $('#filterBidangSidebar').val(filterState.bidang);

                    // Update tampilan filter count
                    updateFilterCount();
                }

                // Set nilai search input
                if (filterState.search) {
                    $('#searchLoker').val(filterState.search);
                }
            }

            // Bind events
            function bindEvents() {
                // Search input
                $('#searchLoker').on('input search', function() {
                    filterState.search = $(this).val().toLowerCase();
                    filterState.currentPage = 1;
                    filterAndPaginate();
                });

                // Dropdown filter changes
                $('.filter-field').on('change', function() {
                    updateFilterState();
                });

                // Sidebar filter changes
                $('.filter-field-sidebar').on('change', function() {
                    updateFilterState();
                });

                // Per page options
                $('.per-page-option').on('click', function(e) {
                    e.preventDefault();
                    $('.per-page-option').removeClass('active');
                    $(this).addClass('active');
                    filterState.perPage = parseInt($(this).data('value'));
                    filterState.currentPage = 1;
                    updatePerPageText();
                    filterAndPaginate();
                });

                // Apply filters (dropdown)
                $('#applyFilters').on('click', function() {
                    $('.dropdown-menu').removeClass('show');
                    filterState.currentPage = 1;
                    filterAndPaginate();
                });

                // Apply filters (sidebar)
                $('#applyFiltersSidebar').on('click', function() {
                    hideFilterSidebar();
                    filterState.currentPage = 1;
                    filterAndPaginate();
                });

                // Reset filters (dropdown)
                $('#resetAllFilters').on('click', function() {
                    resetFilters();
                    $('.dropdown-menu').removeClass('show');
                });

                // Reset filters (sidebar)
                $('#resetAllFiltersSidebar').on('click', function() {
                    resetFilters();
                });

                // Toggle filter sidebar
                $('#toggleFilterSidebar').on('click', function() {
                    showFilterSidebar();
                });

                // Close filter sidebar
                $('#closeFilterSidebar').on('click', function() {
                    hideFilterSidebar();
                });

                // Close sidebar when clicking overlay
                $('#filterSidebarOverlay').on('click', function() {
                    hideFilterSidebar();
                });

                // Pagination
                $('#paginationLoker').on('click', 'li.page-item:not(.disabled) a', function(e) {
                    e.preventDefault();
                    let page = $(this).data('page');
                    if (page !== undefined) {
                        filterState.currentPage = page;
                        filterAndPaginate();
                    }
                });
            }

            // Update filter state from form fields
            function updateFilterState() {
                // From dropdown
                filterState.kerjasama = $('#filterKerjasama').val();
                filterState.bidang = $('#filterBidang').val();
                filterState.perusahaan = $('#filterPerusahaan').val();

                // From sidebar
                filterState.kerjasama = $('input[name="filterKerjasamaSidebar"]:checked').val() || filterState.kerjasama;
                filterState.bidang = $('#filterBidangSidebar').val() || filterState.bidang;
                filterState.perusahaan = $('#filterPerusahaanSidebar').val() || filterState.perusahaan;

                updateFilterCount();
            }

            // Update per page text
            function updatePerPageText() {
                $('#perPageText').text(filterState.perPage + ' data');
            }

            // Update active filter count
            function updateFilterCount() {
                let count = 0;
                if (filterState.kerjasama !== 'all') count++;
                if (filterState.bidang !== 'all') count++;
                if (filterState.perusahaan !== 'all') count++;

                $('#activeFilterCount').text(count);

                // Update button text based on active filters
                if (count > 0) {
                    $('#toggleFilterSidebar').html('<i class="fas fa-filter me-2"></i>Panel Filter (' + count + ')');
                    $('#mainFilterDropdown').html('<i class="fas fa-sliders-h me-2"></i>Filter Detail <span class="badge bg-primary ms-2">' + count + '</span>');

                    // Tampilkan active filters
                    $('#activeFilters').show();
                    updateFilterTags();
                } else {
                    $('#toggleFilterSidebar').html('<i class="fas fa-filter me-2"></i>Panel Filter');
                    $('#mainFilterDropdown').html('<i class="fas fa-sliders-h me-2"></i>Filter Detail <span class="badge bg-primary ms-2">0</span>');
                    $('#activeFilters').hide();
                }
            }

            // Update filter tags
            function updateFilterTags() {
                let tagsHtml = '';

                if (filterState.bidang !== 'all') {
                    tagsHtml += `<span class="badge bg-info">Bidang: ${$('#filterBidang option:selected').text()}</span>`;
                }
                if (filterState.kerjasama !== 'all') {
                    const kerjasamaText = filterState.kerjasama === 'bekerja_sama' ? 'Bekerja Sama' : 'Tidak Bekerja Sama';
                    tagsHtml += `<span class="badge bg-warning">${kerjasamaText}</span>`;
                }
                if (filterState.perusahaan !== 'all') {
                    tagsHtml += `<span class="badge bg-success">Perusahaan: ${$('#filterPerusahaan option:selected').text()}</span>`;
                }

                $('#filterTags').html(tagsHtml);
            }

            // Show filter sidebar
            function showFilterSidebar() {
                $('#filterSidebar').addClass('show');
                $('#filterSidebarOverlay').addClass('show');
                $('body').css('overflow', 'hidden');
            }

            // Hide filter sidebar
            function hideFilterSidebar() {
                $('#filterSidebar').removeClass('show');
                $('#filterSidebarOverlay').removeClass('show');
                $('body').css('overflow', 'auto');
            }

            // Fungsi global untuk reset filters
            function resetFilters() {
                $('#resetAllFilters').click();
            }

            // Reset all filters
            function resetFilters() {
                // Reset form fields
                $('#searchLoker').val('');
                $('#filterKerjasama').val('all');
                $('#filterBidang').val('all');
                $('#filterPerusahaan').val('all');

                $('input[name="filterKerjasamaSidebar"][value="all"]').prop('checked', true);
                $('#filterBidangSidebar').val('all');
                $('#filterPerusahaanSidebar').val('all');

                // Reset state
                filterState.search = '';
                filterState.kerjasama = 'all';
                filterState.bidang = 'all';
                filterState.perusahaan = 'all';
                filterState.currentPage = 1;

                updateFilterCount();
                filterAndPaginate();
            }

            // Main filter function
            function filterAndPaginate() {
                let $cards = $('#lokerCards .loker-card');
                let $adminCard = $('#lokerCards .loker-card-admin');
                let filtered = [];

                $cards.each(function() {
                    let $el = $(this);
                    let judul = $el.data('judul') || '';
                    let perusahaan = $el.data('perusahaan') || '';
                    let bidang = $el.data('bidang') || '';
                    let kerjasama = $el.data('kerjasama') || '';
                    let idPerusahaan = $el.data('perusahaan-id') || '';

                    // Search filter
                    let matchSearch = !filterState.search ||
                        judul.includes(filterState.search) ||
                        perusahaan.includes(filterState.search) ||
                        bidang.includes(filterState.search);

                    // Kerjasama filter
                    let matchKerjasama = filterState.kerjasama === 'all' ||
                        kerjasama === filterState.kerjasama;

                    // Bidang filter
                    let matchBidang = filterState.bidang === 'all' ||
                        bidang.includes(filterState.bidang);

                    // Perusahaan filter
                    let matchPerusahaan = filterState.perusahaan === 'all' ||
                        idPerusahaan == filterState.perusahaan;

                    if (matchSearch && matchKerjasama && matchBidang && matchPerusahaan) {
                        filtered.push($el);
                    }
                });

                let total = filtered.length;
                let totalPages = Math.ceil(total / filterState.perPage) || 1;
                if (filterState.currentPage > totalPages) filterState.currentPage = 1;

                let start = (filterState.currentPage - 1) * filterState.perPage;
                let end = start + filterState.perPage;

                // Hide all cards first
                $cards.hide();
                if ($adminCard.length) $adminCard.show();

                // Show filtered cards
                filtered.forEach(function($el, idx) {
                    if (idx >= start && idx < end) $el.show();
                });

                // Show/hide not found message
                if (filtered.length === 0) {
                    $('#notFoundLoker').show();
                } else {
                    $('#notFoundLoker').hide();
                }

                // Update results counter
                updateResultsCounter(filtered.length, total);

                // Render pagination
                renderPagination(filterState.currentPage, totalPages);
            }

            // Update results counter
            function updateResultsCounter(shown, total) {
                let counterText = '';
                if (total === 0) {
                    counterText = 'Tidak ada lowongan ditemukan';
                } else if (shown === total) {
                    counterText = `Terdapat ${total} lowongan`;
                } else {
                    let start = (filterState.currentPage - 1) * filterState.perPage + 1;
                    let end = Math.min(start + filterState.perPage - 1, total);
                    counterText = `Terdapat ${start}-${end} dari ${total} lowongan`;
                }
                $('#resultsCount').text(counterText);
            }

            // Render pagination
            function renderPagination(current, total) {
                let $ul = $('#paginationLoker');
                $ul.empty();

                if (total <= 1) return;

                // Previous button
                let prev = `<li class="page-item${current === 1 ? ' disabled' : ''}">
                <a class="page-link" href="#" data-page="${current - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>`;
                $ul.append(prev);

                // Page numbers
                let start = Math.max(1, current - 2);
                let end = Math.min(total, start + 4);
                if (end - start < 4) start = Math.max(1, end - 4);

                for (let i = start; i <= end; i++) {
                    $ul.append(`<li class="page-item${i === current ? ' active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`);
                }

                // Next button
                let next = `<li class="page-item${current === total ? ' disabled' : ''}">
                <a class="page-link" href="#" data-page="${current + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>`;
                $ul.append(next);
            }

            // Initialize the filter system
            init();
        });
    </script>
    <!--end::Script-->
</body>
<!--end::Body-->

</html>