<?php

$conn = mysqli_connect("localhost", "root", "", "sekolah_bkk");

//Start Function Data Admin

// Function Logika Tampil Admin
function tampilAdmin($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}

// Function Logika Tambah Admin
function tambahAdmin($data)
{
	global $conn;

	$email = htmlspecialchars($data['email']);
	$nama = htmlspecialchars($data['nama']);
	$telepon = htmlspecialchars($data['telepon']);
	$username = htmlspecialchars($data['username']);
	$password = md5($data['password']);

	// Cek apakah email sudah terdaftar
	$cek = mysqli_query($conn, "SELECT email FROM admin WHERE email = '$email'");
	if (mysqli_fetch_assoc($cek)) {
		header("Location: ../../pages/public/data-admin.php?email=duplikat");
		exit;
	}

	// Ambil semua kode_admin yang ada
	$result = mysqli_query($conn, "SELECT kode_admin FROM admin ORDER BY kode_admin ASC");
	$existingCodes = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$number = (int)substr($row['kode_admin'], 1);
		$existingCodes[] = $number;
	}
	$kodeBaru = 1;
	while (in_array($kodeBaru, $existingCodes)) {
		$kodeBaru++;
	}
	$kode_admin = 'A' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);

	// Insert ke admin
	$queryAdmin = "INSERT INTO admin (kode_admin, nama, email, telepon)
				   VALUES ('$kode_admin', '$nama', '$email', '$telepon')";

	if (mysqli_query($conn, $queryAdmin)) {
		// Insert ke user (username, password, level, kode_pengguna)
		$queryUser = "INSERT INTO user (username, password, level, kode_pengguna)
					  VALUES ('$username', '$password', 'admin', '$kode_admin')";
		mysqli_query($conn, $queryUser);
	}

	return mysqli_affected_rows($conn);
}

// Function Logika Setting Akun Admin
function settingAkunAdmin($data)
{
	global $conn;

	$kode_admin = htmlspecialchars($data['kode_admin']);
	$username = htmlspecialchars($data['username']);
	$password_input = htmlspecialchars($data['password']);

	// Cek apakah password diisi
	if (!empty($password_input)) {
		// Enkripsi password baru
		$password = md5($password_input);

		// Update username dan password
		$queryUser = "UPDATE user SET username = '$username', password = '$password', level = 'admin' 
					  WHERE kode_pengguna = '$kode_admin'";
	} else {
		// Hanya update username saja, password tetap
		$queryUser = "UPDATE user SET username = '$username', level = 'admin' 
					  WHERE kode_pengguna = '$kode_admin'";
	}

	mysqli_query($conn, $queryUser);

	return mysqli_affected_rows($conn);
}

// Function Logika Setting Akun Alumni
function settingAkunAlumni($data)
{
	global $conn;

	$kode_alumni = htmlspecialchars($data['kode_alumni']);
	$username = htmlspecialchars($data['username']);
	$password_input = htmlspecialchars($data['password']);

	// Cek apakah password diisi
	if (!empty($password_input)) {
		// Enkripsi password baru
		$password = md5($password_input);

		// Update username dan password
		$queryUser = "UPDATE user SET username = '$username', password = '$password', level = 'alumni' 
					  WHERE kode_pengguna = '$kode_alumni'";
	} else {
		// Hanya update username saja, password tetap
		$queryUser = "UPDATE user SET username = '$username', level = 'alumni' 
					  WHERE kode_pengguna = '$kode_alumni'";
	}

	mysqli_query($conn, $queryUser);

	return mysqli_affected_rows($conn);
}


