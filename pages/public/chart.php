<?php
// Syarat untuk menggunakan session
session_start();
require '../../src/functions.php';
include '../../src/controller/LoginF.php';
include '../../src/controller/lupapw.php';
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
$tahun_sekarang = date('Y');
$sql = "
    SELECT MONTHNAME(waktu_login) AS bulan, COUNT(DISTINCT id_user) AS jumlah
    FROM log_login
    WHERE YEAR(waktu_login) = '$tahun_sekarang'
    GROUP BY MONTH(waktu_login)
    ORDER BY MONTH(waktu_login)
";
$result = mysqli_query($conn, $sql);
$daftar_bulan = [
  'January',
  'February',
  'March',
  'April',
  'May',
  'June',
  'July',
  'August',
  'September',
  'October',
  'November',
  'December'
];
// Default semua bulan 0
$jumlah_final = array_fill_keys($daftar_bulan, 0);
while ($row = mysqli_fetch_assoc($result)) {
  $jumlah_final[$row['bulan']] = (int)$row['jumlah'];
}
$bulan_labels = array_keys($jumlah_final);
$jumlah_data  = array_values($jumlah_final);
mysqli_query($conn, "DELETE FROM log_login WHERE waktu_login < NOW() - INTERVAL 1 YEAR");
// Hitung yang aktif dalam 60 menit terakhir
$sql_online = mysqli_query($conn, "
    SELECT COUNT(*) AS total_online
    FROM online_users
    WHERE last_activity >= NOW() - INTERVAL 60 MINUTE
");
$row_online = mysqli_fetch_assoc($sql_online);
$total_online = $row_online['total_online'];
$total_perusahaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM perusahaan"))['total'];
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
  .chart-container {
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
  
  .lamaran-chart-container {
    width: 100%;
    max-width: 900px;
    height: 55rem;
    margin: 30px auto;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
  }
  
  .lamaran-chart-container::before {
    content: "";
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
    z-index: 1;
  }
  
  .lamaran-chart-content {
    position: relative;
    z-index: 2;
  }
  
  .lamaran-chart-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
  }
  
  .lamaran-chart-wrapper {
    height: calc(100% - 60px);
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 20px;
  }
  
  .chart-controls {
    display: flex;
    position: absolute;
    justify-content: center;
    align-items: center;
    left: 0;
    right: 0;
    bottom: -1px;
    z-index: 50;
    margin-top: 15px;
  }
  
  .login-chart-container {
    width: 100%;
    max-width: 900px;
    margin: 30px auto;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
  }
  
  .login-chart-container::before {
    content: "";
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
    z-index: 1;
  }
  
  .login-chart-content {
    position: relative;
    z-index: 2;
  }
  
  .login-chart-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
  }
  
  .login-chart-wrapper {
    height: 450px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 20px;
  }
  
  .stats-card {
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s;
  }
  
  .stats-card:hover {
    transform: translateY(-5px);
  }
  
  /* Responsive styles */
  @media (max-width: 992px) {
    .lamaran-chart-container, .login-chart-container {
      max-width: 100%;
      margin: 20px auto;
    }
    
    .chart-controls {
      left: 0;
      right: 0;
      bottom: -2px;
    }
  }
  
  @media (max-width: 768px) {
    .lamaran-chart-wrapper {
      height: 350px;
    }
    
    .login-chart-wrapper {
      height: 350px;
    }
    
    .lamaran-chart-container, .login-chart-container {
      padding: 15px;
      height: 45rem;
    }
    
    .lamaran-chart-title, .login-chart-title {
      font-size: 1.5rem;
      margin-bottom: 15px;
    }
    
    .chart-controls {
      bottom: -1px;
    }
    
    .Bt {
      padding: 8px 12px;
      font-size: 14px;
    }
    
    .stats-card {
      margin-bottom: 15px;
    }
    
    .stats-card .card-body {
      padding: 15px;
    }
    
    .stats-card .rounded {
      width: 50px;
      height: 50px;
    }
    
    .stats-card h4 {
      font-size: 1.2rem;
    }
    
    .stats-card p {
      font-size: 0.9rem;
    }
  }
  
  @media (max-width: 576px) {
    .lamaran-chart-wrapper {
      height: 300px;
    }
    
    .login-chart-wrapper {
      height: 300px;
    }
    
    .lamaran-chart-container, .login-chart-container {
      padding: 10px;
      height: 40rem;
      margin: 15px auto;
    }
    
    .lamaran-chart-title, .login-chart-title {
      font-size: 1.3rem;
      margin-bottom: 10px;
    }
    
    .chart-controls {
      bottom: 1px;
    }
    
    .Bt {
      padding: 6px 10px;
      font-size: 12px;
    }
    
    .row.my-5 {
      margin-top: 1rem !important;
      margin-bottom: 1rem !important;
    }
    
    .stats-card .card-body {
      padding: 10px;
    }
    
    .stats-card .rounded {
      width: 40px;
      height: 40px;
    }
    
    .stats-card h4 {
      font-size: 1rem;
    }
    
    .stats-card p {
      font-size: 0.8rem;
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
          <?php if (isset($_SESSION['level'])) : ?>
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
                  <a href="../../logout.php" class="btn btn-default btn-flat float-end btn-logout" data-bs-trigger="hover" data-bs-placement="left" data-bs-custom-class="custom-tooltip-logout" data-bs-title="LogOut ( Keluar )"><i class="fas fa-arrow-right-from-bracket"></i></a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          <?php else: ?>
            <ul class="navbar-nav ms-auto">
              <!--begin::User Menu Dropdown-->
              <button data-bs-toggle="modal" data-bs-target="#Modaldaftar" class="btn btn-outline-light ps-2 fw-medium d-flex align-items-center justify-content-center text-center" style="height: 30px; font-size: 13px;"><i class="fa-solid fa-pen-to-square me-2"></i>Daftar</button>
              <button data-bs-toggle="modal" data-bs-target="#Modallogin" class="btn btn-outline-light ps-2 mx-2 fw-medium d-flex align-items-center justify-content-center text-center" style="height: 30px; font-size: 13px;"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</button>
              <!--end::User Menu Dropdown-->
            </ul>
          <?php endif; ?>
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
            <!-- Card Lamaran Chart yang Diperbarui -->
            <div class="lamaran-chart-container bg-primary-subtle" data-bs-theme="dark">
              <div class="lamaran-chart-content">
                <h3 class="lamaran-chart-title">Distribusi Lamaran Siswa ke Perusahaan</h3>
                <div class="lamaran-chart-wrapper">
                  <div class="chart-container">
                    <canvas id="lamaranChart"></canvas>
                  </div>
                </div>
              </div>
              <div class="chart-controls">
                <button class="Bt pe-auto text-white bg-primary" data-bs-trigger="hover" data-bs-placement="top" data-bs-custom-class="custom-tooltip-Spin" data-bs-title="Ubah ke Pie" onclick="toggleChart()">
                  <i class="fa-solid fa-arrows-rotate"></i> Ganti Tipe Chart
                </button>
              </div>
            </div>
            
            <div class="row my-5 d-flex justify-content-evenly">
              <div class="col-12 col-md-5 col-lg-3">
                <div class="card stats-card border-0 text-center">
                  <div class="card-body">
                    <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px; background-color:#36b9cc;">
                      <i class="fas fa-building text-white fa-lg"></i>
                    </div>
                    <p class="text-muted mb-1">Perusahaan</p>
                    <h4 class="fw-bold"><?= $total_perusahaan; ?></h4>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-5 col-lg-3">
                <div class="card stats-card border-0 text-center">
                  <div class="card-body">
                    <div class="rounded d-flex align-items-center justify-content-center mx-auto mb-3 bg-success" style="width:60px; height:60px;">
                      <i class="fas fa-users-between-lines text-white fa-lg"></i>
                    </div>
                    <p class="text-muted mb-1">User Online</p>
                    <h4 class="fw-bold"><?= $total_online; ?></h4>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Chart Login Alumni yang Diperbesar -->
            <div class="login-chart-container bg-primary-subtle" data-bs-theme="dark">
              <div class="login-chart-content">
                <h3 class="login-chart-title">Statistik Login Alumni Tahun <?= $tahun_sekarang ?></h3>
                <div class="login-chart-wrapper">
                  <canvas id="loginChart"></canvas>
                </div>
              </div>
            </div>
          </div>
          <!--end::Row-->
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content-->
    </main>
    <!--end::App Main-->
    <!--begin::Footer-->
    <?php include '../../src/template/app-footer.php'; ?>
    <!--end::Footer-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->
  <?php include '../../src/template/modalForm.php'; ?>
  <?php include '../../src/template/footer.php'; ?>
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
          const y = (height - imgheight) / 2.3;
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
            display: false
          },
          legend: {
            position: 'bottom',
            labels: {
              color: 'rgba(255, 255, 255, 0.9)',
              font: { size: 14 },
              padding: 15
            }
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
        },
        layout: {
          padding: {
            left: 20,
            right: 20,
            top: 20,
            bottom: 20
          }
        }
      },
      plugins: [backgroundPlugin]
    });
    function toggleChart() {
      const btn = document.querySelector('.Bt[data-bs-title]');
      if (lamaranChart.options.cutout) {
        lamaranChart.options.cutout = 0;
        if (btn) btn.setAttribute('data-bs-title', 'Ubah ke Doughnut');
      } else {
        lamaranChart.options.cutout = '50%';
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
  <script>
    // Chart Login Alumni
    const loginData = {
      labels: <?= json_encode($bulan_labels); ?>,
      datasets: [{
        label: 'Jumlah Alumni Login',
        data: <?= json_encode($jumlah_data); ?>,
        borderColor: 'rgba(255, 255, 255, 0.8)',
        backgroundColor: 'rgba(255, 255, 255, 0.2)',
        pointBackgroundColor: 'rgba(255, 255, 255, 1)',
        pointRadius: 6,
        fill: true,
        tension: 0.4
      }]
    };
    const loginConfig = {
      type: 'line',
      data: loginData,
      maintainAspectRatio: false,
      options: {
        responsive: true,
        layout: {
          padding: {
            left: 20,
            right: 20,
            top: 20,
            bottom: 20
          }
        },
        animations: {
          y: {
            easing: 'easeInOutElastic',
            from: (ctx) => {
              if (ctx.type === 'data') {
                if (ctx.mode === 'default' && !ctx.dropped) {
                  ctx.dropped = true;
                  return 0;
                }
              }
            }
          }
        },
        scales: {
          y: {
            suggestedMin: 0,
            suggestedMax: 10,
            ticks: {
              color: 'rgba(255, 255, 255, 0.8)',
              font: {
                size: 12
              }
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          },
          x: {
            ticks: {
              color: 'rgba(255, 255, 255, 0.8)',
              font: {
                size: 12
              }
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          }
        },
        plugins: {
          legend: {
            labels: {
              color: 'rgba(255, 255, 255, 0.9)',
              font: {
                size: 14
              }
            }
          }
        }
      }
    };
    new Chart(document.getElementById('loginChart'), loginConfig);
  </script>
  <!--end::Script-->
</body>
<!--end::Body-->
</html>