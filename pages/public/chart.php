<?php
// Syarat untuk menggunakan session
session_start();

// Cek apakah sudah ada session login, jika sudah kembalikan
if (!isset($_SESSION['id_pengguna'])) {
  echo "
        <script>
            document.location.href = '../../index.php';
        </script>
    ";
}

require '../../src/functions.php';

// Ambil data dari database
$query = mysqli_query($conn, "
    SELECT p.nama_perusahaan, COUNT(*) AS total
    FROM lamaran lam
    JOIN lowongan l ON lam.id_lowongan = l.id_lowongan
    JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan
    GROUP BY p.id_perusahaan
");

$labels = [];
$data = [];

while ($row = mysqli_fetch_assoc($query)) {
  $labels[] = $row['nama_perusahaan'];
  $data[] = (int)$row['total'];
}
?>

<!doctype html>
<html lang="en">
<!--begin::Head-->

<?php
$title = "Statistics";
include '../../src/template/headers.php';
?>

<style>
  body {
    font-family: 'Poppins', sans-serif;
  }

  #chart-container {
    width: 100%;
    max-width: 700px;
    margin: auto;
  }

  canvas {
    width: 100% !important;
    height: auto !important;
  }

  .Bt {
    padding: 10px 15px;
    margin: 10px 5px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    transition: transform 0.3s;
  }

  .Bt:hover {
    background: #0056b3;
  }

  .Bt:hover i {
    animation: spin 1.4s infinite linear;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>

</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <!--begin::App Wrapper-->
  <div class="app-wrapper">
    <!--begin::Header-->
    <nav class="app-header navbar sticky-top navbar-expand bg-primary-subtle shadow" data-bs-theme="dark">
      <!--begin::Container-->
      <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
              <i class="bi bi-list" style="color: white;"></i>
            </a>
          </li>
        </ul>
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">

          <!--begin::User Menu Dropdown-->
          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
              <img
                src="../../src/assets/img/logo.png"
                class="user-image rounded-circle shadow"
                alt="User Image" />
              <span class="d-none d-md-inline"><?= $_SESSION["nama"]; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <!--begin::User Image-->
              <li class="user-header bg-secondary-subtle">
                <?php if ($_SESSION["gambar"] !== "") : ?>
                  <img
                    src="../../dist/assets/img/user2-160x160.jpg"
                    class="rounded-circle shadow"
                    alt="User Image" />
                <?php endif; ?>
                <p class="fw-semibold text-light">Nama</p>
                <span class="badge bg-warning-subtle p-2 fs-5 px-3 mb-1"><?= $_SESSION["nama"]; ?></span>
                <p class="fw-semibold text-light">Status</p>
                <?php $kondisi = ($_SESSION["level"] == "admin") ? 'bg-info-subtle' : 'bg-success-subtle' ?>
                <span class="badge <?= $kondisi ?> p-2 fs-6 px-3 mb-1"><?= $_SESSION["level"]; ?></span>
              </li>
              <!--end::User Image-->
              <!--begin::Menu Footer-->
              <li class="user-footer">
                <a href="./pengumuman-all.php" class="btn btn-default btn-flat" data-bs-trigger="hover" data-bs-placement="right" data-bs-custom-class="custom-tooltip-Bell" data-bs-title="Pengumuman"><i class="bi bi-bell"></i><span class="badge bg-danger float-end d-none badgePengumuman">0</span></a>
                <a href="../../logout.php" class="btn btn-default btn-flat float-end btn-logout" data-bs-trigger="hover" data-bs-placement="left" data-bs-custom-class="custom-tooltip-logout" data-bs-title="LogOut ( Keluar )"><i class="bi bi-box-arrow-right"></i></a>
              </li>
              <!--end::Menu Footer-->
            </ul>
          </li>
          <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
      </div>
      <!--end::Container-->
    </nav>
    <!--end::Header-->
    <!--begin::Sidebar-->
    <?php include('../../src/template/menu.php'); ?>
    <!--end::Sidebar-->
    <!--begin::App Main-->
    <main class="app-main">
      <!--begin::App Content Header-->
      <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Row-->
          <div class="row">
            <div class="col-sm-6">
              <h3 class="mb-0">Statistics <i class="fas fa-chart-line"></i></h3>
            </div>

          </div>
          <!--end::Row-->
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content Header-->
      <!--begin::App Content-->
      <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Row-->
          <div class="row">
            <div id="chart-container">
              <canvas id="lamaranChart"></canvas>
            </div>

            <div class="d-flex justify-content-center mt-2">
              <button class="Bt pe-auto text-white bg-primary" data-bs-trigger="hover" data-bs-placement="top" data-bs-custom-class="custom-tooltip-Spin" data-bs-title="Ubah ke Pie" onclick="toggleChart()"><i class="fa-solid fa-arrows-rotate"></i></button>
            </div>


          </div>
          <!--end::Row-->
          <!--begin::Row-->

          <!-- /.row (main row) -->
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content-->
    </main>
    <!--end::App Main-->
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
        <a href="" class="text-decoration-none">SMK MAMBA'UL IHSAN</a>.
      </strong>
      All rights reserved.
      <!--end::Copyright-->
    </footer>
    <!--end::Footer-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->

  <?php
  include '../../src/template/footer.php';
  ?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    const labels = <?= json_encode($labels) ?>;
    const originalData = <?= json_encode($data) ?>;

    function generateData() {
      return originalData.map(() => Math.floor(Math.random() * 100) + 1);
    }

    const colors = labels.map((_, i) => `hsl(${i * 35}, 70%, 60%)`);

    let chartType = 'doughnut';
    const ctx = document.getElementById('lamaranChart').getContext('2d');

    const bgImage = new Image();
    bgImage.src = '../../src/assets/img/logo.png';
    const backgroundPlugin = {
      id: 'customBackground',
      beforeDraw: (chart) => {
        if (bgImage.complete) {
          const {
            ctx,
            width,
            height
          } = chart;
          const imgwidth = width * 0.3;
          const imgheight = height * 0.3;
          const x = (width - imgwidth) / 2;
          const y = (height - imgheight) / 1.9;
          ctx.save();
          ctx.drawImage(bgImage, x, y, imgwidth, imgheight);
          ctx.restore();
        }
      }
    };


    const lamaranChart = new Chart(ctx, {
      type: chartType,
      data: {
        labels: labels,
        datasets: [{
          label: 'Jumlah Lamaran',
          data: [...originalData],
          backgroundColor: colors,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: true,
            padding: {
              top: 20,
              bottom: 25
            },
            text: 'Distribusi Lamaran Siswa ke Perusahaan (Doghnut Chart)'
          },
          legend: {
            position: 'bottom'
          },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                const val = ctx.raw;
                const percent = ((val / total) * 100).toFixed(1);
                return `${ctx.label}: ${val} siswa (${percent}%)`;
              }
            }
          }
        }
      },
      plugins: [backgroundPlugin]
    });

    function toggleChart() {
      const btn = document.querySelector('.Bt[data-bs-title]');
      if (lamaranChart.options.cutout) {
        lamaranChart.options.cutout = 0;
        lamaranChart.options.plugins.title.text = 'Distribusi Lamaran Siswa (Pie Chart)';
        if (btn) btn.setAttribute('data-bs-title', 'Ubah ke Doghnut');
      } else {
        lamaranChart.options.cutout = '50%';
        lamaranChart.options.plugins.title.text = 'Distribusi Lamaran Siswa ke Perusahaan (Doghnut Chart)';
        if (btn) btn.setAttribute('data-bs-title', 'Ubah ke Pie');
      }
      lamaranChart.update();

      if (btn && bootstrap && bootstrap.Tooltip) {
        const tooltip = bootstrap.Tooltip.getInstance(btn);
        if (tooltip) tooltip.setContent({
          '.tooltip-inner': btn.getAttribute('data-bs-title')
        });
      }
    }
  </script>

  <!--end::Script-->
</body>
<!--end::Body-->

</html>