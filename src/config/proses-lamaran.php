<?php
session_start();
require '../functions.php';

if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>alert('Sesi kamu telah habis, silakan login kembali.'); window.location='../../index.php';</script>";
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_lowongan = mysqli_real_escape_string($conn, $_GET['id']);
} elseif (isset($_POST['id_lowongan']) && !empty($_POST['id_lowongan'])) {
    $id_lowongan = mysqli_real_escape_string($conn, $_POST['id_lowongan']);
} else {
    echo "<script>alert('ID lowongan tidak ditemukan.'); window.location='../../pages/public/loker.php';</script>";
    exit;
}

$id_user = $_SESSION['id_pengguna'];

// Ambil kode_pengguna dari user
$query_user = mysqli_query($conn, "SELECT kode_pengguna FROM user WHERE id_user = '$id_user'");
if (!$query_user || mysqli_num_rows($query_user) == 0) {
    echo "<script>alert('Data pengguna tidak ditemukan.'); window.location='../../pages/public/loker.php';</script>";
    exit;
}

$data_user   = mysqli_fetch_assoc($query_user);
$kode_alumni = $data_user['kode_pengguna'];

// Ambil id_alumni dari alumni
$query_alumni = mysqli_query($conn, "SELECT id_alumni FROM alumni WHERE kode_alumni = '$kode_alumni'");
if (!$query_alumni || mysqli_num_rows($query_alumni) == 0) {
    echo "<script>alert('Data alumni tidak ditemukan.'); window.location='../../pages/public/loker.php';</script>";
    exit;
}

$data_alumni = mysqli_fetch_assoc($query_alumni);
$id_alumni   = $data_alumni['id_alumni'];

// Ambil nama siswa dari alumni
$query_nama = mysqli_query($conn, "SELECT nama FROM alumni WHERE id_alumni = '$id_alumni'");
$data_nama = mysqli_fetch_assoc($query_nama);
$nama_siswa = isset($data_nama['nama']) ? $data_nama['nama'] : 'siswa';
$nama_siswa_singkat = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($nama_siswa)); 

// Cek apakah sudah pernah melamar
$cek = mysqli_query($conn, "SELECT 1 FROM lamaran WHERE id_siswa = '$id_alumni' AND id_lowongan = '$id_lowongan'");
if (mysqli_num_rows($cek) > 0) {
?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Lamaran Gagal di Kirim!',
                text: 'Kamu Sudah Melamar di Lowongan ini',
            }).then(() => {
                window.location.href = '../../pages/public/loker.php';
            });
        });
    </script>
    <?php
    exit;
}

// Proses upload CV jika ada file
$cv_file_name = null;
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    $cv = $_FILES['cv'];
    $ekstensi_file = strtolower(pathinfo($cv['name'], PATHINFO_EXTENSION));
    $ekstensi_diperbolehkan = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    $ukuran_max = 100 * 1024 * 1024;

    if (!in_array($ekstensi_file, $ekstensi_diperbolehkan)) {
    ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Didukung',
                    text: 'File harus berformat PDF, Word (.doc/.docx), atau Excel (.xls/.xlsx).',
                }).then(() => {
                    window.history.back();
                });
            });
        </script>
    <?php
        exit;
    }
    if ($cv['size'] > $ukuran_max) {
    ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran File Terlalu Besar',
                    text: 'Ukuran file maksimal 100MB.',
                }).then(() => {
                    window.history.back();
                });
            });
        </script>
    <?php
        exit;
    }
    // Ubah nama file CV menjadi nama siswa
    $nama_file = "cv_" . $nama_siswa_singkat . "_" . $id_lowongan . "." . $ekstensi_file;
    $folder_tujuan = "../../src/assets/persyaratan/cv/";
    if (!is_dir($folder_tujuan)) {
        mkdir($folder_tujuan, 0777, true);
    }
    $path_upload = $folder_tujuan . $nama_file;
    if (!move_uploaded_file($cv['tmp_name'], $path_upload)) {
    ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Gagal',
                    text: 'CV gagal diupload!',
                }).then(() => {
                    window.history.back();
                });
            });
        </script>
    <?php
        exit;
    }
    $cv_file_name = $nama_file;
}

// Insert lamaran (tanpa kolom cv)
$tanggal_lamar = date('Y-m-d');
$status         = 'Menunggu';

$insert = mysqli_query($conn, "INSERT INTO lamaran (id_siswa, id_lowongan, tanggal_lamar, status)
                               VALUES ('$id_alumni', '$id_lowongan', '$tanggal_lamar', '$status')");

if ($insert) {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Lamaran Dikirim',
                text: 'Lamaran berhasil dikirim!',
            }).then(() => {
                window.location.href = '../../pages/public/loker.php';
            });
        });
    </script>
<?php
} else {
    echo "<script>alert('Gagal melamar. Silakan coba lagi.'); window.history.back();</script>";
}
?>