// Function Logika Edit Admin
function editAdmin($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	$id_admin = htmlspecialchars($data['id_admin']);
	$email = htmlspecialchars($data['email']);
	$nama = htmlspecialchars($data['nama']);
	$telepon = htmlspecialchars($data['telepon']);

	// Update data di tabel admin
	mysqli_query($conn, "UPDATE admin 
						 SET nama = '$nama', email = '$email', telepon = '$telepon' 
						 WHERE id_admin = $id_admin");

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

// Function Logika Hapus Admin
function hapusAdmin($id_admin)
{
	global $conn;

	// Ambil id_akun dari id_admin dulu
	$queryGetAkun = "SELECT kode_admin FROM admin WHERE id_admin = $id_admin";
	$result = mysqli_query($conn, $queryGetAkun);

	if ($row = mysqli_fetch_assoc($result)) {
		$kode_pengguna = $row['kode_admin'];

		// Hapus dari tabel admin dulu
		$hapusAdmin = mysqli_query($conn, "DELETE FROM admin WHERE id_admin = $id_admin");

		// Lalu hapus dari tabel user
		$hapusAkun = mysqli_query($conn, "DELETE FROM user WHERE kode_pengguna = '$kode_pengguna'");

		// Kembalikan true jika keduanya berhasil
		if ($hapusAdmin && $hapusAkun) {
			return 1;
		}
	}

	return 0;
}


// End Function Data Admin

// Start Function Data Siswa

// Function Logika Tampil Admin
function tampilSiswa($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}

// Function Logika Tambah Admin
function tambahSiswa($data)
{
	global $conn;

	$nisn = htmlspecialchars($data['nisn']);
	$telepon = htmlspecialchars($data['telepon']);
	$nama = htmlspecialchars($data['nama']);
	$jurusan = htmlspecialchars($data['jurusan']);
	$tahun_lulus = htmlspecialchars($data['tahun_lulus']);
	$alamat = htmlspecialchars($data['alamat']);
	$username = htmlspecialchars($data['username']);
	$password = md5($data['password']);

	// // Cek apakah email sudah terdaftar
	// $cek = mysqli_query($conn, "SELECT email FROM alumni WHERE email = '$email'");
	// if (mysqli_fetch_assoc($cek)) {
	// 	header("Location: ../../pages/public/data-siswa.php?email=duplikat");
	// 	exit;
	// }

	// Ambil semua kode_alumni yang ada, urutkan ASC
	$result = mysqli_query($conn, "SELECT kode_alumni FROM alumni ORDER BY kode_alumni ASC");

	$existingCodes = [];
	while ($row = mysqli_fetch_assoc($result)) {
		// Ambil angka saja, misal A001 jadi 1
		$number = (int)substr($row['kode_alumni'], 1);
		$existingCodes[] = $number;
	}

	// Cari nomor terkecil yang belum dipakai
	$kodeBaru = 1;
	while (in_array($kodeBaru, $existingCodes)) {
		$kodeBaru++;
	}

	// Format jadi A001, A002, dst
	$kode_alumni = 'S' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);

	// Insert ke admin
	$queryAlumni = "INSERT INTO alumni (kode_alumni, nama, nisn, jurusan, tahun_lulus, telepon, alamat)
					   VALUES ('$kode_alumni', '$nama', '$nisn', '$jurusan', '$tahun_lulus', '$telepon', '$alamat')";

	if (mysqli_query($conn, $queryAlumni)) {
		// Insert ke user (username, password, level, kode_pengguna)
		$queryUser = "INSERT INTO user (username, password, level, kode_pengguna)
					  VALUES ('$username', '$password', 'alumni', '$kode_alumni')";
		mysqli_query($conn, $queryUser);
	}

	return mysqli_affected_rows($conn);
}


// Function Logika Edit Siswa
function editSiswa($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	$id_alumni = $data['id_alumni'];
	$nisn = htmlspecialchars($data['nisn']);
	$telepon = htmlspecialchars($data['telepon']);
	$nama = htmlspecialchars($data['nama']);
	$jurusan = htmlspecialchars($data['jurusan']);
	$tahun_lulus = htmlspecialchars($data['tahun_lulus']);
	$alamat = htmlspecialchars($data['alamat']);

	// Update data alumni
	mysqli_query($conn, "UPDATE alumni 
						 SET nama = '$nama', nisn = '$nisn', jurusan = '$jurusan', tahun_lulus = '$tahun_lulus',telepon = '$telepon', alamat = '$alamat' 
						 WHERE id_alumni = $id_alumni");

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

// Function Logika Hapus Siswa
function hapusSiswa($id_alumni)
{
	global $conn;

	// Ambil id_akun dari id_alumni dulu
	$queryGetAkun = "SELECT kode_alumni FROM alumni WHERE id_alumni = $id_alumni";
	$result = mysqli_query($conn, $queryGetAkun);

	if ($row = mysqli_fetch_assoc($result)) {
		$kode_pengguna = $row['kode_alumni'];

		// Hapus dari tabel alumni dulu
		$hapusAdmin = mysqli_query($conn, "DELETE FROM alumni WHERE id_alumni = $id_alumni");

		// Lalu hapus dari tabel user
		$hapusAkun = mysqli_query($conn, "DELETE FROM user WHERE kode_pengguna = '$kode_pengguna'");

		// Kembalikan true jika keduanya berhasil
		if ($hapusAdmin && $hapusAkun) {
			return 1;
		}
	}

	return 0;
}

// End Function Data Siswa

// Start Function Data Perusahaan

// Function Logika Tampil Perusahaan
function tampilPerusahaan($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}

//Function Logika Tambah Perusahaan
function tambahPerusahaan($data)
{
	global $conn;

	$email = htmlspecialchars($data['email']);
	$telepon = htmlspecialchars($data['telepon']);
	$nama = htmlspecialchars($data['nama']);
	$bidang_usaha = htmlspecialchars($data['bidang_usaha']);
	$alamat = htmlspecialchars($data['alamat']);
	$lat = $_POST['lat'];
	$lng = $_POST['lng'];

	// Cek Email Kosong atau Tidak
	if (empty($email)) {
		$email = "Tidak Ada Email Yang Tercantum";
	}

	// Cek No_Telepon Kosong Atau Tidak
	if (empty($telepon)) {
		$telepon = "Tidak Ada No-Telepon Yang Tercantum";
	}

	// Jalankan function upload logo
	$logo = uploadLogoPerusahaan();

	//Cek Apakah Logo di isi
	if ($logo === false) {
		$logo = 'default-logo.png';
	}

	if ($email !== 'Tidak Ada Email Yang Tercantum') {

		// Cek apakah email sudah terdaftar
		$cek = mysqli_query($conn, "SELECT email FROM perusahaan WHERE email = '$email'");
		if (mysqli_fetch_assoc($cek)) {
			header("Location: ../../pages/public/data-perusahaan.php?email=duplikat");
			exit;
		}
	}
	
	$queryPerusahaan = "INSERT INTO perusahaan (nama_perusahaan, email, telepon, alamat, latitude, longitude, bidang_usaha, logo) VALUES('$nama', '$email', '$telepon', '$alamat', '$lat', '$lng', '$bidang_usaha', '$logo')";
	mysqli_query($conn, $queryPerusahaan);

	return mysqli_affected_rows($conn);
}

//Function Logika Edit Perusahaan
function editPerusahaan($data)
{
	global $conn;

	$id_perusahaan = htmlspecialchars($data['id_perusahaan']);
	$email = htmlspecialchars($data['email']);
	$telepon = htmlspecialchars($data['telepon']);
	$nama = htmlspecialchars($data['nama']);
	$bidang_usaha = htmlspecialchars($data['bidang_usaha']);
	$alamat = htmlspecialchars($data['alamat']);
	$lat = isset($data['lat']) ? $data['lat'] : null;
	$lng = isset($data['lng']) ? $data['lng'] : null;

	$logoLama = $data['logoLama'];

	if ($_FILES['logo']['error'] === 4) {
		$logo = $logoLama;
	} else {
		// Jalankan function upload logo
		$logo = uploadLogoPerusahaan();
	}

	$updatePerusahaan = "UPDATE perusahaan SET nama_perusahaan = '$nama', email = '$email', telepon = '$telepon', alamat = '$alamat', latitude = '$lat', longitude = '$lng', bidang_usaha = '$bidang_usaha', logo = '$logo' WHERE id_perusahaan = $id_perusahaan";
	mysqli_query($conn, $updatePerusahaan);

	return mysqli_affected_rows($conn);
}

// Function Logika Hapus Perusahaan
function hapusPerusahaan($id)
{
	global $conn;

	$file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM perusahaan WHERE id_perusahaan = $id"));

	// Hapus gambar logo perusahaan jika ada
	$namaLogo = $file["logo"];
	$logoperusahaan = __DIR__ . '/assets/img/perusahaan/logo/' . $namaLogo;
	if ($namaLogo !== 'default-logo.png' && file_exists($logoperusahaan)) {
		unlink($logoperusahaan);
	}

	// Jalankan query hapus data dari database
	mysqli_query($conn, "DELETE FROM perusahaan WHERE id_perusahaan = $id");

	return mysqli_affected_rows($conn);
}

// Function Logika Upload Gambar Alumni
function uploadLogoPerusahaan()
{
	// Ambil beberapa data dari file logo yang di input dari variabel superglobal PHP yaitu $_FILES
	$namaFile = $_FILES['logo']['name'];
	$ukuranFile = $_FILES['logo']['size'];
	$tmpName = $_FILES['logo']['tmp_name'];

	// Cek apakah yang di upload gambar atau bukan

	// Buat array yang berisi ekstensi file yang diperbolehkan
	$ekstensiGambarValid = ["jpg", "jpeg", "png"];
	$ekstensiGambar = explode('.', $namaFile);
	$ekstensiGambar = strtolower(end($ekstensiGambar));

	// Cek jika ekstensi nya tidak sama dengan yang diperbolehkan
	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "
            <script>
                alert('Yang Anda Upload Bukan Gambar!);
            </script>
        ";
		return false;
	}

	// Cek jika ukuran gambar terlalu besar
	if ($ukuranFile > 5000000) {
		echo "
            <script>
                alert('Ukuran Gambar Terlalu Besar!');
            </script>
        ";
		return false;
	}

	// Lolos pengecekan, gambar siap di upload
	// Generate nama gambar baru
	$namaFileBaru = uniqid();
	$namaFileBaru .= '.';
	$namaFileBaru .= $ekstensiGambar;

	// Jalankan function milik PHP untuk mengupload file
	move_uploaded_file($tmpName, '../../src/assets/img/perusahaan/logo/' . $namaFileBaru);

	// Kembalikan nama file baru
	return $namaFileBaru;
}

// End Function Data Perusahaan

function getPerusahaan()
{
	global $conn;
	$result = mysqli_query($conn, "SELECT * FROM perusahaan ORDER BY nama_perusahaan ASC");

	$perusahaan = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$perusahaan[] = $row;
	}

	return $perusahaan;
}

// Start Function Loker

// Function Logika Tampil Loker
function tampilLoker($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}

//Function Logika Dormat Nominal Uang Menjadi Singkat
function formatUangSingkat($angka)
{
	// Ubah string jadi float
	$angka = (float) str_replace(['.', ','], ['', '.'], $angka);

	if ($angka >= 1000000000) {
		$nilai = floor($angka / 10000000) / 100; // ambil 2 angka desimal tanpa bulat
		$satuan = 'M';
	} elseif ($angka >= 1000000) {
		$nilai = floor($angka / 10000) / 100;
		$satuan = 'jt';
	} elseif ($angka >= 1000) {
		$nilai = floor($angka / 10) / 100;
		$satuan = 'rb';
	} else {
		$nilai = $angka;
		$satuan = '';
	}

	// Pisahkan desimal
	$pecahan = explode('.', (string)$nilai);
	$angkaUtama = $pecahan[0];
	$desimal = isset($pecahan[1]) ? substr($pecahan[1], 0, 2) : '';

	// Jika ada desimal dan bukan "00", tampilkan
	if ($desimal && intval($desimal) > 0) {
		return $angkaUtama . ',' . $desimal . $satuan;
	} else {
		return $angkaUtama . $satuan;
	}
}

function formatPeriodeGaji($periode)
{
	switch (strtoupper($periode)) {
		case 'H':
			return 'Hari';
		case 'M':
			return 'Minggu';
		case 'B':
			return 'Bulan';
		case 'T':
			return 'Tahun';
		default:
			return '';
	}
}

//Function Logika Tambah Loker
function tambahLoker($data)
{
	global $conn;

	$judul = htmlspecialchars($data['judul']);
	$deskripsi = htmlspecialchars($data['deskripsi']);
	$persyaratan = htmlspecialchars(implode(",", $data['persyaratan'] ?? []));
	$gaji = htmlspecialchars($data['gaji']);
	$mata_uang = htmlspecialchars($data['mata_uang']);
	$kpn_gaji_diberi = htmlspecialchars($data['kpn_gaji_diberi']);
	$tanggal_dibuka = htmlspecialchars($data['tanggal_dibuka']);
	$tanggal_ditutup = htmlspecialchars($data['tanggal_ditutup']);
	$id_perusahaan = $data['perusahaan'];

	//Cek Persyaratan di isi atau tidak
	if (empty($persyaratan) && !is_array($persyaratan)) {
		$persyaratan = 'Tidak ada persyaratan';
	}

	//Cek Deskripsi di isi atau tidak
	if (empty($deskripsi)) {
		$deskripsi = 'Tidak ada deskripsi';
	}

	// Query untuk menambahkan loker
	$queryLoker = "INSERT INTO lowongan (judul, deskripsi, persyaratan, mata_uang, gaji, kpn_gaji_diberi, tanggal_dibuka, tanggal_ditutup, id_perusahaan) 
				   VALUES('$judul', '$deskripsi', '$persyaratan', '$mata_uang', '$gaji', '$kpn_gaji_diberi', '$tanggal_dibuka', '$tanggal_ditutup', '$id_perusahaan')";
	$result = mysqli_query($conn, $queryLoker);

	// Pengumuman untuk semua user
	if ($result) {
		// Ambil nama perusahaan
		$nama_perusahaan = '';
		$res = mysqli_query($conn, "SELECT nama_perusahaan FROM perusahaan WHERE id_perusahaan = '$id_perusahaan'");
		if ($row = mysqli_fetch_assoc($res)) {
			$nama_perusahaan = $row['nama_perusahaan'];
		}
		// Validasi tanggal dibuka
		$today = date('Y-m-d');
		if ($tanggal_dibuka > $today) {
			// Loker akan dibuka beberapa hari lagi
			$selisih = (strtotime($tanggal_dibuka) - strtotime($today)) / 86400;
			$judul_pengumuman = "Lowongan Baru (Segera Dibuka)";
			$isi_pengumuman = "Lowongan <b>$judul</b> di perusahaan <b>$nama_perusahaan</b> akan dibuka <b>$selisih hari lagi</b> pada tanggal <b>$tanggal_dibuka</b>.";
		} else {
			// Loker langsung dibuka
			$judul_pengumuman = "Lowongan Baru Dibuka";
			$isi_pengumuman = "Telah dibuka Lowongan baru di Perusahaan <b>$nama_perusahaan</b> dengan Judul : <b>$judul</b>.";
		}
		tambahPengumuman($judul_pengumuman, $isi_pengumuman, 'semua', null);
	}

	return mysqli_affected_rows($conn);
}

// Pengumuman loker berakhir
function cekPengumumanLokerBerakhir()
{
	global $conn;
	$today = date('Y-m-d');
	$query = "SELECT * FROM lowongan WHERE tanggal_ditutup = '$today'";
	$result = mysqli_query($conn, $query);

	while ($row = mysqli_fetch_assoc($result)) {
		$id_perusahaan = $row['id_perusahaan'];
		$judul_loker = $row['judul'];

		// Ambil nama perusahaan sesuai id_perusahaan loker
		$nama_perusahaan = '';
		$res = mysqli_query($conn, "SELECT nama_perusahaan FROM perusahaan WHERE id_perusahaan = '$id_perusahaan'");
		if ($perusahaan = mysqli_fetch_assoc($res)) {
			$nama_perusahaan = $perusahaan['nama_perusahaan'];
		}

		// Cek apakah pengumuman sudah pernah dibuat hari ini untuk loker ini
		$judul_pengumuman = "Loker $judul_loker Telah Berakhir";
		$cek = mysqli_query($conn, "SELECT 1 FROM pengumuman WHERE judul = '$judul_pengumuman' AND DATE(tanggal) = '$today'");
		if (!mysqli_fetch_assoc($cek)) {
			$isi_pengumuman = "Lowongan kerja <b>$judul_loker</b> di perusahaan <b>$nama_perusahaan</b> telah berakhir hari ini.";
			tambahPengumuman($judul_pengumuman, $isi_pengumuman, 'semua', null);
		}
	}
}

// Function Logika Edit Perusahaan
function editLoker($data)
{
	global $conn;

	$id_lowongan = htmlspecialchars($data['id_lowongan']);
	$judul = htmlspecialchars($data['judul']);
	$deskripsi = htmlspecialchars($data['deskripsi']);
	$mata_uang = htmlspecialchars($data['mata_uang']);
	$kpn_gaji_diberi = htmlspecialchars($data['kpn_gaji_diberi']);
	$gaji = htmlspecialchars($data['gaji']);
	$tanggal_dibuka = htmlspecialchars($data['tanggal_dibuka']);
	$tanggal_ditutup = htmlspecialchars($data['tanggal_ditutup']);
	$id_perusahaan = htmlspecialchars($data['perusahaan']);

	$persyaratan = isset($data['persyaratan']) && is_array($data['persyaratan'])
		? implode(',', $data['persyaratan'])
		: 'Tidak ada persyaratan';

	$query = "UPDATE lowongan SET 
              judul = '$judul', 
              deskripsi = '$deskripsi', 
              persyaratan = '$persyaratan',
			  mata_uang = '$mata_uang', 
              gaji = '$gaji',
			  kpn_gaji_diberi = '$kpn_gaji_diberi', 
              tanggal_dibuka = '$tanggal_dibuka', 
              tanggal_ditutup = '$tanggal_ditutup', 
              id_perusahaan = '$id_perusahaan' 
              WHERE id_lowongan = $id_lowongan";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}
// Function Logika Hapus loker
function hapusLoker($id)
{
	global $conn;

	// Ambil data loker sebelum dihapus
	$loker = mysqli_fetch_assoc(mysqli_query($conn, "SELECT judul, id_perusahaan FROM lowongan WHERE id_lowongan = $id"));
	$judul_loker = $loker['judul'] ?? '';
	$id_perusahaan = $loker['id_perusahaan'] ?? 0;
	$nama_perusahaan = '';
	if ($id_perusahaan) {
		$perusahaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_perusahaan FROM perusahaan WHERE id_perusahaan = $id_perusahaan"));
		$nama_perusahaan = $perusahaan['nama_perusahaan'] ?? '';
	}

	// Jalankan query hapus data dari database
	mysqli_query($conn, "DELETE FROM lowongan WHERE id_lowongan = $id");

	// Tambahkan pengumuman jika loker dihapus
	if ($judul_loker) {
		$judul_pengumuman = "Lowongan Dihapus";
		$isi_pengumuman = "Lowongan <b>$judul_loker</b>" . ($nama_perusahaan ? " di perusahaan <b>$nama_perusahaan</b>" : "") . " telah dihapus oleh admin.";
		// Hapus field keterangan karena tidak ada di tabel
		tambahPengumuman($judul_pengumuman, $isi_pengumuman, 'semua', null);
	}

	return mysqli_affected_rows($conn);
}

// End Function Loker

// Start Function Data Lamaran Alumni

// Function Logika Hapus Lamaran
function hapusLamaran($id)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query hapus data dari database
	mysqli_query($conn, "DELETE FROM lamaran WHERE id_lamaran = $id");

	// Kembalikan jumlah baris yang terpengaruh
	return mysqli_affected_rows($conn);
}

// Function Logika Set Status / Ubah Status
function setStatusLamaran($data)
{
	global $conn;

	$id_lamaran = htmlspecialchars($data['id_lamaran']);
	$status = htmlspecialchars($data['status']);

	$query = "UPDATE lamaran SET status = '$status' WHERE id_lamaran = $id_lamaran";
	mysqli_query($conn, $query);

	// Ambil info lamaran untuk pengumuman
	$lamaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lamaran WHERE id_lamaran = $id_lamaran"));
	$lowongan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT judul FROM lowongan WHERE id_lowongan = {$lamaran['id_lowongan']}"));

	// Pastikan id_siswa adalah id_alumni (bukan id_user)
	$id_siswa = intval($lamaran['id_siswa']);

	// Pengumuman hanya untuk status Diterima Kerja atau Tidak Diterima Kerja
	if ($status == 'Diterima Kerja') {
		$judul_pengumuman = "Selamat! Lamaran Diterima";
		$isi_pengumuman = "Lamaran anda untuk posisi <b>{$lowongan['judul']}</b> telah <b>DITERIMA</b>.";
		tambahPengumuman($judul_pengumuman, $isi_pengumuman, 'khusus', $id_siswa);
	} else if ($status == 'Tidak Diterima Kerja') {
		$judul_pengumuman = "Maaf, Lamaran Tidak Diterima";
		$isi_pengumuman = "Lamaran anda untuk posisi <b>{$lowongan['judul']}</b> <b>TIDAK DITERIMA</b>.";
		tambahPengumuman($judul_pengumuman, $isi_pengumuman, 'khusus', $id_siswa);
	}

	return mysqli_affected_rows($conn);
}

function tambahPengumuman($judul, $isi, $ditujukan = 'semua', $id_siswa = null)
{
	global $conn;
	date_default_timezone_set('Asia/Jakarta');
	$judul = mysqli_real_escape_string($conn, $judul);
	$isi = mysqli_real_escape_string($conn, $isi);
	$tanggal = date('Y-m-d H:i:s');
	$ditujukan = ($ditujukan == 'khusus') ? 'khusus' : 'semua';
	$id_siswa_sql = ($id_siswa !== null) ? intval($id_siswa) : 'NULL';
	$query = "INSERT INTO pengumuman (judul, isi, tanggal, ditujukan, id_siswa) VALUES ('$judul', '$isi', '$tanggal', '$ditujukan', $id_siswa_sql)";
	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);

	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}
// End Function Data Lamaran Alumni