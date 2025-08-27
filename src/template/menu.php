<aside class="app-sidebar bg-primary-subtle shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand d-flex justify-content-start">
        <!--begin::Brand Link-->
        <a href="index.php" class="brand-link ms-2">
            <!--begin::Brand Image-->
            <img src="../../src/assets/img/logo.png" alt="SMK MI Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <!--end::Brand Image-->

            <span class="brand-text font-weight-bold ">SMK MI</span>

        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <?php if (!isset($_SESSION['level'])) { ?>
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="../../index.php" class="nav-link "><i class="bi bi-speedometer"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./chart.php" class="nav-link"><i class="fa-solid fa-chart-line"></i>
                            <p>Statistic</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./loker.php" class="nav-link"><i class="bi bi-briefcase-fill"></i>
                            <p>Lowongan Kerja</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="./pengumuman-all.php" class="nav-link mt-2 d-flex align-items-center">
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
        <?php } else if ($_SESSION["level"] == "admin") { ?>
            <nav class="mt-2">
                <!--begin::Sidebar Menu-->
                <ul
                    class="nav sidebar-menu flex-column"
                    data-lte-toggle="treeview"
                    role="menu"
                    data-accordion="false">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link ">
                            <i class="bi bi-speedometer"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="chart.php" class="nav-link ">
                            <i class="fa-solid fa-chart-line"></i>
                            <p>Statistic</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link mt-2">
                            <i class="bi bi-menu-button-wide-fill"></i>
                            <p>
                                Data Manajemen
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../../pages/public/data-admin.php" class="nav-link mt-2">
                                    <i class="nav-icon bi bi-person-fill"></i>
                                    <p>Data Admin</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../../pages/public/data-siswa.php" class="nav-link mt-2">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Data Alumni</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../../pages/public/data-perusahaan.php" class="nav-link mt-2">
                                    <i class="nav-icon bi bi-buildings-fill"></i>
                                    <p>Data Perusahaan</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link mt-2">
                            <i class="bi bi-graph-up-arrow"></i>
                            <p>
                                Bursa Kerja
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="loker.php" class="nav-link">
                                    <i class="nav-icon bi bi-briefcase-fill"></i>
                                    <p>Manejemen Loker</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="lamaran.php" class="nav-link"><Wbr></Wbr>
                                    <i class="nav-icon fas fa-envelope-open-text"></i>
                                    <p>Manejemen Lamaran</p>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link mt-2">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            <p>
                                Laporan
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../../pages/public/history-loker.php" class="nav-link">
                                    <i class="bi bi-clock-history"></i>
                                    <p>History Lowongan Kerja</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="../../pages/public/pengumuman-all.php" class="nav-link mt-2 d-flex align-items-center">
                            <i class="bi bi-bell-fill"></i>
                            <p class="p-b">
                                Pengumuman
                                <span class="badgePengumuman badge bg-danger float-end d-none">0</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../../logout.php" class="nav-link mt-3 btn-logout">
                            <i class="fas fa-arrow-right-from-bracket"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </li>
                </ul>
                <!--end::Sidebar Menu-->
            </nav>
        <?php } else if ($_SESSION["level"] == "alumni") { ?>
            <nav class="mt-2">
                <!--begin::Sidebar Menu-->
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link ">
                            <i class="bi bi-speedometer"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="chart.php" class="nav-link ">
                            <i class="fa-solid fa-chart-line"></i>
                            <p>Statistic</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link mt-2">
                            <i class="bi bi-menu-button-wide-fill"></i>
                            <p>
                                Data Manajemen
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="data-siswa.php" class="nav-link mt-2">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Data Alumni</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link mt-2">
                            <i class="bi bi-graph-up-arrow"></i>
                            <p>
                                Bursa Kerja
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../../pages/public/loker.php" class="nav-link">
                                    <i class="nav-icon bi bi-briefcase-fill"></i>
                                    <p>Lowongan Kerja</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="lamaran.php" class="nav-link">
                                    <i class="nav-icon fas fa-envelope-open-text"></i>
                                    <p>Manejemen Pelamaran</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link mt-2">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            <p>
                                Laporan
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../../pages/public/history-loker.php" class="nav-link">
                                    <i class="nav-icon bi bi-clock-history"></i>
                                    <p>History Lowongan Kerja</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="../../pages/public/pengumuman-all.php" class="nav-link mt-2 d-flex align-items-center">
                            <i class="bi bi-bell-fill"></i>
                            <p class="p-b">
                                Pengumuman
                                <span class="badgePengumuman badge bg-danger float-end d-none">0</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../../logout.php" class="nav-link d-flex align-items-center mt-3 btn-logout">
                            <i class="fas fa-arrow-right-from-bracket"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </li>
                </ul>
                <!--end::Sidebar Menu-->
            </nav>
        <?php } ?>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>