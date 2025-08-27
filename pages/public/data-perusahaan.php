<?php
// Syarat menggunakan session
session_start();

// Cek apakah sudah ada session login, jika sudah kembalikan
if (!isset($_SESSION['id_pengguna'])) {
    echo "
        <script>
            document.location.href = '../../index.php';
        </script>
    ";
}

// Penghubung antar file di PHP
require '../../src/functions.php';

// Buat variabel untuk menampung hasil query
$perusahaan = [];
$query = mysqli_query($conn, "SELECT * FROM perusahaan ORDER BY id_perusahaan DESC");

while ($row = mysqli_fetch_assoc($query)) {
    $perusahaan[] = $row;
}

if (isset($_GET['email']) && $_GET['email'] === 'duplikat') {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: 'PERINGATAN!',
                text: 'Email Yang Anda Masukkan Sudah Terdaftar!',
                confirmButtonText: 'OK'
            }).then(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menambahkan',
                    text: 'Silahkan Gunakan Email Lain!',
                }).then(() => {
                    window.location.href = '../../pages/public/data-perusahaan.php';
                });
            });
        });
    </script>";
}

// Cek apakah tombol tambah di klik
if (isset($_POST['tambah'])) {

    if (tambahPerusahaan($_POST) > 0) {
        // SweetAlert untuk berhasil
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data Perusahaan berhasil ditambahkan!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '../../pages/public/data-perusahaan.php';
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
                    text: 'Data Perusahaan gagal ditambahkan!',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}

// Cek apakah tombol edit di klik
if (isset($_POST['edit'])) {
    if (editPerusahaan($_POST) !== false) {
        echo "
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Perusahaan berhasil diubah!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '../../pages/public/data-perusahaan.php';
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
                    text: 'Data Perusahaan gagal diubah!',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}
?>

<!doctype html>
<html lang="en">
<!--begin::Head-->

<?php
$title = "Perusahaan";
include '../../src/template/headers.php';
?>

<!--begin::Stylesheet-->
<style>
    body,
    .swal2-popup {
        font-family: "Poppins", sans-serif;
    }

    .dataTables_length {
        margin-top: 1rem;
    }

    .dataTables_filter {
        margin: 1rem;
    }

    .suggestions-box {
        position: absolute;
        background: white;
        border: 1px solid #ccc;
        max-height: 150px;
        overflow-y: auto;
        width: 100%;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .linkMaps {
        text-decoration: none;
    }

    @media (max-width: 576px) {
        .img-thumbnail {
            max-width: 60px !important;
        }

        .table-responsive {
            font-size: 0.95rem;
        }
    }
</style>
<link rel="stylesheet" href="../../src/assets/css/styletable.css">
<!-- end::Stylesheet -->
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
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header my-3">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3 class="mb-0 fw-bold font-monospace fs-1">Data Perusahaan</h3>
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
                    <!--begin::Row-->
                    <div class="row">
                        <div>
                            <!--begin::Card-->
                            <div class="card card-success card-outline">
                                <div class="card-header">
                                    <div class="card-title mt-1">
                                        <i class="fa-solid fa-id-card-clip me-1"></i>
                                        Data Perusahaan
                                    </div>
                                    <!-- Tombol Tambah Data -->
                                    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                        <i class="fas fa-plus me-1"></i>
                                        Tambah Data
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Tabel Data Admin -->
                                    <div class="table-responsive">
                                        <table id="example" class="table table-striped table-hover" style="width:100%">
                                            <thead class="table table-dark text-nowrap">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Logo</th>
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>Bidang Usaha</th>
                                                    <th>Alamat</th>
                                                    <th>No Telepon</th>
                                                    <th class="pe-none">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-group-divider text-nowrap">
                                                <?php
                                                $no = 1;
                                                foreach ($perusahaan as $company) :
                                                    $alamat = $company['alamat']; ?>
                                                    <tr>
                                                        <td class="text-center fw-bold"><?= $no++; ?></td>
                                                        <td> <img src="../../src/assets/img/perusahaan/logo/<?= $company['logo']; ?>" alt="Logo Perusahaan" class="img-thumbnail me-2" style="max-width: 100px;"></td>
                                                        <td><?= $company['nama_perusahaan']; ?></td>
                                                        <td><?= $company['email']; ?></td>
                                                        <td><?= $company['bidang_usaha']; ?></td>
                                                        <td><?= '<a class="linkMaps icon-link icon-link-hover" style="--bs-icon-link-transform: translate3d(0, -.150rem, 0); "  href="https://www.google.com/maps?q=' . urlencode($alamat) . '" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16"> <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/> </svg>' . $alamat . '</a>'; ?>
                                                        <td><?= $company['telepon']; ?></td>
                                                        <td class="text-nowrap align-items-center">
                                                            <a href="" id="btnLokasi" class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $company['id_perusahaan']; ?>" data-bs-trigger="hover" data-bs-placement="top" data-bs-custom-class="custom-tooltip-Edit" data-bs-title="Edit ( Ubah )">
                                                                <i class="fas fa-gear"></i>
                                                            </a>
                                                            <a href="../../src/config/hapus-dataperusahaan.php?id=<?= $company['id_perusahaan']; ?>" class="btn btn-sm btn-danger btn-hapus mb-1" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip-Delete" data-bs-title="Delete ( Hapus )">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card-->
                        </div>
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
    <!--begin::Modal Tambah Data-->
    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahLabel">Tambah Data Perusahaan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group my-3">
                                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                <div class="form-floating me-3">
                                    <input id="email" type="email" name="email" class="form-control" placeholder="" autocomplete="off" />
                                    <label for="email" class="form-label">Email</label>
                                </div>
                                <div class="form-floating">
                                    <input type="text" class="form-control telepone" name="telepon" id="telepon" maxlength="14" placeholder="" autocomplete="off">
                                    <label for="telepon" class="form-label">No Telepon</label>
                                </div>
                                <div class="input-group-text"><span class="fas fa-phone"></span></div>
                            </div>
                            <div class="input-group my-3">
                                <div class="input-group-text"><span class="fas fa-user"></span></div>
                                <div class="form-floating me-3">
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="" required autocomplete="off">
                                    <label for="nama" class="form-label">Nama</label>
                                </div>
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="bidang_usaha" id="bidang_usaha" placeholder="" required autocomplete="off">
                                    <label for="bidang_usaha" class="form-label">Bidang Usaha</label>
                                </div>
                                <div class="input-group-text"><span class="fas fa-briefcase"></span></div>
                                <div class="input-group my-3">
                                    <div class="input-group-text"><span class="fas fa-map-location-dot"></span></div>
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="alamat" id="alamat" placeholder="" required autocomplete="off">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <input type="hidden" id="lat" name="lat" value="<?= htmlspecialchars($company['latitude']) ?>">
                                        <input type="hidden" id="lng" name="lng" value="<?= htmlspecialchars($company['longitude']) ?>">

                                        <div id="suggestions" style="position: absolute; z-index: 1000;"></div>
                                        <div id="map" style="height: 400px; display: none;"></div>
                                    </div>
                                    <div class="input-group-text lokasi-icon" style="cursor:pointer;">
                                        <span class="fas fa-location-dot fa-bounce fa-lg"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="file" class="form-control" id="logo" name="logo">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="tambah" class="btn btn-primary">Simpan!</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end::Modal Tambah Data -->

    <?php foreach ($perusahaan as $company) : ?>
        <!-- Modal Edit -->
        <div class="modal fade" id="modalEdit<?= $company["id_perusahaan"]; ?>" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalEditLabel">Edit Data Perusahaan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post" class="needs-validation" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="id_perusahaan" id="id_perusahaan" value="<?= $company["id_perusahaan"]; ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group my-3">
                                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                    <div class="form-floating me-3">
                                        <input id="email" type="text" name="email" class="form-control" placeholder="" value="<?= $company["email"]; ?>" autocomplete="off" />
                                        <label for="email" class="form-label">Email</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="text" class="form-control telepone" name="telepon" id="telepon" maxlength="14" placeholder="" value="<?= $company["telepon"]; ?>" autocomplete="off">
                                        <label for="telepon" class="form-label">No Telepon</label>
                                    </div>
                                    <div class="input-group-text"><span class="fas fa-phone"></span></div>
                                </div>
                                <div class="input-group my-3">
                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                    <div class="form-floating me-3">
                                        <input type="text" class="form-control" name="nama" id="nama" placeholder="" value="<?= $company["nama_perusahaan"]; ?>" required autocomplete="off">
                                        <label for="nama" class="form-label">Nama</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="bidang_usaha" id="bidang_usaha" placeholder="" value="<?= $company["bidang_usaha"]; ?>" required autocomplete="off">
                                        <label for="bidang_usaha" class="form-label">Bidang Usaha</label>
                                    </div>
                                    <div class="input-group-text"><span class="fas fa-briefcase"></span></div>
                                    <div class="input-group mt-3 mb-2">
                                        <div class="input-group-text"><span class="fas fa-map-location-dot"></span></div>
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="alamat" id="alamat<?= $company["id_perusahaan"]; ?>" value="<?= $company["alamat"]; ?>" required autocomplete="off">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <input type="hidden" id="lat<?= $company['id_perusahaan']; ?>" name="lat" value="<?= htmlspecialchars($company['latitude']) ?>">
                                            <input type="hidden" id="lng<?= $company['id_perusahaan']; ?>" name="lng" value="<?= htmlspecialchars($company['longitude']) ?>">
                                            <div id="suggestions<?= $company["id_perusahaan"]; ?>" style="position: absolute; z-index: 1000;"></div>
                                            <div id="map<?= $company["id_perusahaan"]; ?>" style="height: 400px; display: none;"></div>
                                        </div>
                                        <div class="input-group-text lokasi-icon" data-id="<?= $company["id_perusahaan"]; ?>" style="cursor:pointer;">
                                            <span class="fas fa-location-dot fa-bounce fa-lg"></span>
                                        </div>

                                    </div>
                                </div>
                                <div>
                                    <img src="../../src/assets/img/perusahaan/logo/<?= $company['logo']; ?>" alt="Logo Perusahaan" class="img-thumbnail mb-1" style="max-width: 100px;">
                                </div>
                                <div class="input-group my-3">
                                    <input type="file" class="form-control" id="logo" name="logo">
                                    <input type="hidden" name="logoLama" value="<?= $company['logo']; ?>">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="edit" class="btn btn-primary">Simpan!</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>


    <!--begin::Script-->
    <?php
    include '../../src/template/footer.php';
    ?>
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
        const modalTambah = document.getElementById('modalTambah');

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
            const modals = document.querySelectorAll('.modal');

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

    <!--begin::No Phone-->
    <script>
        document.querySelectorAll('.telepone').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                let formattedValue = '';
                for (let i = 0; i < value.length; i++) {
                    if ((i === 4 || (i > 4 && (i - 4) % 4 === 0)) && formattedValue.split('-').length <= 4) {
                        formattedValue += '-';
                    }
                    formattedValue += value[i];
                }
                e.target.value = formattedValue;
            });
        });
    </script>
    <!--end::No Phone-->

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

    <!-- SweetAlert2 CDN -->
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

    <!-- Mencagah SweetAlert Infinite Loop -->
    <script>
        if (window.location.search.includes("email=duplikat")) {
            history.replaceState({}, document.title, window.location.pathname);
        }
    </script>

    <!-- Script Maps Edit -->
    <script>
        <?php foreach ($perusahaan as $company) : ?>
                (function() {
                    var id = <?= $company['id_perusahaan'] ?>;
                    var mapContainer = document.getElementById('map' + id);
                    var icon = document.querySelector('.lokasi-icon[data-id="' + id + '"]');
                    var alamatInput = document.getElementById('alamat' + id);
                    var latInput = document.getElementById('lat' + id);
                    var lngInput = document.getElementById('lng' + id);
                    var suggestionsBox = document.getElementById('suggestions' + id);

                    var lat = parseFloat(latInput.value) || -6.2;
                    var lng = parseFloat(lngInput.value) || 106.816666;

                    var map = L.map('map' + id, {
                        maxZoom: 19
                    }).setView([lat, lng], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19
                    }).addTo(map);

                    var marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map);

                    function setMarkerAndView(lat, lon, popupText) {
                        marker.setLatLng([lat, lon]);
                        map.setView([lat, lon], 17);
                        if (popupText) marker.bindPopup(popupText).openPopup();
                        else marker.closePopup();
                    }

                    // Fungsi debounce
                    function debounce(func, wait) {
                        let timeout;
                        return function(...args) {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => func.apply(this, args), wait);
                        };
                    }

                    // Ambil nama ringkas dari hasil Nominatim
                    function getShortName(item) {
                        if (!item) return '';
                        var a = item.address || {};
                        var name = a.amenity || a.building || a.house || a.attraction || a.shop || a.tourism || a.name;
                        if (name) return name;
                        var road = a.road || a.pedestrian || a.cycleway;
                        var locality = a.village || a.town || a.city || a.suburb || a.hamlet;
                        if (road && locality) return road + ', ' + locality;
                        if (road) return road;
                        if (locality) return locality;
                        if (item.display_name) return item.display_name.split(',')[0].trim();
                        return '';
                    }

                    // Tampilkan suggestions autocomplete
                    function showSuggestions(items) {
                        suggestionsBox.innerHTML = '';
                        if (!items || items.length === 0) {
                            suggestionsBox.style.display = 'none';
                            return;
                        }
                        suggestionsBox.style.display = 'block';
                        items.forEach((it, idx) => {
                            const div = document.createElement('div');
                            div.style.padding = '6px 10px';
                            div.style.cursor = 'pointer';
                            div.style.background = idx % 2 === 0 ? 'rgba(255,255,255,0.95)' : 'rgba(245,245,245,0.95)';

                            const short = getShortName(it) || it.display_name.split(',')[0];
                            const context = (it.address && (it.address.city || it.address.town || it.address.village)) ?
                                ' — ' + (it.address.city || it.address.town || it.address.village) : '';
                            div.textContent = short + context;

                            div.addEventListener('click', () => {
                                alamatInput.value = short;
                                latInput.value = it.lat;
                                lngInput.value = it.lon;
                                setMarkerAndView(it.lat, it.lon, short);
                                showSuggestions([]);
                            });
                            suggestionsBox.appendChild(div);
                        });
                    }

                    // Cari alamat via proxy-maps.php
                    async function searchAddress(q) {
                        if (!q || q.length < 2) {
                            showSuggestions([]);
                            return;
                        }
                        try {
                            const res = await fetch(`proxy-maps.php?q=${encodeURIComponent(q)}&addressdetails=1&limit=5`);
                            if (!res.ok) throw new Error('Network response not ok');
                            const data = await res.json();
                            showSuggestions(data);
                        } catch (err) {
                            console.error('Search error:', err);
                            showSuggestions([]);
                        }
                    }

                    // Debounce untuk input alamat
                    const debouncedSearch = debounce(() => {
                        const q = alamatInput.value.trim();
                        searchAddress(q);
                    }, 20);

                    alamatInput.addEventListener('input', debouncedSearch);

                    // Toggle tampilan peta saat klik tombol
                    icon.addEventListener('click', function() {
                        if (mapContainer.style.display === 'none' || mapContainer.style.display === '') {
                            mapContainer.style.display = 'block';
                            map.invalidateSize();
                        } else {
                            mapContainer.style.display = 'none';
                        }
                    });

                    // Klik pada peta: update marker, lat/lng, reverse geocode
                    marker.on('dragend', function(e) {
                        var pos = e.target.getLatLng();
                        latInput.value = pos.lat;
                        lngInput.value = pos.lng;

                        // Update alamat via reverse geocode
                        fetch(`proxy-maps.php?lat=${pos.lat}&lon=${pos.lng}&addressdetails=1`)
                            .then(res => res.json())
                            .then(data => {
                                var short = getShortName(data) || data.display_name || '';
                                alamatInput.value = short;
                            });
                    });

                    map.on('click', function(e) {
                        var lat = e.latlng.lat;
                        var lon = e.latlng.lng;

                        marker.setLatLng(e.latlng);
                        latInput.value = lat;
                        lngInput.value = lon;

                        // Update alamat via reverse geocode
                        fetch(`proxy-maps.php?lat=${lat}&lon=${lon}&addressdetails=1`)
                            .then(res => res.json())
                            .then(data => {
                                var short = getShortName(data) || data.display_name || '';
                                alamatInput.value = short;
                            });
                    });

                    // Event change pada input alamat: forward geocode, update map & marker
                    alamatInput.addEventListener('change', function() {
                        var addr = this.value.trim();
                        if (addr === '') return;
                        fetch(`proxy-maps.php?q=${encodeURIComponent(addr)}&addressdetails=1&limit=1`)
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    var latn = parseFloat(data[0].lat);
                                    var lonn = parseFloat(data[0].lon);
                                    latInput.value = latn;
                                    lngInput.value = lonn;
                                    setMarkerAndView(latn, lonn, getShortName(data[0]) || data[0].display_name);
                                }
                            })
                            .catch(() => {});
                    });

                    // Sembunyikan suggestion jika klik di luar input dan suggestions box
                    document.addEventListener('click', function(ev) {
                        if (!alamatInput.contains(ev.target) && !suggestionsBox.contains(ev.target)) {
                            suggestionsBox.style.display = 'none';
                        }
                    });

                })();
        <?php endforeach; ?>
    </script>
    <!--end::Script-->
</body>
<!--end::Body-->

</html>