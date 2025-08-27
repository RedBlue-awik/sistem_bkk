<?php
require '../vendor/autoload.php';
include '../functions.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

if (isset($_POST['import'])) {
    $file = $_FILES['file_excel'];

    $allowed_ext = ['xls', 'xlsx'];
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (!in_array(strtolower($file_ext), $allowed_ext)) {
        echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: 'PERINGATAN!',
                text: 'File Yang Anda Masukkan Harus Berupa xlsx atau xls!',
                confirmButtonText: 'OK'
            }).then(() => {
                    window.location.href = '../../pages/public/data-siswa.php';
                });
            });
    </script>";
        exit;
    }

    $spreadsheet = IOFactory::load($file['tmp_name']);
    $data = $spreadsheet->getActiveSheet()->toArray();

    for ($i = 1; $i < count($data); $i++) {
        $nisn          = mysqli_real_escape_string($conn, $data[$i][0]);
        $nama          = mysqli_real_escape_string($conn, $data[$i][1]);
        $jurusan       = mysqli_real_escape_string($conn, $data[$i][2]);
        $alamat        = mysqli_real_escape_string($conn, $data[$i][3]);
        $telepon       = mysqli_real_escape_string($conn, $data[$i][4]);
        if (is_numeric($data[$i][5])) {
            $tahun_lulus = Date::excelToDateTimeObject($data[$i][5])->format('Y-m-d');
        } else {
            $tahun_lulus = mysqli_real_escape_string($conn, $data[$i][5]);
        }
        $username      = mysqli_real_escape_string($conn, $data[$i][6]);
        $password      = isset($data[$i][7]) ? md5($data[$i][7]) : md5('default123');

        // Buat kode_alumni unik
        $result = mysqli_query($conn, "SELECT kode_alumni FROM alumni ORDER BY kode_alumni ASC");
        $existingCodes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $number = (int)substr($row['kode_alumni'], 1);
            $existingCodes[] = $number;
        }

        $kodeBaru = 1;
        while (in_array($kodeBaru, $existingCodes)) {
            $kodeBaru++;
        }
        $kode_alumni = 'S' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);

        // Insert ke tabel alumni
        mysqli_query($conn, "INSERT INTO alumni (kode_alumni, nama, nisn, jurusan, tahun_lulus, telepon, alamat)
                             VALUES ('$kode_alumni', '$nama', '$nisn', '$jurusan', '$tahun_lulus', '$telepon', '$alamat')");

        // Insert ke tabel user (akun alumni)
        mysqli_query($conn, "INSERT INTO user (kode_pengguna, username, password, level)
                             VALUES ('$kode_alumni', '$username', '$password', 'alumni')");
    }

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL!',
                text: 'Data Berhasil Di Import atau Di Tambahkan!',
                confirmButtonText: 'OK'
            }).then(() => {
                    window.location.href = '../../pages/public/data-siswa.php';
                });
            });
    </script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include "../template/headers.php" ?>
</head>

<body>
    <?php include "../template/footer.php" ?>
</body>

</html>