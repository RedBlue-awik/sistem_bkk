<?php
session_start();

require '../../src/functions.php';
include '../../src/controller/LoginF.php';

if (isset($_POST['kirim'])) {
    $id_alumni = $_POST['id_alumni'];
    $id_lowongan = $_POST['id_lowongan'];
    $tanggal = date('Y-m-d');
    $status = 'Menunggu';
    $cv = $_FILES['cv'];

    // Ambil ekstensi file yang diupload (huruf kecil semua)
    $ekstensi_file = strtolower(pathinfo($cv['name'], PATHINFO_EXTENSION));

    // Daftar ekstensi yang diperbolehkan
    $ekstensi_diperbolehkan = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

    if (!in_array($ekstensi_file, $ekstensi_diperbolehkan)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
          Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Didukung',
            text: 'File harus berformat PDF, Word (.doc/.docx), atau Excel (.xls/.xlsx).',
          }).then(() => {
            window.history.back();
          });
        </script>";
        exit; // Hentikan script jika format tidak sesuai
    }

    // Nama file custom: cv_{id_alumni}_{id_lowongan}.{ext}
    $nama_file = "cv_" . $id_alumni . "_" . $id_lowongan . "." . $ekstensi_file;
    $folder_tujuan = "../../src/assets/persyaratan/cv/";

    if (!is_dir($folder_tujuan)) {
        mkdir($folder_tujuan, 0777, true);
    }

    $path_upload = $folder_tujuan . $nama_file;

    if (move_uploaded_file($cv['tmp_name'], $path_upload)) {
        // Simpan lamaran tanpa kolom cv
        $sql = "INSERT INTO lamaran (id_siswa, id_lowongan, tanggal_lamar, status) 
                VALUES ('$id_alumni', '$id_lowongan', '$tanggal', '$status')";
        if (mysqli_query($conn, $sql)) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
              Swal.fire({
                icon: 'success',
                title: 'Lamaran Dikirim',
                text: 'Lamaran berhasil dikirim!',
              }).then(() => {
                window.location.href = './loker.php';
              });
            </script>";
        } else {
            echo "Gagal menyimpan lamaran.";
        }
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
          Swal.fire({
            icon: 'error',
            title: 'Upload Gagal',
            text: 'CV gagal diupload!',
          }).then(() => {
            window.history.back();
          });
        </script>";
    }
}

function getLoker()
{
    global $conn;

    $query = "SELECT lowongan.*, perusahaan.nama_perusahaan, perusahaan.logo, perusahaan.alamat, perusahaan.bidang_usaha
              FROM lowongan 
              JOIN perusahaan ON lowongan.id_perusahaan = perusahaan.id_perusahaan 
              ORDER BY lowongan.id_lowongan DESC";

    $result = mysqli_query($conn, $query);

    $loker = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Format gaji langsung
        $angka = str_replace(['.', ','], ['', '.'], $row['gaji']);
        $row['gaji_full'] = $row['mata_uang'] . ' ' . formatUangSingkat($angka) . '/' . $row['kpn_gaji_diberi'];

        // Ubah persyaratan ke array
        if (is_string($row['persyaratan'])) {
            $row['persyaratan'] = explode(',', $row['persyaratan']);
        }

        $loker[] = $row;
    }

    return $loker;
}

$daftarperusahaan = getPerusahaan();
$daftarLoker = getLoker();

