<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>document.location.href = '../../index.php';</script>";
    exit;
}
require '../../src/functions.php';

// Tambahkan ini agar modal edit bisa menampilkan nama perusahaan
$daftarperusahaan = getPerusahaan();

// Cek apakah tombol tambah di klik
if (isset($_POST['tambah'])) {

    if (tambahLoker($_POST) > 0) {
        // SweetAlert untuk berhasil
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data Lowongan Kerja berhasil ditambahkan!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '../../pages/public/history-loker.php';
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
                window.location.href = '../../pages/public/history-loker.php';
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

function getHistoryLoker()
{
    global $conn;

    $query = "SELECT lowongan.*, perusahaan.nama_perusahaan, perusahaan.logo, perusahaan.alamat, perusahaan.bidang_usaha
              FROM lowongan
              JOIN perusahaan ON lowongan.id_perusahaan = perusahaan.id_perusahaan
              WHERE tanggal_ditutup < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
              ORDER BY lowongan.tanggal_ditutup DESC";
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

$historyLoker = getHistoryLoker();
?>

<!doctype html>
<html lang="en">

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

    /*K onten centering: no offset on mobile, offset on desktop */
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
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6 d-flex">
                            <h3 class="mb-0">History Loker</h3>
                        </div>
                    </div>
                    <!--end::Row-->

                    <!--begin::Search & PerPage Controls-->
                    <div class="row mb-2 mt-4 align-items-center g-2">
                        <div class="col-12 col-md-8">
                            <input type="search" id="searchLoker" class="form-control search-clearable" style="min-width:180px;" placeholder="Cari loker, perusahaan, bidang..." autocomplete="off">
                        </div>
                        <div class="perP col-12 col-md-4 mt-2 mt-md-0 d-flex justify-content-md-end">
                            <select id="perPage" class="form-select" style="max-width: 180px;">
                                <option value="3">3 data</option>
                                <option value="6">6 data</option>
                                <option value="12">12 data</option>
                                <option value="24">24 data</option>
                                <option value="48">48 data</option>
                                <option value="100">100 data</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Search & PerPage Controls-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row g-3" id="lokerList">
                        <?php
                        $historyLoker = getHistoryLoker();
                        ?>

                        <div class="row g-3 mt-3" id="lokerCards"><Wbr></Wbr>
                            <!-- Daftar Loker -->
                            <?php
                            foreach ($historyLoker as $loker) :
                                $alamat = $loker['alamat'];
                            ?>
                                <?php
                                $isTutup = strtotime($loker['tanggal_ditutup']) < time();
                                $isBelumBuka = strtotime($loker['tanggal_dibuka']) > time();
                                ?>
                                <div class="col-sm-6 col-xl-4 loker-card"
                                    data-judul="<?= htmlspecialchars(strtolower($loker['judul'])) ?>"
                                    data-perusahaan="<?= htmlspecialchars(strtolower($loker['nama_perusahaan'])) ?>"
                                    data-bidang="<?= htmlspecialchars(strtolower($loker['bidang_usaha'])) ?>">
                                    <div data-id="<?= $loker['id_lowongan'] ?>" class="card-click card job-card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between">
                                                <span>
                                                    <h5 class="card-title"><?= $loker['judul']; ?></h5>
                                                </span>
                                                <span class="text-muted"><strong><?= $loker['gaji_full']; ?></strong></span>
                                            </div>
                                            <div class="mb-3 mt-n1"><span class="badge bg-success p-2 text-uppercase"><?= $loker['bidang_usaha'] ?></span></div>
                                            <ul class="list-unstyled flex-grow-1">
                                                <li class="mb-2"><strong>Nama Perusahaan:</strong><br><span class="badge bg-primary"> <?= $loker['nama_perusahaan']; ?> </span></li>
                                                <li class="mb-2"><strong>Persyaratan:</strong>
                                                    <br>
                                                    <?php foreach ($loker['persyaratan'] as $persyaratan): ?>
                                                        <span class="badge text-bg-warning"><?= htmlspecialchars($persyaratan); ?></span>
                                                    <?php endforeach; ?>
                                                </li>
                                                <li class="mb-2"><strong>Deskripsi:</strong><br> <?= $loker['deskripsi']; ?></li>
                                            </ul>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <img src="../../src/assets/img/perusahaan/logo/<?= $loker['logo']; ?>" alt="Logo Perusahaan" class="img-thumbnail" style="max-width: 40px; max-height: 40px;">
                                                    <div class="mt-1 ms-2 d-flex flex-column">
                                                        <span class="mb-1 mapsLink"><?= '<a class="linkMaps icon-link icon-link-hover"  href="https://www.google.com/maps?q=' . urlencode($alamat) . '" target="_blank">' . $alamat . '</a>'; ?></span>
                                                        <div class="d-flex flex-column">
                                                            <?php
                                                            if ($isTutup) {
                                                                $tanggal_tutup = strtotime($loker['tanggal_ditutup']);
                                                                $hari_ini = strtotime(date('Y-m-d'));
                                                                $selisih_hari = ceil(($hari_ini - $tanggal_tutup) / 86400);
                                                            ?>
                                                                <span class="text-muted" style="font-size: 12px;">
                                                                    <strong>Sudah di Tutup <?= $selisih_hari ?> Hari Lalu</strong>
                                                                </span>
                                                            <?php } elseif ($isBelumBuka) {
                                                                $tanggal_dibuka = strtotime($loker['tanggal_dibuka']);
                                                                $hari_ini = strtotime(date('Y-m-d'));
                                                                $selisih_hari = ceil(($tanggal_dibuka - $hari_ini) / 86400);
                                                            ?>
                                                                <span class="text-muted" style="font-size: 12px;">
                                                                    <strong>Dibuka <?= $selisih_hari ?> Hari Lagi</strong>
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="time" style="font-size: 10px;">
                                                                    <?= $loker['tanggal_dibuka'] ?> -- <?= $loker['tanggal_ditutup'] ?>
                                                                </span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column align-items-center">
                                                    <?php if ($_SESSION['level'] == 'admin') : ?>
                                                        <span class="btn btn-group me-n3">
                                                            <a href="" class="btn btn-sm btn-outline-success mb-1 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $loker['id_lowongan']; ?>" data-bs-trigger="hover" data-bs-placement="top" data-bs-custom-class="custom-tooltip-Edit" data-bs-title="Edit ( Ubah )"><i class="fas fa-gear"></i></a>
                                                            <a href="../../src/config/hapus-dataloker.php?id=<?= $loker['id_lowongan'] ?>" class="btn btn-sm btn-outline-danger btn-hapus mb-1 ms-1" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip-Delete" data-bs-title="Delete ( Hapus )"><i class="fas fa-trash"></i></a>
                                                        </span>
                                                    <?php else : ?>
                                                        <?php if ($isTutup) : ?>
                                                            <a href="" class="btn btn-xs btn-primary" style="font-size:0.85rem;">Lainnya</a>
                                                        <?php elseif ($isBelumBuka) : ?>
                                                            <a href="" class="btn btn-xs btn-primary" style="font-size:0.85rem;">Lainnya</a>
                                                        <?php else : ?>
                                                            <a href="" data-bs-toggle="modal" data-bs-target="#modalSyarat<?= $loker['id_lowongan']; ?>" class="btn btn-sm px-4 btn-outline-primary">Lamar</a>

                                                        <?php endif; ?>
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
                    <!--begin::Row-->

                    <!-- /.row (main row) -->
                </div>
                <!--end::Container-->
            </div>
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
    <?php include '../../src/template/footer.php'; ?>

    <!-- Modal -->
    <!--begin::Modal Tambah Data-->
    <div class="modal fade" id="modalLoker" tabindex="-1" aria-labelledby="modalLokerLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLokerLabel">Tambah Lowongan Kerja</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group my-3">
                                <div class="input-group-text px-3"><span class="fas fa-user-tie fa-lg"></span></div>
                                <div class="form-floating">
                                    <input id="judul" type="judul" name="judul" class="form-control" placeholder="" required autocomplete="off" />
                                    <label for="judul" class="form-label">Judul</label>
                                </div>
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
                                <input type="hidden" name="kpn_gaji_diberi" id="kpn_gaji_diberi1" value="h">
                                <button class="btn btn-hidden border border-top dropdown-toggle fs-5" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="currencyPeriod1">/H</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('H', 1)">/Hari</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('M', 1)">/Minggu</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('B', 1)">/bulan</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('T', 1)">/Tahun</a></li>
                                </ul>
                                <div class="input-group-text px-3"><span class="fas fa-money-bill-wave fa-lg"></span></div>
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
    <?php foreach ($historyLoker as $loker) : ?>
        <div class="modal fade" id="modalEdit<?= $loker['id_lowongan']; ?>" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalEditLabel">Edit Lowongan Kerja</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="id_lowongan" value="<?= $loker['id_lowongan']; ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group my-3">
                                    <div class="input-group-text px-3"><span class="fas fa-user-tie fa-lg"></span></div>
                                    <div class="form-floating">
                                        <input id="judul" type="judul" name="judul" class="form-control" placeholder="" value="<?= $loker['judul'] ?>" autocomplete="off" />
                                        <label for="judul" class="form-label">Judul</label>
                                    </div>
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
                                        <input id="gaji<?= $loker['id_lowongan']; ?>" type="text" name="gaji" class="gaji form-control" placeholder="Masukkan Gaji" value="<?= $loker['gaji']; ?>" autocomplete="off" />
                                        <label for="gaji<?= $loker['id_lowongan']; ?>" class="form-label">Gaji</label>
                                    </div>
                                    <input type="hidden" name="kpn_gaji_diberi" id="kpn_gaji_diberi<?= $loker['id_lowongan']; ?>" value="<?= $loker['kpn_gaji_diberi']; ?>">
                                    <button class="btn btn-outline-secondary dropdown-toggle fs-5" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="currencyPeriod<?= $loker['id_lowongan']; ?>">
                                        <?= '/' . $loker['kpn_gaji_diberi']; ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('H', <?= $loker['id_lowongan']; ?>)">/Hari</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('M', <?= $loker['id_lowongan']; ?>)">/Minggu</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('B', <?= $loker['id_lowongan']; ?>)">/bulan</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="setkpn_gaji_diberi('T', <?= $loker['id_lowongan']; ?>)">/Tahun</a></li>
                                    </ul>
                                    <div class="input-group-text px-3"><span class="fas fa-money-bill-wave fa-lg"></span></div>
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
    <!-- OPTIONAL SCRIPTS -->

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

        // Reset form validation when modal is closed
        const modalTambah = document.getElementById('modalLoker');

        modalTambah.addEventListener('hidden.bs.modal', function() {
            const form = modalTambah.querySelector('form');
            form.classList.remove('was-validated'); // Hapus kelas validasi
            form.reset(); // Reset semua input di dalam form
        });
    </script>
    <!--end::Validation-->

    <!-- begin::Form -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modals = document.querySelectorAll('.modalLoker');

            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    // Reset form di dalam modal
                    const form = modal.querySelector('form');
                    if (form) {
                        form.reset();
                    }

                    // Kalau kamu pakai value dari PHP, perlu di-*refresh* datanya via AJAX atau reload
                    // Tapi kalau isiannya dari value HTML langsung, cukup pakai form.reset()
                });
            });
        });
    </script>
    <!-- end::Form -->

    <!-- Script Persyaratan -->
    <!-- Link ke JS Bootstrap dan jQuery -->
    <script>
        // Fungsi untuk menambah persyaratan
        document.getElementById('add-btn').addEventListener('click', function() {
            const persyaratanList = document.getElementById('persyaratan-list');

            // Hitung jumlah persyaratan yang sudah ada
            const persyaratanCount = persyaratanList.children.length + 1;

            // Membuat elemen baru untuk input
            const newPersyaratan = document.createElement('div');
            newPersyaratan.classList.add('input-container');


            // HTML untuk input persyaratan baru
            newPersyaratan.innerHTML = `
            <div class="input-group">
                <input type="text" name="persyaratan[]" class="form-control" placeholder="Tulis Persyaratan" required>
                <button type="button" class="btn btn-danger" onclick="removePersyaratan(this)">Hapus</button>
            </div><br>
            `;

            // Menambahkan input baru ke list
            persyaratanList.appendChild(newPersyaratan);
        });

        // Fungsi untuk menghapus input persyaratan
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
        <button type="button" class="btn btn-danger" onclick="removePersyaratan(${index})">Hapus</button>
    `;

            list.appendChild(newInput);
        }

        function removePersyaratan(button) {
            // Cari parent .input-container (tambah) atau .input-group (edit)
            let container = button.closest('.input-container') || button.closest('.input-group');
            if (container) container.remove();
        }
    </script>
    <!-- End Sript Persyaratan -->

    <!--begin::Nominal Gaji-->
    <script>
        // Ambil semua elemen dengan class 'input-gaji'
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
    </script>
    <!--end::Nominal Gaji-->

    <!-- Date -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d",
                allowInput: true
            });
        });
    </script>

    <!-- Begin::Currency Dropdown -->
    <script>
        function setkpn_gaji_diberi(value, id) {
            document.getElementById('currencyPeriod' + id).textContent = '/' + value.charAt(0).toUpperCase(); // Ubah tampilan di tombol
            document.getElementById('kpn_gaji_diberi' + id).value = value; // Simpan value asli
        }

        function setMataUang(value, id) {
            document.getElementById('currencyType' + id).textContent = value;
            document.getElementById('mata_uang' + id).value = value;
        }
    </script>
    <!-- End::Currency Dropdown -->

    <!-- Begin::Details -->
    <script>
        document.querySelectorAll('.card-click').forEach(card => {
            card.addEventListener('click', function(e) {
                // Cegah navigasi kalau klik tombol (yang ada <a> di dalamnya)
                if (e.target.closest('a')) return;

                const id = this.getAttribute('data-id');
                window.open(`../../src/config/detail_loker.php?id_lowongan=${id}`);
            });
        });
    </script>
    <!-- End::Details -->

    <script>
        $(function() {
            // --- Search & Clear Button ---
            $('#searchLoker').on('input search', function() {
                filterAndPaginate();
            });
            // Optional: clear on native X click (for browsers that don't fire 'input' on clear)
            $('#searchLoker').on('search', function() {
                filterAndPaginate();
            });

            // --- Per Page Dropdown ---
            $('#perPage').on('change', function() {
                filterAndPaginate();
            });

            // --- Pagination Click ---
            $('#paginationLoker').on('click', 'li.page-item:not(.disabled) a', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page !== undefined) {
                    window.currentPage = page;
                    filterAndPaginate();
                }
            });

            // --- Filtering, Pagination, and Rendering ---
            window.currentPage = 1;

            function filterAndPaginate() {
                let search = $('#searchLoker').val().toLowerCase();
                let perPage = parseInt($('#perPage').val());
                let $cards = $('#lokerCards .loker-card');
                let $adminCard = $('#lokerCards .loker-card-admin');
                let filtered = [];

                $cards.each(function() {
                    let $el = $(this);
                    let judul = $el.data('judul');
                    let perusahaan = $el.data('perusahaan');
                    let bidang = $el.data('bidang');
                    if (
                        search === '' ||
                        (judul && judul.includes(search)) ||
                        (perusahaan && perusahaan.includes(search)) ||
                        (bidang && bidang.includes(search))
                    ) {
                        filtered.push($el);
                    }
                });

                // Pagination
                let total = filtered.length;
                let totalPages = Math.ceil(total / perPage) || 1;
                if (window.currentPage > totalPages) window.currentPage = totalPages;
                let start = (window.currentPage - 1) * perPage;
                let end = start + perPage;

                // Hide all, then show filtered & paginated
                $cards.hide();
                if ($adminCard.length) $adminCard.show(); // always show admin add card
                filtered.forEach(function($el, idx) {
                    if (idx >= start && idx < end) $el.show();
                });

                // Tampilkan pesan jika tidak ada loker ditemukan
                if (filtered.length === 0) {
                    $('#notFoundLoker').show();
                } else {
                    $('#notFoundLoker').hide();
                }

                // Render pagination
                renderPagination(window.currentPage, totalPages);
            }

            function renderPagination(current, total) {
                let $ul = $('#paginationLoker');
                $ul.empty();
                if (total <= 1) return;

                let prev = `<li class="page-item${current === 1 ? ' disabled' : ''}">
                <a class="page-link" href="#" data-page="${current - 1}" tabindex="-1">&laquo;</a>
            </li>`;
                $ul.append(prev);

                // Show max 5 pages
                let start = Math.max(1, current - 2);
                let end = Math.min(total, start + 4);
                if (end - start < 4) start = Math.max(1, end - 4);

                for (let i = start; i <= end; i++) {
                    $ul.append(`<li class="page-item${i === current ? ' active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`);
                }

                let next = `<li class="page-item${current === total ? ' disabled' : ''}">
                <a class="page-link" href="#" data-page="${current + 1}">&raquo;</a>
            </li>`;
                $ul.append(next);
            }

            // --- Initial Render ---
            filterAndPaginate();
        });
    </script>
    <!--end::Script-->
</body>
<!--end::Body-->

</html>