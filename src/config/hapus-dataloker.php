<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo "
        <script>
            document.location.href = '../../index.php';
        </script>
    ";
exit;
}

if ($_SESSION['level'] !== 'admin') {
    echo "
        <script>
            document.location.href = '../../index.php';
        </script>
    ";
exit;
}

// Penghubung antar file di PHP
require '../functions.php';

// Tangkap id admin di url dengan $_GET
$id = $_GET['id'];

// Jalankan function hapus admin
if (hapusLoker($id) > 0) {
    // Jika berhasil
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Lowongan Kerja berhasil dihapus!',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../pages/public/loker.php';
                }
            });
        });
    </script>";
} else {
    // Jika gagal
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Data Lowongan Kerja gagal dihapus!',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../pages/public/loker.php';
                }
            });
        });
    </script>";
}

?>
<style>
    * {
        font-family: "Poppins", sans-serif;
    }
</style>