if (isset($_GET['id_lowongan'])) {
    $id = intval($_GET['id_lowongan']);
    $query = mysqli_query($conn, "SELECT lowongan.*, perusahaan.nama_perusahaan, perusahaan.logo, perusahaan.alamat, perusahaan.bidang_usaha
              FROM lowongan 
              JOIN perusahaan ON lowongan.id_perusahaan = perusahaan.id_perusahaan 
              WHERE lowongan.id_lowongan = $id");
    $data = mysqli_fetch_assoc($query);
} else {
    echo "ID tidak valid.";
    exit;
}

/**
 * Cari file CV berdasarkan id_alumni dan id_lowongan
 * @param int $id_alumni
 * @param int $id_lowongan
 * @return string|null  // path relatif jika ada, null jika tidak ada
 */
function getCvFile($id_alumni, $id_lowongan)
{
    $folder = "../../src/assets/persyaratan/cv/";
    $allowed_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    foreach ($allowed_ext as $ext) {
        $filename = "cv_{$id_alumni}_{$id_lowongan}.{$ext}";
        $filepath = $folder . $filename;
        if (file_exists($filepath)) {
            return $filepath;
        }
    }
    return null;
}

$alamat = $data['alamat'];
?>

<!DOCTYPE html>
<html lang="en">

<?php
$title = "Details-Lowongan";
include '../../src/template/headers.php';
?>

<style>
    body {
        font-family: "Poppins", sans-serif;
        min-height: 100vh;
        background-color: rgb(255, 255, 255);
    }

    .swal2-popup {
        font-family: "Poppins", sans-serif;
    }

    /* Konten centering: no offset on mobile, offset on desktop */
    .content {
        padding: 1.5rem;
    }

    /* Container inside content always centered */
    .content .container {
        max-width: 1140px;
        margin: 0 auto;
    }

    .job-card {
        transition: transform .2s, box-shadow .2s;
        cursor: pointer;
    }

    .job-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
    }

    .mapslink {
        font-size: 14px !important;
    }

    .linkmaps {
        text-decoration: none;
    }

    .mapsLink {
        font-size: 12px !important;
    }

    .linkMaps {
        text-decoration: none;
    }

    @media (max-width: 1170px) {
        .mapsLink {
            font-size: 11px !important;
        }

    }

    @media (max-width: 768px) {
        .info-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
    }

    @media (max-width: 576px) {
        .job-card h5 {
            font-size: 1rem;
        }

        .job-card p,
        .job-card small {
            font-size: 0.85rem;
        }

        .info-header h4 {
            font-size: 1rem;
        }

        .info-header p {
            font-size: 0.85rem;
        }

        .info-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .info {
            font-size: 0.95rem;
        }

        .mapslink {
            font-size: 12px !important;
        }

        .mapsLink {
            font-size: 10px !important;
        }

    }

    @media (max-width: 400px) {
        .info-header h4 {
            font-size: .68rem;
        }

        .info-header p {
            font-size: .53rem;
        }

        .info-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .info {
            font-size: .80rem;
        }

        .mapslink {
            font-size: 10px !important;
        }

        .mapsLink {
            font-size: 9px;
        }

    }
</style>

