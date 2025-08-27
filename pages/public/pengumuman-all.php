<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
// if (!isset($_SESSION['id_pengguna'])) {
//     echo "<script>document.location.href = '../../index.php';</script>";
//     exit;
// }

require '../../src/functions.php';

include '../../src/controller/LoginF.php';

include '../../src/controller/lupapw.php';

// Ambil id_alumni
$id_siswa = 0;
if (isset($_SESSION['level']) && $_SESSION['level'] == 'alumni') {
    $id_user = intval($_SESSION['id_pengguna']);
    $q = mysqli_query($conn, "SELECT kode_pengguna FROM user WHERE id_user = $id_user");
    $row = mysqli_fetch_assoc($q);
    if ($row) {
        $kode_alumni = $row['kode_pengguna'];
        $q2 = mysqli_query($conn, "SELECT id_alumni FROM alumni WHERE kode_alumni = '$kode_alumni'");
        $row2 = mysqli_fetch_assoc($q2);
        if ($row2) {
            $id_siswa = intval($row2['id_alumni']);
        }
    }
}


$cek = cekPengumumanLokerBerakhir();

$pengumuman = [];
$q = mysqli_query($conn, "
    SELECT * FROM pengumuman
    WHERE ditujukan='semua'
       OR (ditujukan='khusus' AND id_siswa=$id_siswa)
    ORDER BY tanggal DESC, id_pengumuman DESC
");
while ($row = mysqli_fetch_assoc($q)) {
    $pengumuman[] = $row;
}

// Hitung pengumuman baru
$last_viewed = $_SESSION['last_viewed_pengumuman'] ?? 0;
$new_pengumuman = 0;
foreach ($pengumuman as $p) {
    if (strtotime($p['tanggal']) > $last_viewed) {
        $new_pengumuman++;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <?php
    $title = "Pengumuman";
    include '../../src/template/headers.php';
    ?>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f8f9fa;
        }

        .pengumuman-card {
            margin-bottom: 1.5rem;
        }

        .tgl {
            font-size: 15px;
        }

        .notification-badge {
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

        @media (max-width: 768px) {
            .judul {
                font-size: .94rem;
            }

            .tgl {
                font-size: .85rem;
            }

            .isi {
                font-size: .85rem;
            }

            .btn-C {
                font-size: .8rem;
            }
        }

        @media (max-width: 576px) {
            .judul {
                font-size: .9rem;
            }

            .tgl {
                font-size: .8rem;
            }

            .isi {
                font-size: .82rem;
            }

            .btn-C {
                font-size: .75rem;
            }

            .badge-p {
                font-size: .60rem;
                width: 50px;
            }
        }

        @media (max-width: 535px) {
            .judul {
                font-size: .7rem;
            }

            .tgl {
                font-size: .65rem;
                transform: translate(0, 19%);
            }

            .isi {
                font-size: .67rem;
            }

            .btn-C {
                font-size: .65rem;
            }

            .badge-p {
                font-size: .55rem;
                width: 45px;
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

                <?php if (isset ($_SESSION['level'])) : ?>
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
                <?php else: ?>
                    <ul class="navbar-nav ms-auto">

                        <!--begin::User Menu Dropdown-->
                        <button data-bs-toggle="modal" data-bs-target="#Modaldaftar" class="btn btn-outline-light ps-2 fw-medium d-flex align-items-center justify-content-center text-center" style="height: 30px; font-size: 13px;"><i class="fa-solid fa-pen-to-square me-2"></i>Daftar</button>
                        <button data-bs-toggle="modal" data-bs-target="#Modallogin" class="btn btn-outline-light ps-2 mx-2 fw-medium d-flex align-items-center justify-content-center text-center" style="height: 30px; font-size: 13px;"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</button>
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
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6 d-flex">
                            <h3 class="mb-0">Pengumuman</h3>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <div class="container-fluid">
                    <!--begin::Row-->
                    <?php if (empty($pengumuman)): ?>
                        <div class="alert alert-info">Belum ada pengumuman.</div>
                    <?php else: ?>
                        <?php foreach ($pengumuman as $p): ?>
                            <?php
                            $warna = 'bg-secondary';
                            if ($p['judul'] == 'Pengumuman Baru (Segera Dibuka)') {
                                $warna = 'bg-info';
                            } elseif ($p['judul'] == 'Lowongan Baru Dibuka') {
                                $warna = 'bg-primary';
                            } elseif ($p['judul'] == 'Lowongan Dihapus') {
                                $warna = 'bg-danger';
                            } elseif ($p['judul'] == 'Selamat! Lamaran Diterima') {
                                $warna = 'bg-success';
                            } elseif ($p['judul'] == 'Maaf, Lamaran Tidak Diterima') {
                                $warna = 'bg-warning text-dark';
                            }

                            $label = ($p['ditujukan'] === 'khusus') ? '<span class="badge badge-p bg-dark ms-2">Pribadi</span>' : '<span class="badge badge-p bg-light text-dark ms-2">Umum</span>';
                            ?>
                            <div class="card pengumuman-card shadow-sm border-0 position-relative">
                                <div class="card-header <?= $warna ?> text-white d-flex mb-3 align-items-center" style="min-height: 56px;">
                                    <div>
                                        <strong class="judul"><?= htmlspecialchars($p['judul']) ?></strong>
                                        <?= $label ?>
                                    </div>
                                    <div class="d-flex align-items-center p-2 ms-auto">
                                        <span class="tgl me-2 text-white"><?= date('d-m-Y H:i', strtotime($p['tanggal'])) ?></span>
                                        <?php if (
                                            $p['ditujukan'] === 'khusus'
                                            && isset($p['id_siswa'])
                                            && $p['id_siswa'] == $id_siswa
                                        ) : ?>
                                            <a href="../../src/config/hapus-pengumuman.php?id=<?= $p['id_pengumuman']; ?>" class="btn-C btn btn-sm btn-outline-light ms-1" title="Hapus Pengumuman" style="padding:2px 6px;">
                                                <i class="fas fa-x"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body bg-white rounded-bottom">
                                    <div class="isi"><?= nl2br($p['isi']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

    <!-- beggin::Modal -->
        <?php include '../../src/template/modalForm.php'; ?>
    <!-- end::Modal -->

    <?php include '../../src/template/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPengumuman = <?= $new_pengumuman ?>;
            // Pastikan badge sudah ada di DOM, jika belum tunggu sebentar
            function updateBadge() {
                var badge = document.getElementById('badgePengumuman');
                if (badge) {
                    if (newPengumuman > 0) {
                        badge.classList.remove('d-none');
                        badge.textContent = newPengumuman;
                        badge.classList.add('notification-badge');
                        // Tandai sudah dilihat
                        fetch(window.location.pathname + '?viewed=true');
                    } else {
                        badge.classList.add('d-none');
                        badge.textContent = '0';
                        badge.classList.remove('notification-badge');
                    }
                } else {
                    // Coba lagi setelah 200ms jika badge belum ada
                    setTimeout(updateBadge, 200);
                }
            }
            updateBadge();
        });
    </script>
</body>
<!--end::Body-->

</html>