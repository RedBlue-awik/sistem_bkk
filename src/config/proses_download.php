<?php
session_start();
require_once '../functions.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('Akses ditolak! Hanya admin yang bisa mengunduh backup.');
}

// Cek apakah file ZIP ada di session
if (!isset($_SESSION['zip_file'])) {
    header('HTTP/1.0 404 Not Found');
    die('Tidak ada file ZIP yang tersedia untuk diunduh!');
}

$zipName = $_SESSION['zip_file'];
$zipPath = sys_get_temp_dir() . '/' . $zipName;

// Cek apakah file ZIP ada secara fisik
if (!file_exists($zipPath)) {
    header('HTTP/1.0 404 Not Found');
    die('File ZIP tidak ditemukan di server!');
}

// Set header untuk download
header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($zipPath));

// Baca file dan kirim ke output
readfile($zipPath);

// Hapus file temporary setelah didownload
unlink($zipPath);

// Hapus session
unset($_SESSION['zip_file']);

exit;
?>