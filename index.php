<!DOCTYPE html>
<html lang="en">

<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90680653-2"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-90680653-2');
  </script>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Meta -->
  <meta name="description" content="Responsive Bootstrap 4 Dashboard Template">
  <meta name="author" content="BootstrapDash">
  <title>Landing Page | Pengaduan Siswa</title>
  <!-- vendor css -->
  <link href="assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
  <link href="assets/lib/typicons.font/typicons.css" rel="stylesheet">
  <link href="assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- azia CSS -->
  <link rel="stylesheet" href="assets/css/azia.css">
  <link rel="stylesheet" href="assets/css/bootstrap-grid.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/sdb-style.css">


</head>

<body>
  <div class="az-header sticky-top">
    <div class="container">

      <!-- Kiri: logo -->
      <div class="az-header-left">
        <a href="dashboard-admin.php" class="az-logo">
          <span></span>
          <img src="assets/img/logosekolah2.png" width="42px" style="margin:0 10px 0 0;">
          SMK KUSUMA JAYA
        </a>
        <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
      </div>

      <!-- Tengah: navigasi -->
      <div class="az-header-menu" id="azHeaderMenu">
        <div class="az-header-menu-header">
          <a href="dashboard-admin.php" class="az-logo"><span></span> SMK KUSUMA JAYA</a>
          <a href="" class="close">&times;</a>
        </div>
        <ul class="nav">
          <li class="nav-item">
            <a href="" class="nav-link">
              <i class="typcn typcn-calendar-outline"></i>
              <?php echo date('d M Y'); ?>
            </a>
          </li>
        </ul>
      </div>

      <!-- Kanan: profil -->

    </div>
  </div><!-- /az-header -->
  </div><!-- container -->
  </div><!-- az-header -->
  <section class="hero-section d-flex align-items-center">
    <div class="overlay"></div>
    <div class="container position-relative">
      <div class="row">
        <div class="col-md-8">
          <h1 class="fw-bold">
            Selamat Datang Di
            <span class="text-primary">Aplikasi Pengaduan Sarana Sekolah</span>
          </h1>
          <p class="mt-3">
            SMK KUSUMA JAYA
          </p>
          <div class="mt-4">
            <a href="login-as.php"
              class="btn btn-primary me-2">
              Login
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5 bg-white">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold position-relative d-inline-block">
          <span class="px-3 bg-white">TATA CARA PELAPORAN</span>
        </h2>
        <p class="text-muted mt-2">
          Nah ini dia alur pelaporan yang ada di website <strong>SMK KUSUMA JAYA</strong>
        </p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm text-center p-4 flow-card">
            <div class="icon-circle mb-3">
              <i class="bi bi-person"></i>
            </div>
            <h5 class="fw-bold">Siswa Login</h5>
            <p class="text-muted">
              Login terlebih dahulu dengan cara klik button <strong>LOGIN</strong>.
            </p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm text-center p-4 flow-card">
            <div class="icon-circle mb-3">
              <i class="bi bi-pencil-square"></i>
            </div>
            <h5 class="fw-bold">Kirim Laporan</h5>
            <p class="text-muted">
              Tulis laporan keluhan Anda dengan jelas.
            </p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm text-center p-4 flow-card">
            <div class="icon-circle mb-3">
              <i class="bi bi-shuffle"></i>
            </div>
            <h5 class="fw-bold">Proses Verifikasi</h5>
            <p class="text-muted">
              Tunggu sampai laporan Anda diverifikasi oleh admin/petugas.
            </p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 border-0 shadow-sm text-center p-4 flow-card">
            <div class="icon-circle mb-3">
              <i class="bi bi-speedometer2"></i>
            </div>
            <h5 class="fw-bold">Tindak Lanjut</h5>
            <p class="text-muted">
              Laporan Anda diproses hingga selesai ditindaklanjuti.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="az-footer ht-40">
    <div class="container ht-100p pd-t-0-f">
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">
        Copyright ©smkkusumajaya.com 2025
      </span>
    </div>
  </div>
</body>

</html>
<!-- data ringkasan -->