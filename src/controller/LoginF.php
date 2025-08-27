<?php

// Gabungkan logika login dari pages/view/index.php ke sini
if (isset($_POST["login"])) {
    $username = htmlspecialchars($_POST['username']);
    $password = md5(htmlspecialchars($_POST['password']));

    // Cek di tabel admin
    $tabel_admin = "SELECT * FROM user u
        INNER JOIN admin a ON a.`kode_admin`=u.`kode_pengguna`
        WHERE username='" . $username . "' AND password='" . $password . "' LIMIT 1";
    $cek_tabel_admin = mysqli_query($conn, $tabel_admin);
    $admin = mysqli_num_rows($cek_tabel_admin);

    // Cek di tabel alumni
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
                    window.location.href = "./index.php";
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
                    window.location.href = "./index.php";
                });
            });
        </script>';
        exit();
    } else {
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: "Username Atau Password Salah!",
                            confirmButtonText: "OK"
                        }).then(function() {
                            window.location.href = "index.php";
                        });
                    });
                </script>';
        exit();
    }
}