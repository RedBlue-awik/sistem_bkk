<?php
// Matikan display error untuk mencegah output HTML
ini_set('display_errors', 0);
error_reporting(0);

// Aktifkan output buffering untuk menangkap output apapun
ob_start();
session_start();
require_once '../functions.php';

// Set header untuk JSON
header('Content-Type: application/json');

try {
    // Cek apakah user adalah admin
    if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
        throw new Exception('Akses ditolak! Hanya admin yang bisa mengunduh backup.');
    }

    // Ambil semua data backup dari database
    $query = "SELECT * FROM backup_db ORDER BY tanggal_backup DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception('Gagal mengambil data backup: ' . mysqli_error($conn));
    }

    // Jika tidak ada data backup
    if (mysqli_num_rows($result) == 0) {
        throw new Exception('Tidak ada data backup yang ditemukan di database!');
    }

    // Buat nama file ZIP unik dengan timestamp
    $zipName = 'Data_BKK_All.zip';
    $zipPath = sys_get_temp_dir() . '/' . $zipName;

    // Buat file ZIP
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
        throw new Exception('Gagal membuat file ZIP! Error: ' . $zip->getStatusString());
    }

    // Simpan semua data backup ke array
    $backupsData = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $backupsData[] = $row;
    }

    // Kembalikan pointer ke awal
    mysqli_data_seek($result, 0);

    // Tambahkan semua backup ke ZIP
    $backupCount = 0;
    foreach ($backupsData as $backup) {
        $backupCount++;
        
        // Buat nama file unik dengan menambahkan ID backup
        $uniqueFileName = pathinfo($backup['nama_file'], PATHINFO_FILENAME) . 
                          '_ID' . $backup['id_backup'] . 
                          '.' . pathinfo($backup['nama_file'], PATHINFO_EXTENSION);
        
        // Buat backup yang UNIK untuk setiap file berdasarkan data backup
        $backupContent = generateUniqueBackup($conn, $backup);
        
        // Tambahkan ke ZIP dengan nama file unik
        $zip->addFromString($uniqueFileName, $backupContent);
    }

    // Tutup file ZIP
    $zip->close();

    // Simpan informasi ZIP di session
    $_SESSION['zip_file'] = $zipName;
    $_SESSION['zip_path'] = $zipPath; // Simpan path lengkap di session

    // Bersihkan output buffer dan kirim JSON
    ob_end_clean();
    echo json_encode([
        'success' => true, 
        'message' => "File ZIP berhasil dibuat! $backupCount backup telah disertakan.",
        'zip_file' => $zipName,
        'backup_count' => $backupCount
    ]);

} catch (Exception $e) {
    // Tangkap error dan kirim sebagai JSON
    ob_end_clean();
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

// Fungsi untuk generate backup yang UNIK berdasarkan data backup
function generateUniqueBackup($conn, $backupData)
{
    try {
        // Coba ambil data backup yang tersimpan di database
        $query = "SELECT isi_backup FROM backup_db WHERE id_backup = " . intval($backupData['id_backup']);
        $result = mysqli_query($conn, $query);
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            if (!empty($row['isi_backup'])) {
                // Jika ada data backup tersimpan, gunakan itu
                $backupContent = $row['isi_backup'];
                
                // Tambahkan header unik untuk membedakan
                $header = "-- phpMyAdmin SQL Dump\n";
                $header .= "-- Backup ID: " . $backupData['id_backup'] . "\n";
                $header .= "-- Nama File: " . $backupData['nama_file'] . "\n";
                $header .= "-- Tanggal Backup Asli: " . date('d M Y pada H.i', strtotime($backupData['tanggal_backup'])) . "\n";
                $header .= "-- Dibuat ulang pada: " . date('d M Y H:i:s') . "\n";
                $header .= "-- Signature: " . md5($backupData['id_backup'] . $backupData['tanggal_backup'] . $backupData['nama_file'] . time()) . "\n";
                $header .= "-- Unique ID: " . uniqid() . "\n\n";
                
                // Tambahkan data dummy unik
                $dummyData = "-- DUMMY DATA FOR BACKUP ID " . $backupData['id_backup'] . "\n";
                $dummyData .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
                $dummyData .= "-- Random string: " . bin2hex(random_bytes(16)) . "\n\n";
                
                return $header . $dummyData . $backupContent;
            }
        }
        
        // Jika tidak ada data backup tersimpan, generate dari database saat ini
        return generateCurrentDatabaseBackup($conn, $backupData);
    } catch (Exception $e) {
        // Jika terjadi error, kembalikan string kosong dengan informasi error
        return "-- ERROR: " . $e->getMessage() . "\n";
    }
}

function generateCurrentDatabaseBackup($conn, $backupData)
{
    try {
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
        $return .= "-- Signature: " . md5($backupData['id_backup'] . $backupData['tanggal_backup'] . $backupData['nama_file'] . time()) . "\n";
        $return .= "-- Unique ID: " . uniqid() . "\n";
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
    } catch (Exception $e) {
        // Jika terjadi error, kembalikan string kosong dengan informasi error
        return "-- ERROR: " . $e->getMessage() . "\n";
    }
}
?>