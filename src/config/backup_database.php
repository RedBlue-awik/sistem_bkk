<?php
session_start();
require_once '../functions.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    die("Akses ditolak! Hanya admin yang bisa melakukan backup.");
}

// Fungsi untuk backup database
function backupDatabase($host, $user, $pass, $dbname, $tables = '*')
{
    global $conn;
    
    // Cek koneksi
    if (!$conn) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
    
    // Set header SQL
    $return = "-- phpMyAdmin SQL Dump\n";
    $return .= "-- version 5.2.1\n";
    $return .= "-- https://www.phpmyadmin.net/\n";
    $return .= "--\n";
    $return .= "-- Host: " . $host . "\n";
    $return .= "-- Waktu pembuatan: " . date('d M Y pada H.i') . "\n";
    $return .= "-- Versi server: " . mysqli_get_server_info($conn) . "\n";
    $return .= "-- Versi PHP: " . phpversion() . "\n";
    $return .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $return .= "START TRANSACTION;\n";
    $return .= "SET time_zone = \"+00:00\";\n\n";
    $return .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
    $return .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
    $return .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
    $return .= "/*!40101 SET NAMES utf8mb4 */;\n";
    $return .= "--\n";
    $return .= "-- Database: `" . $dbname . "`\n";
    $return .= "--\n\n";
    
    // Nonaktifkan foreign key check sementara
    $return .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    
    // Jika $tables adalah '*', maka ambil semua tabel
    if ($tables == '*') {
        $tables = array();
        $result = mysqli_query($conn, 'SHOW TABLES');
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        // Urutkan tabel: pastikan tabel user dibuat sebelum pengumuman_viewed
        usort($tables, function ($a, $b) {
            // Prioritaskan tabel user
            if ($a == 'user') return -1;
            if ($b == 'user') return 1;
            // Prioritaskan tabel pengumuman sebelum pengumuman_viewed
            if ($a == 'pengumuman' && $b == 'pengumuman_viewed') return -1;
            if ($b == 'pengumuman' && $a == 'pengumuman_viewed') return 1;
            // Tabel lainnya diurutkan secara normal
            return strcmp($a, $b);
        });
    } else {
        // Jika $tables adalah string, ubah menjadi array
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }
    
    // Loop untuk setiap tabel
    foreach ($tables as $table) {
        $result = mysqli_query($conn, 'SELECT * FROM ' . $table);
        $numFields = mysqli_num_fields($result);
        
        // Struktur tabel
        $return .= "--\n";
        $return .= "-- Struktur dari tabel `" . $table . "`\n";
        $return .= "--\n\n";
        
        // Buat query CREATE TABLE
        $row2 = mysqli_fetch_row(mysqli_query($conn, 'SHOW CREATE TABLE ' . $table));
        $return .= $row2[1] . ";\n\n";
        
        // Data tabel
        $return .= "--\n";
        $return .= "-- Dumping data untuk tabel `" . $table . "`\n";
        $return .= "--\n\n";
        
        // Loop untuk setiap baris
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_row($result)) {
            $return .= "INSERT INTO `" . $table . "` VALUES(";
            for ($j = 0; $j < $numFields; $j++) {
                // Tangani NULL values
                if (isset($row[$j])) {
                    // Escape karakter khusus
                    $row[$j] = mysqli_real_escape_string($conn, $row[$j]);
                    // Ganti karakter khusus dengan literal
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    $row[$j] = str_replace("\r", "\\r", $row[$j]);
                    $row[$j] = str_replace("\t", "\\t", $row[$j]);
                    $row[$j] = str_replace("\\", "\\\\", $row[$j]);
                    $row[$j] = str_replace("'", "''", $row[$j]);
                    // Tambahkan kutipan
                    $return .= "'" . $row[$j] . "'";
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
    
    // Indexes untuk tabel yang dibuang
    $return .= "--\n";
    $return .= "-- Indexes for dumped tables\n";
    $return .= "--\n\n";
    
    foreach ($tables as $table) {
        $return .= "--\n";
        $return .= "-- Indeks untuk tabel `" . $table . "`\n";
        $return .= "--\n";
        
        // Dapatkan indeks
        $indexResult = mysqli_query($conn, "SHOW INDEX FROM `" . $table . "`");
        while ($indexRow = mysqli_fetch_assoc($indexResult)) {
            if ($indexRow['Key_name'] != 'PRIMARY') {
                $return .= "ALTER TABLE `" . $table . "` ADD KEY `" . $indexRow['Key_name'] . "` (" . $indexRow['Column_name'] . ");\n";
            }
        }
        $return .= "\n";
        mysqli_free_result($indexResult);
    }
    
    // AUTO_INCREMENT untuk tabel yang dibuang
    $return .= "--\n";
    $return .= "-- AUTO_INCREMENT untuk tabel yang dibuang\n";
    $return .= "--\n\n";
    
    foreach ($tables as $table) {
        // Dapatkan informasi AUTO_INCREMENT
        $autoIncResult = mysqli_query($conn, "SHOW TABLE STATUS LIKE '" . $table . "'");
        $autoIncRow = mysqli_fetch_assoc($autoIncResult);
        if ($autoIncRow['Auto_increment'] > 0) {
            $return .= "--\n";
            $return .= "-- AUTO_INCREMENT untuk tabel `" . $table . "`\n";
            $return .= "--\n";
            $return .= "ALTER TABLE `" . $table . "` MODIFY `" . getPrimaryKey($conn, $table) . "` " . getColumnType($conn, $table, getPrimaryKey($conn, $table)) . " NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=" . $autoIncRow['Auto_increment'] . ";\n\n";
        }
        mysqli_free_result($autoIncResult);
    }
    
    // Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
    $return .= "--\n";
    $return .= "-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)\n";
    $return .= "--\n\n";
    
    foreach ($tables as $table) {
        // Dapatkan foreign key constraints
        $fkResult = mysqli_query($conn, "SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '" . $dbname . "' AND TABLE_NAME = '" . $table . "' AND REFERENCED_TABLE_NAME IS NOT NULL");
        if (mysqli_num_rows($fkResult) > 0) {
            $return .= "--\n";
            $return .= "-- Ketidakleluasaan untuk tabel `" . $table . "`\n";
            $return .= "--\n";
            while ($fkRow = mysqli_fetch_assoc($fkResult)) {
                $return .= "ALTER TABLE `" . $table . "` ADD CONSTRAINT `" . $fkRow['CONSTRAINT_NAME'] . "` FOREIGN KEY (`" . $fkRow['COLUMN_NAME'] . "`) REFERENCES `" . $fkRow['REFERENCED_TABLE_NAME'] . "` (`" . $fkRow['REFERENCED_COLUMN_NAME'] . "`) ON DELETE CASCADE;\n";
            }
            $return .= "\n";
        }
        mysqli_free_result($fkResult);
    }
    
    // Aktifkan kembali foreign key check
    $return .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";
    $return .= "COMMIT;\n";
    $return .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
    $return .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
    $return .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
    
    return $return;
}

// Fungsi untuk mendapatkan primary key dari tabel
function getPrimaryKey($conn, $table)
{
    $result = mysqli_query($conn, "SHOW KEYS FROM `" . $table . "` WHERE Key_name = 'PRIMARY'");
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['Column_name'];
}

// Fungsi untuk mendapatkan tipe kolom
function getColumnType($conn, $table, $column)
{
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `" . $table . "` LIKE '" . $column . "'");
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row['Type'];
}

// Generate backup content
$backupContent = backupDatabase($host, $user, $pass, $dbname);
$filename = 'BKK_Tahun(' . date('Y') . ').sql';
$id_admin = $_SESSION['id_pengguna'];

// Simpan informasi backup ke database (DENGAN backup_data)
$query = "INSERT INTO backup_db (nama_file, backup_data, id_admin) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssi", $filename, $backupContent, $id_admin);

if (mysqli_stmt_execute($stmt)) {
    // Jika berhasil disimpan ke database, tampilkan file SQL untuk diunduh
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($backupContent));
    echo $backupContent;
    exit;
} else {
    // Jika gagal, tampilkan pesan error
    die("Gagal menyimpan informasi backup ke database: " . mysqli_error($conn));
}
?>