<body>

    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content-->
            <div class="app-container">
                <!--begin::Container-->
                <div class="container-fluid">
                    <div class="mb-4 p-4 border-bottom border-2 detail-header">
                        <div class="row align-items-start g-3">
                            <div class="col-12 col-md-8 d-flex align-items-start gap-3 flex-wrap info-header">
                                <div style="flex-shrink: 0;">
                                    <img src="../../src/assets/img/perusahaan/logo/<?= $data['logo'] ?>" alt="Logo Perusahaan" class="img-fluid" style="width: 90px; height: 90px; object-fit: contain;">
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-1"><?= $data['nama_perusahaan'] ?></h4>
                                    <p class="text-muted mb-1"><?= $data['judul'] ?></p>

                                    <div class="d-flex flex-wrap align-items-baseline gap-3 mt-3 flex-column info">
                                        <span class="mapslink"><?= '<a class="linkMaps icon-link icon-link-hover" style="--bs-icon-link-transform: translate3d(0, -.200rem, 0); "  href="https://www.google.com/maps?q=' . urlencode($alamat) . '" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16"> <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/> </svg>' . $alamat . '</a>'; ?></span>
                                        <span class="text-muted"><i class="bi bi-cash-stack" style="margin-right: .33rem;"></i><?= $data['mata_uang'] . ' ' . formatUangSingkat($data['gaji']) . '/' . formatPeriodeGaji($data['kpn_gaji_diberi']) ?>
                                        </span>
                                        <span class="text-muted"><i class="bi bi-building me-1"></i><?= $data['bidang_usaha'] ?></span>
                                        <?php if (strtotime($data['tanggal_ditutup']) < time()) : ?>
                                            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-clock" style="font-size: 15px; margin-right: .33rem;"></i><?= $data['tanggal_ditutup'] ?> <?php $tanggal_tutup = strtotime($data['tanggal_ditutup']);
                                                                                                                                                                                                        $hari_ini = strtotime(date('Y-m-d'));
                                                                                                                                                                                                        $selisih_hari = ceil(($hari_ini - $tanggal_tutup) / 86400); ?> <strong>Sudah di Tutup <?= $selisih_hari ?> Hari Lalu</strong></span>
                                            <span class="text-danger fw-semibold"><i class="fa-regular fa-circle-xmark  me-1"></i>Lowongan ini telah ditutup</span>
                                        <?php elseif (strtotime($data['tanggal_dibuka']) > time()) : ?>
                                            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-clock" style="font-size: 15px; margin-right: .33rem;"></i><?= $data['tanggal_dibuka'] ?> <?php $tanggal_dibuka = strtotime($data['tanggal_dibuka']);
                                                                                                                                                                                                        $selisih_hari = ceil(($tanggal_dibuka - $hari_ini) / 86400); ?> <strong>Dibuka <?= $selisih_hari ?> Hari Lagi</strong></span>
                                            <span class="text-danger fw-semibold"><i class="fa-regular fa-clock fa-shake me-1"></i>Lowongan ini belum dibuka</span>
                                        <?php else : ?>
                                            <span class="text-muted" style="font-size: 12px;"><i class="bi bi-clock" style="font-size: 15px; margin-right: .33rem;"></i><?= $data['tanggal_dibuka'] ?> -- <?= $data['tanggal_ditutup'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-3">
                                        <?php if (strtotime($data['tanggal_ditutup']) < time()) : ?>
                                            <a href="./loker.php" class="btn btn-sm px-5 btn-primary">Cari Lamaran Lainnya</a>
                                        <?php elseif (strtotime($data['tanggal_dibuka']) > time()) : ?>
                                            <a href="./loker.php" class="btn btn-sm px-5 btn-primary">Cari Lamaran Lainnya</a>
                                        <?php elseif (isset($_SESSION['level']) && $_SESSION['level'] === 'alumni') : ?>
                                            <a href="" data-bs-toggle="modal" data-bs-target="#modalSyarat<?= $data['id_lowongan']; ?>" class="btn btn-sm px-4 btn-outline-primary">Lamar</a>
                                        <?php elseif (isset($_SESSION['level']) && $_SESSION['level'] === 'admin') : ?>
                                            <a href="../../logout.php" class="btn btn-sm btn-outline-primary">Khusus Alumni</a>
                                        <?php else : ?>
                                            <a href="" data-bs-toggle="modal" data-bs-target="#Modallogin" class="btn btn-sm px-4 btn-outline-primary text-center"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <?php if (!empty($data['persyaratan'])) : ?>
                                    <div class="">
                                        <h5 class="fw-semibold mb-3 mt-n2">Persyaratan</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php
                                            $tags = is_array($data['persyaratan']) ? $data['persyaratan'] : explode(',', $data['persyaratan']);
                                            foreach ($tags as $tag) :
                                            ?>
                                                <span class="badge bg-light border text-dark"><?= trim($tag) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <strong>Deskripsi Pekerjaan</strong>
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?= $data['deskripsi'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid mt-4">
                        <div class="p-3 bg-info bg-opacity-10 border border-info border-start-0 border-end-0 text-center">
                            <h4>Lowongan Kerja Lainnya</h4>
                        </div>
                        <div class="d-flex flex-row flex-nowrap overflow-auto px-2 gap-3 my-3">
                            <!-- Daftar Loker -->
                            <?php
                            $saranLoker = getLoker();
                            $saranLoker = array_filter($saranLoker, function ($loker) use ($data) {
                                return $loker['id_lowongan'] != $data['id_lowongan'];
                            });
                            foreach ($saranLoker as $loker) :
                                $isTutup = strtotime($loker['tanggal_ditutup']) < time();
                                $isBelumBuka = strtotime($loker['tanggal_dibuka']) > time();
                                $alamat = $loker['alamat'];
                            ?>
                                <div class="col-12 col-sm-5 col-md-6 col-lg-5">
                                    <div data-id="<?= $loker['id_lowongan'] ?>" class="card-click card job-card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>
                                                    <h5 class="card-title"><?= $loker['judul']; ?></h5>
                                                </span>
                                                <span class="text-muted"><strong><?= $loker['gaji_full']; ?></strong></span>
                                            </div>
                                            <div class="mb-3"><span class="badge bg-success p-2 text-uppercase"><?= $loker['bidang_usaha'] ?></span></div>
                                            <ul class="list-unstyled">
                                                <li class="mb-1"><strong>Nama Perusahaan:</strong><br><span class="badge bg-primary"> <?= $loker['nama_perusahaan']; ?> </span></li>
                                            </ul>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <img src="../../src/assets/img/perusahaan/logo/<?= $loker['logo']; ?>" alt="Logo Perusahaan" class="img-thumbnail" style="max-width: 40px; max-height: 40px;">
                                                    <div class="mt-1 ms-2 d-flex flex-column">
                                                        <span class="mb-1 mapsLink"><?= '<a class="linkMaps icon-link icon-link-hover"  href="https://www.google.com/maps?q=' . urlencode($alamat) . '" target="_blank">' . $alamat . '</a>'; ?></span>
                                                        <?php
                                                        if ($isTutup) {
                                                            $tanggal_tutup = strtotime($loker['tanggal_ditutup']);
                                                            $hari_ini = strtotime(date('Y-m-d'));
                                                            $selisih_hari = ceil(($hari_ini - $tanggal_tutup) / 86400);
                                                        ?>
                                                            <span class="text-muted" style="font-size: 12px;">
                                                                <strong>Sudah di Tutup <?= $selisih_hari ?> Hari Lalu</strong>
                                                            </span>
                                                        <?php } elseif ($isBelumBuka) {
                                                            $tanggal_dibuka = strtotime($loker['tanggal_dibuka']);
                                                            $hari_ini = strtotime(date('Y-m-d'));
                                                            $selisih_hari = ceil(($tanggal_dibuka - $hari_ini) / 86400);
                                                        ?>
                                                            <span class="text-muted" style="font-size: 12px;">
                                                                <strong>Dibuka <?= $selisih_hari ?> Hari Lagi</strong>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="time" style="font-size: 10px;">
                                                                <?= $loker['tanggal_dibuka'] ?> -- <?= $loker['tanggal_ditutup'] ?>
                                                            </span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column align-items-center">
                                                    <?php if ($isTutup) : ?>
                                                        <a href="./loker.php" class="btn btn-xs btn-primary" style="font-size:0.85rem;">Lainnya</a>
                                                    <?php elseif ($isBelumBuka) : ?>
                                                        <a href="./loker.php" class="btn btn-xs btn-primary" style="font-size:0.85rem;">Lainnya</a>
                                                    <?php elseif (isset($_SESSION['level']) && $_SESSION['level'] === 'alumni') : ?>
                                                        <a href="" data-bs-toggle="modal" data-bs-target="#modalSyarat<?= $loker['id_lowongan']; ?>" class="btn btn-sm px-4 btn-outline-primary">Lamar</a>
                                                    <?php elseif (isset($_SESSION['level']) && $_SESSION['level'] === 'admin') : ?>
                                                        <a href="../../logout.php" class="btn btn-sm btn-outline-primary">Khusus Alumni</a>
                                                    <?php else : ?>
                                                        <a href="" data-bs-toggle="modal" data-bs-target="#Modallogin" class="btn btn-sm px-4 btn-outline-primary text-center"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::App Content-->
        </main>
        <!--end::App Main-->
    </div>
    <!--begin::Footer-->
    <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
            <?php
            $tahun_sekarang = date("Y");
            ?>
            Copyright &copy; <?= $tahun_sekarang; ?>&nbsp;
            <a href="#" class="text-decoration-none">SMK MAMBA'UL IHSAN</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
    </footer>
    <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <?php include '../../src/template/modalForm.php'; ?>

    <?php foreach ($daftarLoker as $loker) : ?>
        <!--begin::Modal Syarat -->
        <div class="modal fade" id="modalSyarat<?= $loker['id_lowongan']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <form action="../../src/config/proses-lamaran.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Lamar Lowongan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_lowongan" value="<?= $loker['id_lowongan']; ?>">
                            <input type="hidden" name="id_alumni" value="<?= $_SESSION['id_pengguna'] ?>">
                            <div class="mb-3">
                                <label for="cv">Upload CV</label>
                                <input type="file" name="cv" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="kirim" class="btn btn-primary">Kirim Lamaran</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End::Modal Syarat -->
    <?php endforeach; ?>

    <!-- Script -->
    <?php
    include '../../src/template/footer.php';
    ?>

    <!-- Begin::Details -->
    <script>
        document.querySelectorAll('.card-click').forEach(card => {
            card.addEventListener('click', function(e) {
                // Cegah navigasi kalau klik tombol (yang ada <a> di dalamnya)
                if (e.target.closest('a')) return;
                const id = this.getAttribute('data-id');
                window.location.href = `./detail_loker.php?id_lowongan=${id}`;
            });
        });
    </script>
    <!-- End::Details -->

</body>

</html>