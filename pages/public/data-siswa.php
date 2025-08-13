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

$studen = [];
$level = $_SESSION['level'];
$id_user = $_SESSION['id_pengguna'];

if ($level === 'admin') {
    $query = mysqli_query($conn, "
        SELECT * FROM alumni
        INNER JOIN user ON alumni.kode_alumni = user.kode_pengguna
        ORDER BY alumni.nama ASC
    ");
} elseif ($level === 'alumni') {
    // Ambil jurusan user ini
    $qjurusan = mysqli_query($conn, "
        SELECT jurusan FROM alumni
        INNER JOIN user ON alumni.kode_alumni = user.kode_pengguna
        WHERE user.id_user = $id_user
    ");
    $row = mysqli_fetch_assoc($qjurusan);
    $jurusan_siswa = $row['jurusan'] ?? '';

    // Ambil teman sejurusan tanpa dirinya sendiri
    $query = mysqli_query($conn, "
        SELECT * FROM alumni
        INNER JOIN user ON alumni.kode_alumni = user.kode_pengguna
        WHERE alumni.jurusan = '$jurusan_siswa' 
        ORDER BY alumni.nama ASC
    ");
}

while ($row = mysqli_fetch_assoc($query)) {
    $studen[] = $row;
}

// Pindahkan user yang login ke urutan pertama
usort($studen, function ($a, $b) use ($id_user) {
    return ($b['id_user'] == $id_user) <=> ($a['id_user'] == $id_user);
});


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
                    window.location.href = '../../pages/public/data-siswa.php';
                });
            });
        });
    </script>";
}

