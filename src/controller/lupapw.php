<?php
// Proses Lupa Password
if (isset($_POST['lupa'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nisn = mysqli_real_escape_string($conn, $_POST['nisn']);
    $password_baru = mysqli_real_escape_string($conn, $_POST['passwordnew']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirmpassword']);

    // Set session untuk menandai bahwa form sudah disubmit
    $_SESSION['form_submitted'] = true;

    // Verifikasi bahwa username exists
    $cek_user = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");

    if (mysqli_num_rows($cek_user) > 0) {
        if ($password_baru !== $confirm_password) {
            $_SESSION['alert_type'] = 'error';
            $_SESSION['alert_message'] = 'Password dan Konfirmasi Password tidak sesuai!';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $cek_nisn = mysqli_query($conn, "SELECT * FROM alumni WHERE nisn = '$nisn'");
        if (mysqli_num_rows($cek_nisn) == 0) {
            $_SESSION['alert_type'] = 'error';
            $_SESSION['alert_message'] = 'Nisn tidak terdaftar dalam aplikasi!';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        // Hash password baru
        $password_hash = md5($password_baru);

        // Update password di database
        $update_query = mysqli_query($conn, "UPDATE user SET password = '$password_hash' WHERE username = '$username'");

        if ($update_query) {
            $_SESSION['alert_type'] = 'success';
            $_SESSION['alert_message'] = 'Password berhasil diubah!';
        } else {
            $_SESSION['alert_type'] = 'error';
            $_SESSION['alert_message'] = 'Gagal reset password!';
        }
    } else {
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'Username tidak terdaftar dalam aplikasi!';
    }

    // Redirect untuk menghindari resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Tampilkan alert hanya jika ada sessionnya
if (isset($_SESSION['form_submitted']) && $_SESSION['form_submitted'] === true) {
    if (isset($_SESSION['alert_type']) && isset($_SESSION['alert_message'])) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '" . $_SESSION['alert_type'] . "',
                    title: '" . ($_SESSION['alert_type'] == 'success' ? 'Berhasil' : 'Error') . "',
                    text: '" . $_SESSION['alert_message'] . "',
                    confirmButtonText: 'OK'
                }).then(() => {
                    " . ($_SESSION['alert_type'] == 'success' ? "
                    // Redirect ke modal login hanya jika success
                    var modalLogin = new bootstrap.Modal(document.getElementById('Modallogin'));
                    var modalLupa = bootstrap.Modal.getInstance(document.getElementById('Modallupapw'));
                    modalLupa.hide();
                    modalLogin.show();
                    " : "") . "
                });
            });
        </script>";

        // Hapus session setelah ditampilkan
        unset($_SESSION['form_submitted']);
        unset($_SESSION['alert_type']);
        unset($_SESSION['alert_message']);
    }
}
