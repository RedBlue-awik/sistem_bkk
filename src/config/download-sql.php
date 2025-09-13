<?php
session_start();
require_once '../functions.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('Akses ditolak! Hanya admin yang bisa mengunduh backup.');
}

// Cek apakah ID file backup diberikan
if (!isset($_GET['id'])) {
    header('HTTP/1.0 400 Bad Request');
    die('ID backup tidak ditemukan!');
}

$id = intval($_GET['id']);

// Ambil data backup dari database
$query = "SELECT * FROM backup_db WHERE id_backup = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('HTTP/1.0 404 Not Found');
    die('Data backup tidak ditemukan!');
}

$row = mysqli_fetch_assoc($result);

// Generate backup content
$backupContent = generateUniqueBackup($conn, $row);

// Set header untuk download
header('Content-Description: File Transfer');
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $row['nama_file'] . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($backupContent));

// Output konten SQL
echo $backupContent;
exit;

// Fungsi generateUniqueBackup() sama seperti di download-all.php
function generateUniqueBackup($conn, $backupData)
{
    // Dapatkan nama database
    $dbname = 'sekolah_bkk';

    $tables = array();
    $result = mysqli_query($conn, 'SHOW TABLES');
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    // Urutkan tabel
    usort($tables, function ($a, $b) {
        if ($a == 'user') return -1;
        if ($b == 'user') return 1;
        if ($a == 'pengumuman' && $b == 'pengumuman_viewed') return -1;
        if ($b == 'pengumuman' && $a == 'pengumuman_viewed') return 1;
        return strcmp($a, $b);
    });

    // Buat header yang UNIK untuk setiap backup berdasarkan data asli
    $formattedDate = date('d M Y pada H.i', strtotime($backupData['tanggal_backup']));

    $return = "-- phpMyAdmin SQL Dump\n";
    $return .= "-- Backup ID: " . $backupData['id_backup'] . "\n";
    $return .= "-- Nama File: " . $backupData['nama_file'] . "\n";
    $return .= "-- Tanggal Backup Asli: " . $formattedDate . "\n";
    $return .= "-- Dibuat ulang pada: " . date('d M Y H:i:s') . "\n";
    $return .= "-- Signature: " . md5($backupData['id_backup'] . time() . rand()) . "\n";
    $return .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $return .= "START TRANSACTION;\n";
    $return .= "SET time_zone = \"+00:00\";\n\n";
    $return .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    // Loop untuk setiap tabel
    foreach ($tables as $table) {
        $result = mysqli_query($conn, 'SELECT * FROM ' . $table);
        $numFields = mysqli_num_fields($result);

        // Struktur tabel
        $return .= "-- Struktur dari tabel `" . $table . "`\n\n";

        $createResult = mysqli_query($conn, 'SHOW CREATE TABLE ' . $table);
        if ($createResult && $createRow = mysqli_fetch_row($createResult)) {
            $return .= $createRow[1] . ";\n\n";
        }

        // Data tabel
        $return .= "-- Data untuk tabel `" . $table . "`\n\n";

        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_row($result)) {
            $return .= "INSERT INTO `" . $table . "` VALUES(";
            for ($j = 0; $j < $numFields; $j++) {
                if (isset($row[$j])) {
                    $escaped = mysqli_real_escape_string($conn, $row[$j]);
                    $escaped = str_replace(array("\n", "\r", "\t", "\\", "'"), array("\\n", "\\r", "\\t", "\\\\", "''"), $escaped);
                    $return .= "'" . $escaped . "'";
                } else {
                    $return .= "NULL";
                }
                if ($j < ($numFields - 1)) {
                    $return .= ",";
                }
            }
            $return .= ");\n";
        }
        $return .= "\n\n";

        mysqli_free_result($result);
    }

    $return .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";
    $return .= "COMMIT;\n";

    return $return;
}