// Cek apakah tombol tambah di klik
if (isset($_POST['tambah'])) {

    if (tambahSiswa($_POST) > 0) {
        // SweetAlert untuk berhasil
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data Alumni berhasil ditambahkan!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '../../pages/public/data-siswa.php';
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
                    text: 'Data Alumni gagal ditambahkan!',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}

// Cek apakah tombol setting akun di klik
if (isset($_POST['settingAkun'])) {
    if (settingAkunAlumni($_POST) > 0) {
        // SweetAlert untuk berhasil
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Setting Akun berhasil!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '../../pages/public/data-siswa.php';
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
                    text: 'Setting Akun gagal!',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}

// Cek apakah tombol edit di klik
if (isset($_POST['edit'])) {
    if (editSiswa($_POST) !== false) {
        echo "
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Alumni berhasil diubah!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '../../pages/public/data-siswa.php';
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
                    text: 'Data Alumni gagal diubah!',
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
$title = "Alumni";
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
                                <a href="../../logout.php" class="btn btn-default btn-flat float-end btn-logout" data-bs-trigger="hover" data-bs-placement="left" data-bs-custom-class="custom-tooltip-logout" data-bs-title="LogOut ( Keluar )"><i class="bi bi-box-arrow-right"></i></a>
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
                            <h3 class="mb-0 fw-bold font-monospace fs-1">Data Alumni</h3>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <?php if ($level == 'admin') : ?>
                <div class="my-2 text-center">
                    <!-- Tombol Export Import To Excel -->
                    <a href="../../src/config/exportExcel.php" class="btn btn-success mb-1 me-1">
                        <i class="fas fa-file-export me-1"></i>
                        Export Excel
                    </a>
                    <a href="../../src/assets/file_excel/Struktur_Excel_Data_Alumni.xlsx" class="btn btn-warning mb-1 me-1" download>
                        <i class="fas fa-download me-1"></i>
                        Struktur Excel
                    </a>
                    <button type="button" class="btn btn-danger mb-1 ms-1" data-bs-toggle="modal" data-bs-target="#modalImpor">
                        <i class="fas fa-file-import me-1"></i>
                        Import Excel
                    </button>
                </div>
            <?php endif; ?>
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div>
                            <!--begin::Card-->
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <div class="card-title mt-1">
                                        <i class="fa-solid fa-id-card-clip me-1"></i>
                                        Data Alumni
                                    </div>
                                    <?php if ($level === 'admin') : ?>
                                        <!-- Tombol Tambah Data -->
                                        <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                            <i class="fas fa-plus me-1"></i>
                                            Tambah Data
                                        </button>
                                    <?php else : ?>
                                        <i class="fa-solid fa-users-between-lines float-end mt-2 fs-4"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <!-- Tabel Data Siswa -->
                                    <div class="table-responsive">
                                        <table id="example" class="table table-striped table-hover" style="width:100%">
                                            <thead class="table table-dark text-nowrap">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Email</th>
                                                    <th>Nama</th>
                                                    <th>Nisn</th>
                                                    <th>Jurusan</th>
                                                    <th>Alamat</th>
                                                    <th>No Telepon</th>
                                                    <th>Tahun Lulus</th>
                                                    <th class="pe-none">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-group-divider text-nowrap">
                                                <?php
                                                $no = 1;
                                                foreach ($studen as $siswa) : ?>
                                                    <tr>
                                                        <td class="text-center fw-bold"><?= $no++; ?></td>
                                                        <td><?= $siswa['email']; ?></td>
                                                        <td><?= $siswa['nama']; ?></td>
                                                        <td><?= $siswa['nisn']; ?></td>
                                                        <td><?= strtoupper($siswa['jurusan']); ?></td>
                                                        <td><?= $siswa['alamat']; ?></td>
                                                        <td><?= $siswa['telepon']; ?></td>
                                                        <td><?= date('d-m-Y', strtotime($siswa['tahun_lulus'])); ?></td>
                                                        <?php if ($level === 'admin') : ?>
                                                            <td>
                                                                <a href="" class="btn btn-sm btn-info text-white mb-1" data-bs-toggle="modal" data-bs-target="#modalUser<?= $siswa['kode_alumni']; ?>" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip-User" data-bs-title="Account ( Akun )">
                                                                    <i class="fas fa-user"></i>
                                                                </a>
                                                                <a href="" class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $siswa['id_alumni']; ?>" data-bs-trigger="hover" data-bs-placement="top" data-bs-custom-class="custom-tooltip-Edit" data-bs-title="Edit ( Ubah )">
                                                                    <i class="fas fa-gear"></i>
                                                                </a>
                                                                <a href="../../src/config/hapus-datasiswa.php?id=<?= $siswa['id_alumni']; ?>" class="btn btn-sm btn-danger btn-hapus mb-1" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip-Delete" data-bs-title="Delete ( Hapus )">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        <?php else : ?>
                                                            <td>
                                                                <a href="" class="btn btn-sm btn-info text-white mb-1" data-bs-toggle="modal" data-bs-target="#modalUser<?= $siswa['kode_alumni']; ?>" data-user-id="<?= $siswa['id_user'] ?>" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip-User" data-bs-title="Account ( Akun )">
                                                                    <i class="fas fa-user"></i>
                                                                </a>
                                                            </td>
                                                        <?php endif; ?>
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
    <!--begin::Modal Impor -->
    <div class="modal fade" id="modalImpor" tabindex="-1">
        <div class="modal-dialog">
            <form action="../../src/config/importExcel.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data <b>Siswa</b> Dari Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="xls" class="fw-semibold mb-2 ms-1">Import File Excel</label>
                            <input class="form-control" type="file" name="file_excel" accept=".xls,.xlsx" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="import" class="btn btn-primary">Import Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End::Modal Impor -->
    <!--begin::Modal Tambah Data-->
    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahLabel">Tambah Data Alumni</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group my-3">
                                <div class="input-group-text"><span class="fas fa-rectangle-list"></span></div>
                                <div class="form-floating me-3">
                                    <input id="nisn" type="text" name="nisn" class="form-control" placeholder="" required autocomplete="off" />
                                    <label for="nisn" class="form-label">Nisn</label>
                                </div>
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="telepon" id="telepon" maxlength="14" placeholder="" required autocomplete="off">
                                    <label for="telepon" class="form-label">No Telepon</label>
                                </div>
                                <div class="input-group-text"><span class="fas fa-phone"></span></div>
                            </div>
                            <div class="input-group my-3">
                                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                <div class="form-floating me-3">
                                    <input id="email" type="email" name="email" class="form-control" placeholder="" required autocomplete="off" />
                                    <label for="email" class="form-label">Email</label>
                                </div>
                                <!-- <div class="form-floating">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="8" required autocomplete="off">
                                    <label for="password" class="form-label">Password</label>
                                </div>
                                <div class="input-group-text"><span class="fas fa-lock"></span></div> -->
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="" required autocomplete="off">
                                    <label for="nama" class="form-label">Nama</label>
                                </div>
                                <div class="input-group-text"><span class="fas fa-user"></span></div>
                            </div>
                            <!-- <div class="input-group my-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="username" id="username" placeholder="" required autocomplete="off">
                                    <label for="username" class="form-label">UserName</label>
                                </div>
                                
                            </div> -->
                            <div class="input-group my-3">
                                <div class="input-group-text"><span class="fas fa-tags"></span></div>
                                <div class="form-floating me-3">
                                    <select class="form-select form-select p-2" aria-label="Default select example" name="jurusan" id="jurusan" required>
                                        <option value="" selected disabled hidden>Pilih Jurusan Anda</option>
                                        <option value="rpl">RPL</option>
                                        <option value="kuliner">KULINER</option>
                                        <option value="atph">ATPH</option>
                                        <option value="busana">BUSANA</option>
                                    </select>
                                </div>
                                <div class="form-floating flex-grow-1">
                                    <input type="date" class="form-control flatpickr" id="tahun_lulus" name="tahun_lulus" placeholder="" required autocomplete="off" data-date-format="Y-m-d">
                                    <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                                </div>
                                <div class="input-group-text"><label for="tahun_lulus" class="fas fa-clock fa-shake fa-lg"></label></div>
                            </div>
                            <div class="input-group my-3">
                                <div class="input-group-text"><span class="fas fa-map-location-dot"></span></div>
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="alamat" id="alamat" placeholder="" required autocomplete="off">
                                    <label for="alamat" class="form-label">Alamat</label>
                                </div>
                                <div class="input-group-text"><span class="fas fa-location-dot fa-bounce fa-lg"></span></div>
                            </div>
                            <span class="fw-bold text-center mt-3 mb-1">
                                <p>-- Tambahkan Akun Alumni --</p>
                            </span>
                            <div class="input-group">
                                <div class="input-group-text px-3"><span class="fas fa-user"></span></div>
                                <div class="form-floating me-3">
                                    <input type="text" class="form-control" name="username" id="username" placeholder="" value="" required autocomplete="off">
                                    <label for="username" class="form-label">Username</label>
                                </div>
                                <div class="form-floating">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="8" required autocomplete="off">
                                    <label for="password" class="form-label">Password</label>
                                </div>
                                <div class="input-group-text px-3"><span class="fas fa-lock"></span></div>
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
    <!-- end::Modal Tambah Data -->

    <?php foreach ($studen as $siswa) : ?>
        <div class="modal fade" id="modalUser<?= $siswa["kode_alumni"]; ?>" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalTambahLabel">Setting Akun</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="kode_alumni" id="kode_alumni" value="<?= $siswa["kode_alumni"]; ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group my-3">
                                    <div class="input-group-text px-3"><span class="fas fa-user"></span></div>
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="username" id="username" placeholder="" value="<?= $siswa["username"]; ?>" required autocomplete="off">
                                        <label for="username" class="form-label">Username</label>
                                    </div>
                                </div>
                                <div class="input-group my-3">
                                    <div class="input-group-text px-3"><span class="fas fa-lock"></span></div>
                                    <div class="form-floating">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="8" autocomplete="off">
                                        <label for="password" class="form-label">Password</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="settingAkun" class="btn btn-primary">Simpan!</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="modalEdit<?= $siswa["id_alumni"]; ?>" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalEditLabel">Edit Data Alumni</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="id_alumni" id="id_alumni" value="<?= $siswa["id_alumni"]; ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group my-3">
                                    <div class="input-group-text"><span class="fas fa-rectangle-list"></span></div>
                                    <div class="form-floating me-3">
                                        <input id="nisn<?= $siswa["id_alumni"]; ?>" type="text" name="nisn" class="form-control" placeholder="" value="<?= $siswa["nisn"]; ?>" required autocomplete="off" />
                                        <label for="nisn<?= $siswa["id_alumni"]; ?>" class="form-label">Nisn</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="telepon" id="telepon<?= $siswa["id_alumni"]; ?>" maxlength="14" placeholder="" value="<?= $siswa["telepon"]; ?>" required autocomplete="off">
                                        <label for="telepon<?= $siswa["id_alumni"]; ?>" class="form-label">No Telepon</label>
                                    </div>
                                    <div class="input-group-text"><span class="fas fa-phone"></span></div>
                                </div>
                                <div class="input-group my-3">
                                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                    <div class="form-floating me-3">
                                        <input id="email<?= $siswa["id_alumni"]; ?>" type="email" name="email" class="form-control" placeholder="" value="<?= $siswa["email"]; ?>" required autocomplete="off" />
                                        <label for="email<?= $siswa["id_alumni"]; ?>" class="form-label">Email</label>
                                    </div>
                                    <!-- <div class="form-floating">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="8" required autocomplete="off">
                                    <label for="password" class="form-label">Password</label>
                                </div>
                                <div class="input-group-text"><span class="fas fa-lock"></span></div> -->
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="nama" id="nama<?= $siswa["id_alumni"]; ?>" placeholder="" value="<?= $siswa["nama"]; ?>" required autocomplete="off">
                                        <label for="nama<?= $siswa["id_alumni"]; ?>" class="form-label">Nama</label>
                                    </div>
                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                </div>
                                <!-- <div class="input-group my-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="username" id="username" placeholder="" required autocomplete="off">
                                    <label for="username" class="form-label">UserName</label>
                                </div>
                                
                            </div> -->
                                <div class="input-group my-3">
                                    <div class="input-group-text"><span class="fas fa-tags"></span></div>
                                    <div class="form-floating me-3">
                                        <select class="form-select form-select p-2" aria-label="Default select example" name="jurusan" id="jurusan<?= $siswa["id_alumni"]; ?>" required>
                                            <option value="" selected disabled hidden>Pilih Jurusan Anda</option>
                                            <option value="rpl" <?php if ($siswa["jurusan"] == "rpl") {
                                                                    echo "selected";
                                                                } ?>>RPL</option>
                                            <option value="kuliner" <?php if ($siswa["jurusan"] == "kuliner") {
                                                                        echo "selected";
                                                                    } ?>>KULINER</option>
                                            <option value="atph" <?php if ($siswa["jurusan"] == "atph") {
                                                                        echo "selected";
                                                                    } ?>>ATPH</option>
                                            <option value="busana" <?php if ($siswa["jurusan"] == "busana") {
                                                                        echo "selected";
                                                                    } ?>>BUSANA</option>
                                        </select>
                                    </div>
                                    <div class="form-floating flex-grow-1">
                                        <input type="date" class="form-control flatpickr" id="tahun_lulus<?= $siswa["id_alumni"]; ?>" name="tahun_lulus" placeholder="" required autocomplete="off" data-date-format="Y-m-d" value="<?= $siswa["tahun_lulus"]; ?>">
                                        <label for="tahun_lulus<?= $siswa["id_alumni"]; ?>" class="form-label">Tahun Lulus</label>
                                    </div>
                                    <div class="input-group-text"><label for="tahun_lulus<?= $siswa["id_alumni"]; ?>" class="fas fa-clock fa-shake fa-lg"></label></div>
                                </div>
                                <div class="input-group my-3">
                                    <div class="input-group-text"><span class="fas fa-map-location-dot"></span></div>
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="alamat" id="alamat<?= $siswa["id_alumni"]; ?>" placeholder="" value="<?= $siswa["alamat"]; ?>" required autocomplete="off">
                                        <label for="alamat<?= $siswa["id_alumni"]; ?>" class="form-label">Alamat</label>
                                    </div>
                                    <div class="input-group-text"><span class="fas fa-location-dot fa-bounce fa-lg"></span></div>
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
        <!-- end::Modal Edit Data -->
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
        // Untuk semua input telepon (tambah & edit)
        document.querySelectorAll('input[name="telepon"]').forEach(function(el) {
            el.addEventListener('input', function(e) {
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

    <!-- Nis Yang cuma bisa angka -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi NISN hanya angka (tambah & edit)
            document.querySelectorAll('input[name="nisn"]').forEach(function(el) {
                el.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });
        });
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

                    const siswaId = this.dataset.id;
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

    <!-- hanya pemilik yang bisa mengakses akunnya -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const idLogin = <?= json_encode($_SESSION['id_pengguna']) ?>;
            const level = <?= json_encode($_SESSION['level']) ?>;

            // Tangkap event sebelum modal tampil
            const modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const targetUserId = button.getAttribute('data-user-id');

                    if (targetUserId !== idLogin && level !== 'admin') {
                        // Bukan user pemilik akun → batalkan modal
                        Swal.fire({
                            title: "Gagal",
                            text: "Anda Bukan Pemilik Akun!",
                            icon: "warning",
                            confirmButtonText: "OK"
                        });
                        event.preventDefault();
                    }
                });
            });
        });
    </script>


    <!--end::Script-->
</body>
<!--end::Body-->

</html>