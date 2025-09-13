<?php
session_start();
require '../functions.php';

header('Content-Type: application/json; charset=utf-8');
// Hindari cache agar fetch selalu fresh
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

function hitung_lowongan(&$akan_dibuka, &$baru_dibuka, $conn)
{
    $akan_dibuka = 0;
    $baru_dibuka = 0;

    // Akan dibuka 7 hari ke depan
    $q_loker = mysqli_query($conn, "
        SELECT tanggal_dibuka 
        FROM lowongan
        WHERE tanggal_dibuka > CURDATE() 
          AND tanggal_dibuka <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ");

    $now = strtotime(date('Y-m-d'));
    $seven_days = strtotime('+7 days', $now);

    while ($row = mysqli_fetch_assoc($q_loker)) {
        $tgl_buka = strtotime($row['tanggal_dibuka']);
        if ($tgl_buka > $now && $tgl_buka <= $seven_days) {
            $akan_dibuka++;
        }
    }

    // Baru dibuka hari ini
    $q_loker_baru = mysqli_query($conn, "
        SELECT COUNT(*) AS jumlah 
        FROM lowongan
        WHERE tanggal_dibuka = CURDATE()
    ");
    $row_baru = mysqli_fetch_assoc($q_loker_baru);
    $baru_dibuka = $row_baru ? intval($row_baru['jumlah']) : 0;
}

// ==========================
// KONDISI TAMU (BELUM LOGIN)
// ==========================
if (!isset($_SESSION['id_pengguna'])) {
    // Ambil id terbaru
    $result = mysqli_query($conn, "SELECT MAX(id_pengumuman) as last_id FROM pengumuman WHERE ditujukan='semua'");
    $row = mysqli_fetch_assoc($result);
    $last_id = $row['last_id'] ?? 0;

    // Ambil dari cookie
    $last_seen = $_COOKIE['tamu_last_seen'] ?? 0;

    // Hitung pengumuman baru
    $query = mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM pengumuman WHERE ditujukan='semua' AND id_pengumuman > $last_seen");
    $row = mysqli_fetch_assoc($query);
    $jumlah = $row['jumlah'];

    // Jika sudah dibuka → update cookie
    if (isset($_GET['viewed']) && $_GET['viewed'] === 'true') {
        setcookie('tamu_last_seen', $last_id, time() + (86400 * 30), "/"); 
        $jumlah = 0;
    }

    echo json_encode([
        'jumlah' => $jumlah,
        'latest' => $last_id
    ]);
    exit;
}

// ==========================
// KONDISI LOGIN (ADMIN/ALUMNI)
// ==========================
$level = $_SESSION['level'];
$id_user = intval($_SESSION['id_pengguna']);
$jumlah = 0;
$akan_dibuka = 0;
$baru_dibuka = 0;

$id_siswa = null;
if ($level === 'alumni') {
    $q = mysqli_query($conn, "SELECT kode_pengguna FROM user WHERE id_user = $id_user LIMIT 1");
    if ($q && ($row = mysqli_fetch_assoc($q))) {
        $kode_alumni = mysqli_real_escape_string($conn, $row['kode_pengguna']);
        $q2 = mysqli_query($conn, "SELECT id_alumni FROM alumni WHERE kode_alumni = '$kode_alumni' LIMIT 1");
        if ($q2 && ($row2 = mysqli_fetch_assoc($q2))) {
            $id_siswa = intval($row2['id_alumni']);
        }
    }
}

// Tandai sudah dilihat
if (isset($_GET['viewed']) && $_GET['viewed'] === 'true') {
    if ($level === 'alumni' && $id_siswa !== null) {
        $query = mysqli_query($conn, "
            SELECT id_pengumuman 
            FROM pengumuman
            WHERE ditujukan='semua' 
               OR (ditujukan='khusus' AND id_siswa = $id_siswa)
        ");
        while ($row = mysqli_fetch_assoc($query)) {
            $id_pengumuman = intval($row['id_pengumuman']);
            mysqli_query($conn, "
                INSERT IGNORE INTO pengumuman_viewed (id_pengumuman, id_user, id_siswa)
                VALUES ($id_pengumuman, $id_user, $id_siswa)
            ");
        }
    } elseif ($level === 'admin') {
        $query = mysqli_query($conn, "
            SELECT id_pengumuman 
            FROM pengumuman
            WHERE ditujukan='semua'
        ");
        while ($row = mysqli_fetch_assoc($query)) {
            $id_pengumuman = intval($row['id_pengumuman']);
            mysqli_query($conn, "
                INSERT IGNORE INTO pengumuman_viewed (id_pengumuman, id_user, id_siswa)
                VALUES ($id_pengumuman, $id_user, NULL)
            ");
        }
    }

    // Setelah ditandai, reset jumlah pengumuman menjadi 0 (untuk sesi ini)
    hitung_lowongan($akan_dibuka, $baru_dibuka, $conn);
    echo json_encode([
        'jumlah' => 0,
        'latest' => '0',
        'akan_dibuka' => $akan_dibuka,
        'baru_dibuka' => $baru_dibuka
    ]);
    exit;
}

// Hitung jumlah pengumuman belum dibaca (login)
if ($level === 'admin') {
    $q = mysqli_query($conn, "
        SELECT COUNT(*) AS jumlah 
        FROM pengumuman
        WHERE ditujukan='semua'
          AND id_pengumuman NOT IN (
              SELECT id_pengumuman 
              FROM pengumuman_viewed 
              WHERE id_user = $id_user
          )
    ");
    $row = mysqli_fetch_assoc($q);
    $jumlah = $row ? intval($row['jumlah']) : 0;
} elseif ($level === 'alumni' && $id_siswa !== null) {
    $q = mysqli_query($conn, "
        SELECT COUNT(*) AS jumlah 
        FROM pengumuman
        WHERE (ditujukan='semua' OR (ditujukan='khusus' AND id_siswa = $id_siswa))
          AND id_pengumuman NOT IN (
              SELECT id_pengumuman 
              FROM pengumuman_viewed 
              WHERE id_user = $id_user
          )
    ");
    $row = mysqli_fetch_assoc($q);
    $jumlah = $row ? intval($row['jumlah']) : 0;
}

// Lowongan
hitung_lowongan($akan_dibuka, $baru_dibuka, $conn);

// Respons akhir
echo json_encode([
    'jumlah' => $jumlah,
    'latest' => '0',
    'akan_dibuka' => $akan_dibuka,
    'baru_dibuka' => $baru_dibuka
]);
