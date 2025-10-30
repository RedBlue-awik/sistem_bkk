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

// Cek apakah tombol edit di klik
if (isset($_POST['edit'])) {
    if (setStatusLamaran($_POST) !== false) {
        echo "
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Status Siswa berhasil di simpan!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '../../pages/public/lamaran.php';
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
                    text: 'Status Siswa gagal di simpan!',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}

$level = $_SESSION['level'];
$id_user = $_SESSION['id_pengguna'];

// Inisialisasi $no
$no = 1;

if ($level == 'admin') {
    $query = mysqli_query($conn, "
    SELECT lamaran.*, 
           alumni.nama AS nama_siswa,
           lowongan.judul AS judul_lowongan, 
           lowongan.gaji, 
           lowongan.tanggal_ditutup,
           lowongan.status_kerjasama,
           perusahaan.nama_perusahaan,
           perusahaan.bidang_usaha
    FROM lamaran
    JOIN alumni ON lamaran.id_siswa = alumni.id_alumni
    JOIN lowongan ON lamaran.id_lowongan = lowongan.id_lowongan
    JOIN perusahaan ON lowongan.id_perusahaan = perusahaan.id_perusahaan
");
} else {
    $result = mysqli_query($conn, "
    SELECT id_alumni FROM alumni
        INNER JOIN user ON alumni.kode_alumni = user.kode_pengguna
        WHERE user.id_user = $id_user
        ");
    $row = mysqli_fetch_assoc($result);
    $id_alumni = $row['id_alumni'];
    $query = mysqli_query($conn, "
    SELECT lamaran.*, 
           alumni.nama AS nama_siswa,
           lowongan.judul AS judul_lowongan, 
           lowongan.gaji, 
           lowongan.tanggal_ditutup,
           lowongan.status_kerjasama,
           perusahaan.nama_perusahaan,
           perusahaan.bidang_usaha
    FROM lamaran
    JOIN alumni ON lamaran.id_siswa = alumni.id_alumni
    JOIN lowongan ON lamaran.id_lowongan = lowongan.id_lowongan
    JOIN perusahaan ON lowongan.id_perusahaan = perusahaan.id_perusahaan
    WHERE lamaran.id_siswa = $id_alumni
");
}

// Ambil semua data lamaran ke array
$dataLamaran = [];
while ($row = mysqli_fetch_assoc($query)) {
    $dataLamaran[] = $row;
}

// Ambil semua status unik untuk dropdown filter
$statusList = [];
foreach ($dataLamaran as $row) {
    if (!in_array($row['status'], $statusList) && !empty($row['status'])) {
        $statusList[] = $row['status'];
    }
}
sort($statusList);
?>

<!doctype html>
<html lang="en">
<!--begin::Head-->

<?php
$title = "Data Lamaran";
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

    .btn-status-toggle.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    .dropdown-item.active {
        background-color: #0d6efd;
        color: white;
    }
</style>
<link rel="stylesheet" href="../../src/assets/css/styletable.css">
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
                            <h3 class="mb-0 fw-bold font-monospace fs-1">Data Lamaran Siswa</h3>
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
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <div class="card-title mt-1">
                                        <i class="fa-solid fa-id-card-clip me-1"></i>
                                        Data Lamaran
                                    </div>
                                    <!-- Tombol Filter hanya untuk admin -->
                                    <?php if ($level == 'admin') : ?>
                                        <div class="float-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="filterStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-filter me-1"></i> Semua Status
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="filterStatusDropdown">
                                                    <li><a class="dropdown-item filter-option active" href="#" data-status="all">Semua Status</a></li>
                                                    <?php foreach ($statusList as $status) : ?>
                                                        <li><a class="dropdown-item filter-option" href="#" data-status="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <i class="fa-solid fa-envelope-open-text float-end mt-2 fs-4 me-2"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <!-- Tabel Data Siswa -->
                                    <div class="table-responsive">
                                        <table id="lamaranTable" class="table table-striped table-hover" style="width:100%">
                                            <thead class="table table-dark text-nowrap">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Status Lamaran</th>
                                                    <th>Status Kerjasama</th>
                                                    <th>Nama Siswa</th>
                                                    <th>Perusahaan</th>
                                                    <th>Judul</th>
                                                    <th>Bidang Usaha</th>
                                                    <th>Tanggal Lamar</th>
                                                    <th>File Cv</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-group-divider text-nowrap">
                                                <?php if (count($dataLamaran) > 0) : ?>
                                                    <?php foreach ($dataLamaran as $row) : ?>
                                                        <tr>
                                                            <td class="text-center fw-bold"><?= $no++; ?></td>
                                                            <td>
                                                                <?= $row['status'] ?: "<span class='text-muted'>Tidak Ada Status</span>" ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                // Cek apakah key status_kerjasama ada dan tidak null
                                                                if (isset($row['status_kerjasama']) && $row['status_kerjasama'] !== null) {
                                                                    echo $row['status_kerjasama'] == 'bekerja_sama' ? 'Bekerja Sama' : 'Tidak Bekerja Sama';
                                                                } else {
                                                                    echo 'Tidak Bekerja Sama';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?= $row['nama_siswa']; ?></td>
                                                            <td><?= $row['nama_perusahaan']; ?></td>
                                                            <td><?= $row['judul_lowongan']; ?></td>
                                                            <td><?= $row['bidang_usaha']; ?></td>
                                                            <td><?= date('d-m-Y', strtotime($row['tanggal_lamar'])); ?></td>
                                                            <td>
                                                                <?php
                                                                $cv_path = null;
                                                                $allowed_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
                                                                $nama_siswa_singkat = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($row['nama_siswa']));
                                                                foreach ($allowed_ext as $ext) {
                                                                    $filename = "cv_" . $nama_siswa_singkat . "_" . $row['id_lowongan'] . "." . $ext;
                                                                    $filepath = "../../src/assets/persyaratan/cv/" . $filename;
                                                                    if (file_exists($filepath)) {
                                                                        $cv_path = $filepath;
                                                                        break;
                                                                    }
                                                                }
                                                                if ($cv_path) : ?>
                                                                    <a href="<?= $cv_path ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                                        <i class="fas fa-download"></i> Download CV
                                                                    </a>
                                                                <?php else : ?>
                                                                    <span class="text-danger">Tidak Ada CV</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-nowrap align-items-center justify-content-center d-flex gap-1">
                                                                <?php if ($level == 'admin') : ?>
                                                                    <?php if ($row['status']) : ?>
                                                                        <a href="" class="btn btn-sm btn-info text-white mb-1" style="padding-left: .4rem; padding-right: .3rem;" data-bs-toggle="modal" data-bs-target="#modalStatus<?= $row['id_lamaran']; ?>" data-bs-trigger="hover" data-bs-placement="top" data-bs-custom-class="custom-tooltip-User" data-bs-title="Set Status">
                                                                            <i class="fas fa-user-tag"></i>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>

                                                                <a href="./detail_loker.php?id_lowongan=<?= $row['id_lowongan']; ?>" class="btn btn-sm text-white mb-1" style="background-color: #11224E;" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip-Detail" data-bs-title="Detail ( Lihat Loker )">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>

                                                                <a href="../../src/config/hapus-datalamaran.php?id=<?= $row['id_lamaran']; ?>" class="btn btn-sm btn-danger btn-hapus mb-1" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip-Delete" data-bs-title="Delete ( Hapus )">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="10" class="text-center py-4 text-muted">
                                                            <i class="fas fa-search fa-2x mb-2"></i>
                                                            <br>
                                                            Belum ada data lamaran
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
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
        <?php include '../../src/template/app-footer.php'; ?>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!-- begin::Modal -->
    <?php foreach ($dataLamaran as $row) : ?>
        <div class="modal fade" id="modalStatus<?= $row["id_lamaran"]; ?>" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-5" id="modalStatusLabel">
                            <i class="fas fa-user-tag me-2"></i>Set Status Lamaran
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="id_lamaran" value="<?= $row["id_lamaran"]; ?>">
                        <input type="hidden" name="status" id="statusInput<?= $row["id_lamaran"]; ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <span class="mb-1">Nama Siswa :</span>
                                            <span class="fw-bold    "><?= $row["nama_siswa"] ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="mb-1">Perusahaan :</span>
                                            <span class="fw-bold"><?= $row["nama_perusahaan"] ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="mb-1">Status Saat Ini :</span>
                                            <span class="fw-bold"><?= $row['status']; ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="mb-1">Status Kerjasama :</span>
                                            <span class="fw-bold">
                                                <?php
                                                if (isset($row['status_kerjasama']) && $row['status_kerjasama'] !== null) {
                                                    echo $row['status_kerjasama'] == 'bekerja_sama' ? 'Bekerja Sama' : 'Tidak Bekerja Sama';
                                                } else {
                                                    echo 'Tidak Bekerja Sama';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="mb-1">File CV <?= $row["nama_siswa"] ?> :</span>
                                            <?php
                                            $cv_path = null;
                                            $allowed_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
                                            $nama_siswa_singkat = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($row['nama_siswa']));
                                            foreach ($allowed_ext as $ext) {
                                                $filename = "cv_" . $nama_siswa_singkat . "_" . $row['id_lowongan'] . "." . $ext;
                                                $filepath = "../../src/assets/persyaratan/cv/" . $filename;
                                                if (file_exists($filepath)) {
                                                    $cv_path = $filepath;
                                                    break;
                                                }
                                            }
                                            if ($cv_path) : ?>
                                                <a href="<?= $cv_path ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-download"></i> Download CV
                                                </a>
                                            <?php else : ?>
                                                <span class="text-danger">Tidak ada CV</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-evenly my-3">
                                <button type="button" class="btn btn-outline-danger px-4 btn-status-toggle" id="btn-tidak-<?= $row['id_lamaran']; ?>" onclick="setStatus('Tidak Diterima Kerja', <?= $row['id_lamaran']; ?>)">
                                    <i class="fas fa-times-circle me-1"></i> Tidak Diterima
                                </button>
                                <button type="button" class="btn btn-outline-success px-4 btn-status-toggle" id="btn-diterima-<?= $row['id_lamaran']; ?>" onclick="setStatus('Diterima Kerja', <?= $row['id_lamaran']; ?>)">
                                    <i class="fas fa-check-circle me-1"></i> Diterima
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- end::Modal -->

    <!--begin::Script-->
    <?php
    include '../../src/template/footer.php';
    ?>
    <!-- OPTIONAL SCRIPTS -->

    <!-- begin::SweetAlertKonfirmasi -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi DataTables
            const dataTable = $('#lamaranTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthMenu: [5, 10, 25, 50],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 2
                    }, // Nama 
                    {
                        responsivePriority: 1,
                        targets: 0
                    }, // No
                    {
                        responsivePriority: 3,
                        targets: 3
                    }, // Email
                    {
                        responsivePriority: 2,
                        targets: -1
                    } // Aksi
                ],
            });

            // Fungsi untuk filter status
            const filterOptions = document.querySelectorAll('.filter-option');

            filterOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Update active class
                    filterOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');

                    const selectedStatus = this.getAttribute('data-status');

                    // Update teks dropdown
                    document.getElementById('filterStatusDropdown').innerHTML =
                        `<i class="fas fa-filter me-1"></i>` + (selectedStatus === 'all' ? 'Semua Status' : selectedStatus);

                    // Filter data di DataTables
                    if (selectedStatus === 'all') {
                        dataTable.column(1).search('').draw();
                    } else {
                        dataTable.column(1).search('^' + selectedStatus + '$', true, false).draw();
                    }
                });
            });

            // Seleksi semua tombol hapus
            const deleteButtons = document.querySelectorAll('.btn-hapus');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Mencegah langsung ke link

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

        function setStatus(status, id) {
            document.getElementById('statusInput' + id).value = status;
            // Toggle active class for buttons
            const btnDiterima = document.getElementById('btn-diterima-' + id);
            const btnTidak = document.getElementById('btn-tidak-' + id);

            // Remove active class from all buttons in this modal
            const modal = document.getElementById('modalStatus' + id);
            const allButtons = modal.querySelectorAll('.btn-status-toggle');
            allButtons.forEach(btn => btn.classList.remove('active'));

            // Add active class to clicked button
            if (status === 'Diterima Kerja') {
                btnDiterima.classList.add('active');
            } else if (status === 'Tidak Diterima Kerja') {
                btnTidak.classList.add('active');
            }
        }

        // Optional: Reset active state when modal is closed
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($dataLamaran as $row): ?>
                var modal = document.getElementById('modalStatus<?= $row["id_lamaran"]; ?>');
                if (modal) {
                    modal.addEventListener('hidden.bs.modal', function() {
                        const allButtons = this.querySelectorAll('.btn-status-toggle');
                        allButtons.forEach(btn => btn.classList.remove('active'));
                        document.getElementById('statusInput<?= $row["id_lamaran"]; ?>').value = '';
                    });
                }
            <?php endforeach; ?>
        });

        // SweetAlert untuk button logout
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
    <!-- End::Script -->
</body>
<!--end::Body-->

</html>