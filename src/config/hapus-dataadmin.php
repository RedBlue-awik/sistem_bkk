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
if (hapusAdmin($id) > 0) {
    // Jika berhasil
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Admin berhasil dihapus!',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../pages/public/data-admin.php';
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
                text: 'Data Admin gagal dihapus!',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../pages/public/data-admin.php';
                }
            });
        });
    </script>";
}

?>
<style>
    * {
        font-family: sans-serif;
    }
</style>