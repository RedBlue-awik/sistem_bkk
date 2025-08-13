<?php
// Syarat untuk menggunakan session
session_start();

// Penghubung antar file di PHP
require './src/functions.php';

// Cek cookie (remember me)
// if (isset($_COOKIE['unik'])  && isset($_COOKIE['key'])) {
//     // Tangkap data dari $_COOKIE
//     $id = $_COOKIE['unik'];
//     $key = $_COOKIE['key'];

//     // Ambil semua data admin berdasarkan id
//     $result = mysqli_query($conn, "SELECT * FROM admin WHERE id_admin = $id");
//     $row = mysqli_fetch_assoc($result);

//     // Cek cookie dan email
//     if ($key === hash('sha256', $row['email'])) {
//         // Set session untuk keamanan dan mengirim data
//         $_SESSION['login'] = true;
//         $_SESSION['id_admin'] = $row['id_admin'];
//         $_SESSION['nama'] = $row['nama'];
//     }
// }

// Cek apakah sudah ada session login, jika sudah kembalikan
if (isset($_SESSION['id_pengguna'])) {
    echo "
        <script>
            document.location.href = './pages/public/index.php';
        </script>
    ";
}

// Cek apakah tombol login sudah di tekan
if (isset($_POST["login"])) {
    // Tangkap data dari form inputan user
    $username = htmlspecialchars($_POST['username']);
    $password = md5(htmlspecialchars($_POST['password']));

    // Cari data username di database yang sesuai dengan inputan user
    // Cek di tabel admin
    //Query untuk cek tabel user yang dijoinkan dengan table admin
    $tabel_admin = "SELECT * FROM user u
    INNER JOIN admin a ON a.`kode_admin`=u.`kode_pengguna`
    WHERE username='" . $username . "' AND password='" . $password . "' LIMIT 1";
    $cek_tabel_admin = mysqli_query($conn, $tabel_admin);
    $admin = mysqli_num_rows($cek_tabel_admin);
    //Query untuk cek pada tabel user yang dijoinkan dengan table alumni
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
        // Set jika ada error (username/password salah)
        $error = true;
    }
}
?>

<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/png" href="./src/assets/img/favicon.png">
    <title>BKK | Login</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE 4 | Login Page v2" />
    <meta name="author" content="ColorlibHQ" />
    <meta
        name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS." />
    <meta
        name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard" />
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
        crossorigin="anonymous" />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
        integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
        crossorigin="anonymous" />

    <link rel="stylesheet" href="./src/fontawesome-free-6.7.2-web/css/all.css">
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="./src/assets/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->

    <style>
        body,
        .swal2-popup {
            font-family: "Poppins", sans-serif;
        }
    </style>

</head>
<!--end::Head-->
<!--begin::Body-->

<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <a
                    href="index.php"
                    class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover">
                    <h1 class="mb-0"><b>Sign In-</b>BKK</h1>
                </a>
            </div>
            <div class="card-body login-card-body ">
                <p class="login-box-msg">Sign in to start your session</p>
                <form action="" method="post" class="needs-validation" novalidate>
                    <div class="input-group my-3">
                        <div class="form-floating">
                            <input id="username" type="text" name="username" class="form-control" placeholder="" required />
                            <label for="username">Username</label>
                        </div>
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                    <div class="input-group my-3">
                        <div class="form-floating">
                            <input id="password" type="password" name="password" class="form-control" placeholder="" required />
                            <label for="password">Password</label>
                        </div>
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                    <div class="input-group">
                        <div class="form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input" />
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                    </div>
                    <!--begin::Row-->
                    <div class="row">
                        <!-- /.col -->
                        <div class="d-grid gap-2 my-3">
                            <button type="submit" name="login" class="btn btn-primary">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!--end::Row-->
                </form>
                <?php if (isset($error)): ?>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Username Atau Password Salah!',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                <?php endif; ?>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
        src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
        crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
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
    <!--end::Script-->
</body>
<!--end::Body-->

</html>