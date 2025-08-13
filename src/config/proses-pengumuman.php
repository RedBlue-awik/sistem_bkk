<?php
session_start();
require '../functions.php';

header('Content-Type: application/json');

// Pastikan pengguna login
if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['jumlah' => 0, 'latest' => '0', 'akan_dibuka' => 0, 'baru_dibuka' => 0]);
    exit;
}

$level = $_SESSION['level'];
$id_user = intval($_SESSION['id_pengguna']);
$jumlah = 0;
$latest = 0; // tidak lagi dipakai, tapi dikirim agar tidak error
$akan_dibuka = 0;
$baru_dibuka = 0;

// Ambil id_siswa jika level alumni
$id_siswa = null;
if ($level === 'alumni') {
    $q = mysqli_query($conn, "SELECT kode_pengguna FROM user WHERE id_user = $id_user");
    $row = mysqli_fetch_assoc($q);
    if ($row) {
        $kode_alumni = $row['kode_pengguna'];
        $q2 = mysqli_query($conn, "SELECT id_alumni FROM alumni WHERE kode_alumni = '$kode_alumni'");
        $row2 = mysqli_fetch_assoc($q2);
        if ($row2) {
            $id_siswa = intval($row2['id_alumni']);
        }
    }
}

// Jika ada parameter ?viewed=true maka tandai semua pengumuman sebagai sudah dibaca
if (isset($_GET['viewed']) && $_GET['viewed'] === 'true') {
    if ($level === 'alumni' && $id_siswa !== null) {
        $query = mysqli_query($conn, "
            SELECT id_pengumuman FROM pengumuman
            WHERE ditujukan='semua' OR (ditujukan='khusus' AND id_siswa = $id_siswa)
        ");
        while ($row = mysqli_fetch_assoc($query)) {
            $id_pengumuman = $row['id_pengumuman'];
            // Masukkan ke pengumuman_viewed jika belum ada
            mysqli_query($conn, "
                INSERT IGNORE INTO pengumuman_viewed (id_pengumuman, id_user, id_siswa)
                VALUES ($id_pengumuman, $id_user, $id_siswa)
            ");
        }
    } elseif ($level === 'admin') {
        $query = mysqli_query($conn, "
            SELECT id_pengumuman FROM pengumuman
            WHERE ditujukan='semua'
        ");
        while ($row = mysqli_fetch_assoc($query)) {
            $id_pengumuman = $row['id_pengumuman'];
            mysqli_query($conn, "
                INSERT IGNORE INTO pengumuman_viewed (id_pengumuman, id_user, id_siswa)
                VALUES ($id_pengumuman, $id_user, NULL)
            ");
        }
    }

    echo json_encode(['jumlah' => 0, 'latest' => '0', 'akan_dibuka' => 0, 'baru_dibuka' => 0]);
    exit;
}

// Hitung jumlah pengumuman yang belum dibaca
if ($level === 'admin') {
    $q = mysqli_query($conn, "
        SELECT COUNT(*) as jumlah FROM pengumuman
        WHERE ditujukan='semua'
          AND id_pengumuman NOT IN (
              SELECT id_pengumuman FROM pengumuman_viewed WHERE id_user = $id_user
          )
    ");
    $row = mysqli_fetch_assoc($q);
    $jumlah = $row ? $row['jumlah'] : 0;

} elseif ($level === 'alumni' && $id_siswa !== null) {
    $q = mysqli_query($conn, "
        SELECT COUNT(*) as jumlah FROM pengumuman
        WHERE (ditujukan='semua' OR (ditujukan='khusus' AND id_siswa = $id_siswa))
          AND id_pengumuman NOT IN (
              SELECT id_pengumuman FROM pengumuman_viewed WHERE id_user = $id_user
          )
    ");
    $row = mysqli_fetch_assoc($q);
    $jumlah = $row ? $row['jumlah'] : 0;
}

// Cek lowongan akan dibuka dalam 7 hari
$now = strtotime(date('Y-m-d'));
$seven_days = strtotime('+7 days', $now);

$q_loker = mysqli_query($conn, "
    SELECT tanggal_dibuka FROM lowongan
    WHERE tanggal_dibuka > CURDATE() AND tanggal_dibuka <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
");
while ($row = mysqli_fetch_assoc($q_loker)) {
    $tgl_buka = strtotime($row['tanggal_dibuka']);
    if ($tgl_buka > $now && $tgl_buka <= $seven_days) {
        $akan_dibuka++;
    }
}

// Lowongan baru dibuka hari ini
$q_loker_baru = mysqli_query($conn, "
    SELECT COUNT(*) as jumlah FROM lowongan
    WHERE tanggal_dibuka = CURDATE()
");
$row_baru = mysqli_fetch_assoc($q_loker_baru);
$baru_dibuka = $row_baru ? $row_baru['jumlah'] : 0;

// Kirim respons JSON
echo json_encode([
    'jumlah' => $jumlah,
    'latest' => '0',
    'akan_dibuka' => $akan_dibuka,
    'baru_dibuka' => $baru_dibuka
]);
