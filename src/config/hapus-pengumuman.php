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

require '../functions.php';

// Hapus pengumuman berdasarkan id dari GET
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($conn, "DELETE FROM pengumuman WHERE id_pengumuman = $id");
}
header('Location: ../../pages/public/pengumuman-all.php');
exit;
