<?php

/**
 * ================================================
 * Aplikasi Pengaduan Siswa - SMK KUSUMA JAYA
 * ================================================
 * File    : dashboard-admin.php
 * Modul   : Dashboard Admin
 * Fungsi  : Halaman utama admin untuk melihat
 *           semua aspirasi masuk, filter berdasarkan
 *           kelas, memberi tanggapan, mengubah status,
 *           dan menghapus aspirasi siswa.
 *           Dilengkapi pagination dan AJAX handler.
 * Revisi dari asesor:  
 * ================================================
 */
session_start();
require_once '../models/database.php';
require_once '../models/auth.php';
$auth = new Auth();
$auth->restrictToAdmin();
$nama = $_SESSION['nama'];

$db = new Database();
$conn = $db->koneksi();

// Handle AJAX Request
if (isset($_POST['action'])) {
  header('Content-Type: application/json');

  // PROSES TANGGAPAN
  if ($_POST['action'] == 'tanggapi') {
    $id_input = mysqli_real_escape_string($conn, $_POST['id']);
    $umpan_balik = mysqli_real_escape_string($conn, $_POST['tanggapan']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $cek = mysqli_query($conn, "SELECT id_aspirasi FROM aspirasi WHERE id_input_aspirasi = '$id_input'");

    if (mysqli_num_rows($cek) > 0) {
      $row = mysqli_fetch_assoc($cek);
      $query = "UPDATE aspirasi SET umpan_balik = '$umpan_balik', tanggal_pengaduan = NOW() WHERE id_aspirasi = '" . $row['id_aspirasi'] . "'";
    } else {
      $query = "INSERT INTO aspirasi (id_input_aspirasi, umpan_balik, tanggal_pengaduan) VALUES ('$id_input', '$umpan_balik', NOW())";
    }

    $query2 = "UPDATE input_aspirasi SET status_pengaduan = '$status' WHERE id_input = '$id_input'";

    if (mysqli_query($conn, $query) && mysqli_query($conn, $query2)) {
      echo json_encode(['success' => true, 'message' => 'Tanggapan berhasil dikirim']);
    } else {
      echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
  }

  // UPDATE STATUS
  if ($_POST['action'] == 'update_status') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE input_aspirasi SET status_pengaduan = '$status' WHERE id_input = '$id'";

    if (mysqli_query($conn, $query)) {
      echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
    } else {
      echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
  }

  // HAPUS ASPIRASI
  if ($_POST['action'] == 'hapus') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    mysqli_query($conn, "DELETE FROM aspirasi WHERE id_input_aspirasi = '$id'");

    if (mysqli_query($conn, "DELETE FROM input_aspirasi WHERE id_input = '$id'")) {
      echo json_encode(['success' => true, 'message' => 'Aspirasi berhasil dihapus']);
    } else {
      echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
  }

  // GET DETAIL
  if ($_POST['action'] == 'get_detail') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $query = "SELECT 
                ia.id_input, ia.lokasi, ia.keterangan, ia.tanggal_pengaduan, ia.status_pengaduan,
                s.nama, s.nis, s.kelas,
                k.nama_kategori,
                a.umpan_balik, a.tanggal_pengaduan as tanggal_umpan_balik
              FROM input_aspirasi ia
              JOIN siswa s ON ia.id_siswa = s.id_siswa
              JOIN kategori k ON ia.id_kategori = k.id_kategori
              LEFT JOIN aspirasi a ON ia.id_input = a.id_input_aspirasi
              WHERE ia.id_input = '$id'";

    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
  }
}

// ===== PAGINATION & FILTER (GET request / load halaman) =====
$per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Filter kelas
$filter_kelas = isset($_GET['kelas']) && $_GET['kelas'] != '' ? mysqli_real_escape_string($conn, $_GET['kelas']) : '';
$where_clause = $filter_kelas ? "WHERE s.kelas LIKE '$filter_kelas%'" : "";

// Hitung total untuk pagination
$total_query = "SELECT COUNT(*) AS total 
                FROM input_aspirasi i 
                JOIN siswa s ON i.id_siswa = s.id_siswa
                $where_clause";
$total_data = mysqli_fetch_assoc(mysqli_query($conn, $total_query))['total'];
$total_pages = ceil($total_data / $per_page);

// Query utama dengan LIMIT
$query = "SELECT 
            i.id_input, i.lokasi, i.keterangan, i.status_pengaduan, i.tanggal_pengaduan,
            s.nama, s.kelas,
            k.nama_kategori
          FROM input_aspirasi i
          JOIN siswa s ON i.id_siswa = s.id_siswa
          JOIN kategori k ON i.id_kategori = k.id_kategori
          $where_clause
          ORDER BY i.tanggal_pengaduan DESC
          LIMIT $per_page OFFSET $offset";

$result = mysqli_query($conn, $query);

// Data ringkasan (tetap tanpa filter)
$total_input_aspirasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM input_aspirasi"))['total'];
$total_kategori       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM kategori"))['total'];
$total_siswa          = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM siswa"))['total'];

$count_kelas_10 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM input_aspirasi i JOIN siswa s ON i.id_siswa = s.id_siswa WHERE s.kelas LIKE '10%'"))['total'];
$count_kelas_11 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM input_aspirasi i JOIN siswa s ON i.id_siswa = s.id_siswa WHERE s.kelas LIKE '11%'"))['total'];
$count_kelas_12 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM input_aspirasi i JOIN siswa s ON i.id_siswa = s.id_siswa WHERE s.kelas LIKE '12%'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

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

  <title>Admin Dashboard</title>
  <!-- vendor css -->
  <link href="../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
  <link href="../assets/lib/typicons.font/typicons.css" rel="stylesheet">
  <link href="../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
  <!-- azia CSS -->
  <!-- <link rel="stylesheet" href="../assets/css/azia.css"> -->
  <link rel="stylesheet" href="../assets/css/sdb-style.css">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">


</head>

<body>
  <div class="az-header sticky-top">
    <div class="container">

      <div class="az-header-left">
        <a href="dashboard-admin.php" class="az-logo">
          <span></span>
          <img src="../assets/img/logosekolah2.png" width="42px" style="margin:0 10px 0 0;">
          SMK KUSUMA JAYA
        </a>
        <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
      </div>

      <div class="az-header-menu" id="azHeaderMenu">
        <div class="az-header-menu-header">
          <a href="dashboard-admin.php" class="az-logo"><span></span> SMK KUSUMA JAYA</a>
          <a href="" class="close">&times;</a>
        </div>
        <ul class="nav">
          <li class="nav-item active">
            <a href="dashboard-admin.php" class="nav-link">
              Dashboard
            </a>
          </li>
          <li class="nav-item show">
            <a href="" class="nav-link with-sub">
              Manage
            </a>
            <nav class="az-menu-sub">
              <a href="managesiswa.php" class="nav-link">Siswa Management</a>
              <a href="managekategori.php" class="nav-link">Kategori Management</a>
            </nav>
          </li>
          <li class="nav-item">
            <a href="" class="nav-link">
              </i>
              <?php echo date('d M Y'); ?>
            </a>
          </li>
        </ul>
      </div>

      <div class="az-header-right">
        <a href="../app/logout.php" class="sdb-btn sdb-btn--secondary" style="font-size:0.78rem;padding:0.35rem 0.8rem;">
          Logout
        </a>
      </div>

    </div>
  </div>

  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Dashboard Title - TETAP SAMA -->
      <div class="text-center mb-4">
        <h2 class="fw-bold" style="color: #2d4a7c;">👩🏽‍💻 Admin Dashboard</h2>
      </div>

      <!-- Statistics Cards - MENGGUNAKAN DESIGN ORIGINAL KAMU -->
      <div class="row g-3 justify-content-center mb-4">
        <!-- Total Data Masuk -->
        <div class="col-md-6 col-lg-4">
          <div class="dashboard-card card-input">
            <div class="card-icon-wrapper">
              <div class="card-icon">
                <i class="fas fa-clipboard-list"></i>
              </div>
            </div>
            <div class="card-content">
              <h6 class="card-label">TOTAL DATA MASUK</h6>
              <h2 class="card-value"><?php echo $total_input_aspirasi; ?></h2>
              <div class="card-trend">
                <i class="fas fa-arrow-up"></i>
                <span>Aktif</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Kategori -->
        <div class="col-md-6 col-lg-4">
          <div class="dashboard-card card-kategori">
            <div class="card-icon-wrapper">
              <div class="card-icon">
                <i class="fas fa-tags"></i>
              </div>
            </div>
            <div class="card-content">
              <h6 class="card-label">TOTAL KATEGORI</h6>
              <h2 class="card-value"><?php echo $total_kategori; ?></h2>
              <div class="card-trend">
                <i class="fas fa-check-circle"></i>
                <span>Tersedia</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Siswa -->
        <div class="col-md-6 col-lg-4">
          <div class="dashboard-card card-input">
            <div class="card-icon-wrapper">
              <div class="card-icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
            <div class="card-content">
              <h6 class="card-label">TOTAL SISWA</h6>
              <h2 class="card-value"><?php echo $total_siswa; ?></h2>
              <div class="card-trend">
                <i class="fas fa-arrow-up"></i>
                <span>Aktif</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Buttons -->
      <div class="filter-container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
          <div class="mb-2 mb-md-0">
            <h6 class="mb-0 fw-bold" style="color: #2d4a7c;">
              <i class="bi bi-funnel-fill me-2"></i>Filter Berdasarkan Kelas
            </h6>
          </div>
          <div>
            <button class="filter-btn <?= $filter_kelas == '' ? 'active' : '' ?>" onclick="window.location='?kelas='">
              <i class="bi bi-grid-3x3-gap-fill me-1"></i>Semua
              <span class="filter-badge"><?= $total_input_aspirasi ?></span>
            </button>
            <button class="filter-btn <?= $filter_kelas == '10' ? 'active' : '' ?>" onclick="window.location='?kelas=10'">
              <i class="bi bi-mortarboard-fill me-1"></i>Kelas 10
              <span class="filter-badge"><?= $count_kelas_10 ?></span>
            </button>
            <button class="filter-btn <?= $filter_kelas == '11' ? 'active' : '' ?>" onclick="window.location='?kelas=11'">
              <i class="bi bi-mortarboard-fill me-1"></i>Kelas 11
              <span class="filter-badge"><?= $count_kelas_11 ?></span>
            </button>
            <button class="filter-btn <?= $filter_kelas == '12' ? 'active' : '' ?>" onclick="window.location='?kelas=12'">
              <i class="bi bi-mortarboard-fill me-1"></i>Kelas 12
              <span class="filter-badge"><?= $count_kelas_12 ?></span>
            </button>
          </div>
        </div>
      </div>

      <!-- Tabel Aspirasi dengan Modal -->
      <div class="table-container">
        <div class="card-header-gradient">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0 fw-bold">
              <i class="bi bi-clipboard-data me-2"></i> Histori Aspirasi Siswa
            </h5>
            <span class="badge bg-white text-primary px-3 py-2 mt-2 mt-md-0" style="font-size: 0.9rem;">
              Total: <span id="total-displayed"><?= mysqli_num_rows($result); ?></span> Aspirasi
            </span>
          </div>
        </div>
        <div class="p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="aspirasiTable" style="overflow: hidden;">
              <thead>
                <tr>
                  <th class="text-center" style="width: 60px;">NO</th>
                  <th style="min-width: 180px;">NAMA SISWA</th>
                  <th class="text-center" style="width: 90px;">KELAS</th>
                  <th style="min-width: 150px;">LOKASI</th>
                  <th class="text-center" style="min-width: 130px;">KATEGORI</th>
                  <th class="text-center" style="width: 130px;">STATUS</th>
                  <th class="text-center" style="width: 120px;">TANGGAL</th>
                  <th class="text-center" style="min-width: 160px;">AKSI</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                mysqli_data_seek($result, 0);
                while ($row = mysqli_fetch_assoc($result)):
                  // Ambil angka kelas (10, 11, atau 12)
                  preg_match('/^(\d+)/', $row['kelas'], $matches);
                  $kelas_number = isset($matches[1]) ? $matches[1] : '';
                ?>
                  <tr class="aspirasi-row" data-id="<?= $row['id_input']; ?>" data-kelas="<?= $kelas_number; ?>">
                    <td class="text-center">
                      <span class="badge bg-light text-dark fw-bold" style="font-size: 0.85rem; padding: 6px 10px;"><?= $no++; ?></span>
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2">
                          <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                        </div>
                        <div>
                          <div class="fw-bold" style="color: #2d4a7c; font-size: 0.9rem;"><?= htmlspecialchars($row['nama']); ?></div>
                          <small class="text-muted" style="font-size: 0.75rem;">Siswa Aktif</small>
                        </div>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="badge" style="background: linear-gradient(135deg, #e8ecff 0%, #d1d9f0 100%); color: #2d4a7c; font-size: 0.8rem; padding: 6px 12px;">
                        <?= htmlspecialchars($row['kelas']); ?>
                      </span>
                    </td>
                    <td>
                      <i class="bi bi-geo-alt-fill me-1" style="color: #ef4444; font-size: 0.85rem;"></i>
                      <span style="color: #374151; font-size: 0.85rem;"><?= htmlspecialchars($row['lokasi']); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2d4a7c; font-size: 0.75rem; padding: 6px 12px;">
                        <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($row['nama_kategori']); ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <?php
                      $statusClass = '';
                      $statusIcon = '';
                      $statusText = '';

                      if ($row['status_pengaduan'] == 'menunggu') {
                        $statusClass = 'background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);';
                        $statusIcon = 'clock-history';
                        $statusText = 'Menunggu';
                      } elseif ($row['status_pengaduan'] == 'memproses') {
                        $statusClass = 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);';
                        $statusIcon = 'arrow-repeat';
                        $statusText = 'Diproses';
                      } else {
                        $statusClass = 'background: linear-gradient(135deg, #10b981 0%, #059669 100%);';
                        $statusIcon = 'check-circle-fill';
                        $statusText = 'Selesai';
                      }
                      ?>
                      <span class="badge" style="<?= $statusClass ?> color: white; padding: 6px 12px; font-size: 0.75rem;">
                        <i class="bi bi-<?= $statusIcon; ?> me-1"></i><?= $statusText; ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <div style="color: #6b7280;">
                        <i class="bi bi-calendar3 me-1" style="font-size: 0.8rem;"></i>
                        <div class="fw-semibold" style="font-size: 0.8rem;"><?= date('d M Y', strtotime($row['tanggal_pengaduan'])); ?></div>
                      </div>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <button type="button" class="btn-action btn-primary"
                          onclick="lihatDetail(<?= $row['id_input']; ?>)"
                          data-bs-toggle="tooltip" title="Lihat Detail">
                          <i class="bi bi-eye"></i>
                        </button>

                        <?php if ($row['status_pengaduan'] != 'selesai'): ?>
                          <button type="button" class="btn-action btn-success"
                            onclick="tanggapiAspirasi(<?= $row['id_input']; ?>)"
                            data-bs-toggle="tooltip" title="Tanggapi">
                            <i class="bi bi-reply-fill"></i>
                          </button>

                          <button type="button" class="btn-action btn-info"
                            onclick="updateStatus(<?= $row['id_input']; ?>, '<?= $row['status_pengaduan']; ?>')"
                            data-bs-toggle="tooltip" title="Update Status">
                            <i class="bi bi-arrow-clockwise"></i>
                          </button>
                        <?php endif; ?>

                        <button type="button" class="btn-action btn-danger"
                          onclick="hapusAspirasi(<?= $row['id_input']; ?>)"
                          data-bs-toggle="tooltip" title="Hapus">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
              <div class="d-flex justify-content-between align-items-center px-4 py-3 flex-wrap gap-2"
                style="border-top: 1px solid #e8ecff;">

                <div class="text-muted" style="font-size: 12px;">
                  Menampilkan <?= ($offset + 1) ?>–<?= min($offset + $per_page, $total_data) ?>
                  dari <?= $total_data ?> aspirasi
                  <?= $filter_kelas ? "· Kelas <strong>$filter_kelas</strong>" : "" ?>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <?php
                  $url_base = '?kelas=' . $filter_kelas;
                  $start_loop = max(1, $page - 2);
                  $end_loop   = min($total_pages, $page + 2);
                  ?>

                  <!-- Prev -->
                  <?php if ($page > 1): ?>
                    <a href="<?= $url_base ?>&page=<?= $page - 1 ?>" class="btn btn-sm btn-outline-primary px-3">
                      <i class="bi bi-chevron-left"></i> Prev
                    </a>
                  <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary px-3" disabled>
                      <i class="bi bi-chevron-left"></i> Prev
                    </button>
                  <?php endif; ?>

                  <!-- Halaman pertama + ellipsis -->
                  <?php if ($start_loop > 1): ?>
                    <a href="<?= $url_base ?>&page=1" class="btn btn-sm btn-outline-primary px-3">1</a>
                    <?php if ($start_loop > 2): ?><span class="text-muted">...</span><?php endif; ?>
                  <?php endif; ?>

                  <!-- Nomor halaman -->
                  <?php for ($i = $start_loop; $i <= $end_loop; $i++): ?>
                    <a href="<?= $url_base ?>&page=<?= $i ?>"
                      class="btn btn-sm <?= $i == $page ? 'btn-primary' : 'btn-outline-primary' ?> px-3">
                      <?= $i ?>
                    </a>
                  <?php endfor; ?>

                  <!-- Halaman terakhir + ellipsis -->
                  <?php if ($end_loop < $total_pages): ?>
                    <?php if ($end_loop < $total_pages - 1): ?><span class="text-muted">...</span><?php endif; ?>
                    <a href="<?= $url_base ?>&page=<?= $total_pages ?>" class="btn btn-sm btn-outline-primary px-3">
                      <?= $total_pages ?>
                    </a>
                  <?php endif; ?>

                  <!-- Next -->
                  <?php if ($page < $total_pages): ?>
                    <a href="<?= $url_base ?>&page=<?= $page + 1 ?>" class="btn btn-sm btn-outline-primary px-3">
                      Next <i class="bi bi-chevron-right"></i>
                    </a>
                  <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary px-3" disabled>
                      Next <i class="bi bi-chevron-right"></i>
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Detail Aspirasi -->
  <div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary-gradient text-white">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-info-circle-fill me-2"></i>Detail Aspirasi Siswa
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="detailContent">
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat data...</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tanggapi Aspirasi -->
  <div class="modal fade" id="modalTanggapi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success-gradient text-white">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-reply-fill me-2"></i>Tanggapi Aspirasi Siswa
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="formTanggapi">
          <div class="modal-body">
            <input type="hidden" id="tanggapi_id" name="id">
            <div class="mb-4">
              <label class="form-label fw-bold" style="color: #2d4a7c; font-size: 1.05rem;">
                <i class="bi bi-chat-text me-2"></i>Tanggapan
              </label>
              <textarea class="form-control" id="tanggapan" name="tanggapan" rows="6"
                style="border-radius: 12px; padding: 15px; font-size: 1rem;"
                placeholder="Masukkan tanggapan untuk aspirasi ini..." required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold" style="color: #2d4a7c; font-size: 1.05rem;">
                <i class="bi bi-arrow-repeat me-2"></i>Update Status
              </label>
              <select class="form-select" id="status_tanggapan" name="status"
                style="border-radius: 12px; padding: 12px; font-size: 1rem;" required>
                <option value="memproses">Diproses</option>
                <option value="selesai">Selesai</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">
              <i class="bi bi-x-circle me-1"></i>Batal
            </button>
            <button type="submit" class="btn btn-success px-4 py-2" style="border-radius: 10px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
              <i class="bi bi-send-fill me-1"></i>Kirim Tanggapan
            </button>
          </div>
        </form>


      </div>
    </div>
  </div>

  <!-- Modal Update Status -->
  <div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-info-gradient text-white">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-arrow-clockwise me-2"></i>Update Status Aspirasi
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="formStatus">
          <div class="modal-body">
            <input type="hidden" id="status_id" name="id">
            <div class="mb-3">
              <label class="form-label fw-bold" style="color: #2d4a7c; font-size: 1.05rem;">
                <i class="bi bi-flag-fill me-2"></i>Status Baru
              </label>
              <select class="form-select" id="status_baru" name="status"
                style="border-radius: 12px; padding: 12px; font-size: 1rem;" required>
                <option value="menunggu">Menunggu</option>
                <option value="memproses">Diproses</option>
                <option value="selesai">Selesai</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">
              <i class="bi bi-x-circle me-1"></i>Batal
            </button>
            <button type="submit" class="btn btn-info px-4 py-2" style="border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
              <i class="bi bi-check-lg me-1"></i>Update Status
            </button>
          </div>
        </form>


      </div>
    </div>
  </div>



  <script src="../assets/lib/jquery/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/lib/ionicons/ionicons.js"></script>
  <script src="../assets/lib/jquery.flot/jquery.flot.js"></script>
  <script src="../assets/lib/jquery.flot/jquery.flot.resize.js"></script>
  <script src="../assets/lib/chart.js/Chart.bundle.min.js"></script>
  <script src="../assets/lib/peity/jquery.peity.min.js"></script>
  <script src="../assets/js/azia.js"></script>
  <script src="../assets/js/chart.flot.sampledata.js"></script>
  <script src="../assets/js/dashboard.sampledata.js"></script>
  <script src="../assets/js/jquery.cookie.js" type="text/javascript"></script>

  <script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });

    // Filter by Class Function
    function filterByClass(kelas) {
      const rows = document.querySelectorAll('.aspirasi-row');
      const buttons = document.querySelectorAll('.filter-btn');
      let count = 0;

      // Update active button
      buttons.forEach(btn => btn.classList.remove('active'));
      document.getElementById('btn-' + kelas).classList.add('active');

      // Filter rows
      rows.forEach((row, index) => {
        const rowKelas = row.getAttribute('data-kelas');

        if (kelas === 'all') {
          row.style.display = '';
          count++;
          // Update nomor urut
          row.querySelector('td:first-child .badge').textContent = count;
        } else {
          if (rowKelas === kelas) {
            row.style.display = '';
            count++;
            // Update nomor urut
            row.querySelector('td:first-child .badge').textContent = count;
          } else {
            row.style.display = 'none';
          }
        }
      });

      // Update total displayed
      document.getElementById('total-displayed').textContent = count;
    }

    // Lihat Detail
    function lihatDetail(id) {
      const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
      modal.show();

      const formData = new FormData();
      formData.append('action', 'get_detail');
      formData.append('id', id);

      fetch('dashboard-admin.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            const data = result.data;
            let html = `
        <div class="row g-4">
          <div class="col-md-6">
            <div class="detail-label"><i class="bi bi-person me-2"></i>Nama Lengkap</div>
            <div class="detail-value">${data.nama}</div>
            
            <div class="detail-label"><i class="bi bi-card-text me-2"></i>NIS</div>
            <div class="detail-value">${data.nis}</div>
            
            <div class="detail-label"><i class="bi bi-book me-2"></i>Kelas</div>
            <div class="detail-value"><span class="badge bg-light text-dark border">${data.kelas}</span></div>
          </div>
          <div class="col-md-6">
            <div class="detail-label"><i class="bi bi-geo-alt me-2"></i>Lokasi Kejadian</div>
            <div class="detail-value">${data.lokasi}</div>
            
            <div class="detail-label"><i class="bi bi-tag me-2"></i>Kategori</div>
            <div class="detail-value"><span class="badge" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2d4a7c;">${data.nama_kategori}</span></div>
            
            <div class="detail-label"><i class="bi bi-calendar me-2"></i>Tanggal Pengaduan</div>
            <div class="detail-value">${formatDate(data.tanggal_pengaduan)}</div>
          </div>
        </div>
        <div class="col-12 mt-2">
          <div class="detail-label"><i class="bi bi-info-circle me-2"></i>Status Pengaduan</div>
          <div class="detail-value">
            <span class="badge ${data.status_pengaduan == 'selesai' ? 'bg-success' : (data.status_pengaduan == 'memproses' ? 'bg-info' : 'bg-warning')} px-4 py-2" style="font-size: 1rem;">
              ${data.status_pengaduan.charAt(0).toUpperCase() + data.status_pengaduan.slice(1)}
            </span>
          </div>
        </div>
        <hr style="margin: 30px 0; border-top: 2px solid #e5e7eb;">
        <div class="mb-4">
          <div class="detail-label"><i class="bi bi-chat-left-text me-2"></i>Isi Aspirasi / Keluhan</div>
          <div class="p-4 rounded" style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-left: 4px solid #2d4a7c;">
            ${data.keterangan.replace(/\n/g, '<br>')}
          </div>
        </div>
      `;

            if (data.umpan_balik) {
              html += `
          <hr style="margin: 30px 0; border-top: 2px solid #e5e7eb;">
          <div class="alert alert-success" style="border-radius: 15px; border: 2px solid #10b981; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
            <div class="detail-label" style="color: #047857;"><i class="bi bi-reply-fill me-2"></i>Umpan Balik Admin</div>
            <p class="mt-3 mb-2" style="color: #065f46; font-size: 1.05rem; line-height: 1.7;">${data.umpan_balik.replace(/\n/g, '<br>')}</p>
            <small style="color: #047857;">
              <i class="bi bi-clock me-1"></i>Ditanggapi pada: <strong>${formatDateTime(data.tanggal_umpan_balik)}</strong>
            </small>
          </div>
        `;
            } else {
              html += `
          <div class="alert alert-warning" style="border-radius: 15px; border: 2px solid #f59e0b; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
            <i class="bi bi-exclamation-triangle me-2"></i><strong>Belum ada umpan balik</strong> untuk aspirasi ini.
          </div>
        `;
            }

            document.getElementById('detailContent').innerHTML = html;
          }
        })
        .catch(error => {
          document.getElementById('detailContent').innerHTML =
            '<div class="alert alert-danger" style="border-radius: 15px;">Gagal memuat data. Silakan coba lagi.</div>';
        });
    }

    // Tanggapi Aspirasi
    function tanggapiAspirasi(id) {
      document.getElementById('tanggapi_id').value = id;
      document.getElementById('tanggapan').value = '';
      document.getElementById('status_tanggapan').value = 'memproses';

      const modal = new bootstrap.Modal(document.getElementById('modalTanggapi'));
      modal.show();
    }

    // Submit Form Tanggapan
    document.getElementById('formTanggapi').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.append('action', 'tanggapi');

      fetch('dashboard-admin.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('✅ Tanggapan berhasil dikirim!');
            location.reload();
          } else {
            alert('❌ Gagal mengirim tanggapan: ' + data.message);
          }
        })
        .catch(error => {
          alert('❌ Terjadi kesalahan: ' + error);
        });
    });

    // Update Status
    function updateStatus(id, currentStatus) {
      document.getElementById('status_id').value = id;
      document.getElementById('status_baru').value = currentStatus;

      const modal = new bootstrap.Modal(document.getElementById('modalStatus'));
      modal.show();
    }

    // Submit Form Status
    document.getElementById('formStatus').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.append('action', 'update_status');

      fetch('dashboard-admin.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('✅ Status berhasil diupdate!');
            location.reload();
          } else {
            alert('❌ Gagal update status: ' + data.message);
          }
        })
        .catch(error => {
          alert('❌ Terjadi kesalahan: ' + error);
        });
    });

    // Hapus Aspirasi
    function hapusAspirasi(id) {
      if (confirm('⚠️ Apakah Anda yakin ingin menghapus aspirasi ini?\n\nData yang dihapus tidak dapat dikembalikan!')) {
        const formData = new FormData();
        formData.append('action', 'hapus');
        formData.append('id', id);

        fetch('dashboard-admin.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('✅ Aspirasi berhasil dihapus!');
              location.reload();
            } else {
              alert('❌ Gagal menghapus: ' + data.message);
            }
          })
          .catch(error => {
            alert('❌ Terjadi kesalahan: ' + error);
          });
      }
    }

    // Helper Functions
    function formatDate(dateString) {
      const date = new Date(dateString);
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
    }

    function formatDateTime(dateString) {
      const date = new Date(dateString);
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()} - ${hours}:${minutes} WIB`;
    }
  </script>
</body>

</html>