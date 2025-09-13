<?php
// Matikan display error untuk mencegah output HTML
ini_set('display_errors', 0);
error_reporting(0);

// Aktifkan output buffering untuk menangkap output apapun
ob_start();
session_start();
require_once '../functions.php';

try {
    // Cek apakah user adalah admin
    if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
        throw new Exception('Akses ditolak! Hanya admin yang bisa mengunduh backup.');
    }

    // Cek apakah file ZIP ada di session
    if (!isset($_SESSION['zip_file']) || !isset($_SESSION['zip_path'])) {
        throw new Exception('Tidak ada file ZIP yang tersedia untuk diunduh!');
    }

    $zipName = $_SESSION['zip_file'];
    $zipPath = $_SESSION['zip_path']; // Gunakan path yang disimpan di session

    // Cek apakah file ZIP ada
    if (!file_exists($zipPath)) {
        throw new Exception('File ZIP tidak ditemukan di server!');
    }

    // Set header untuk download
    header('Content-Description: File Transfer');
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zipPath));

    // Bersihkan output buffer
    ob_end_clean();

    // Baca file dan kirim ke output
    readfile($zipPath);

    // Hapus file temporary setelah didownload (dengan delay untuk memastikan unduhan selesai)
    register_shutdown_function(function() use ($zipPath) {
        // Tunggu 5 detik sebelum menghapus file
        sleep(5);
        @unlink($zipPath);
    });

    // Hapus session
    unset($_SESSION['zip_file']);
    unset($_SESSION['zip_path']);

    exit;

} catch (Exception $e) {
    // Tangkap error dan kirim sebagai response error
    ob_end_clean();
    header('HTTP/1.0 500 Internal Server Error');
    die($e->getMessage());
}
?>