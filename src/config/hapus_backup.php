<?php
session_start();
require_once '../functions.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['id_pengguna'])) {
    echo "
        <script>
            document.location.href = '../../index.php';
        </script>
    ";
}
// Cek apakah user adalah admin
if ($_SESSION['level'] != 'admin') {
    echo "
        <script>
            document.location.href = '../../index.php';
        </script>
    ";
}

$id = $_GET['id'];

// Query untuk menghapus backup
$query = "DELETE FROM backup_db WHERE id_backup = '$id'";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Backup berhasil dihapus.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus backup: ' . mysqli_error($conn)]);
}
