<?php
/**
 * ================================================
 * Aplikasi Pengaduan Siswa - SMK KUSUMA JAYA
 * ================================================
 * File    : dashboard.php
 * Modul   : Dashboard Siswa
 * Fungsi  : Halaman utama siswa untuk mengirim
 *           aspirasi baru dan melihat riwayat
 *           aspirasi beserta tanggapan admin.
 *           Dilengkapi pagination 5 data per halaman.
 * ================================================
 */
session_start();

// proteksi login
require_once '../models/auth.php';
$auth = new Auth();
$auth->restrictToSiswa();
$nama = $_SESSION['nama'];
?>
<!-- buat histori pengaduan -->
<?php
require_once "../models/database.php";
require_once "../models/function.php";

$db = new Database();
$conn = $db->koneksi();

$queryKategori = "SELECT id_kategori, nama_kategori FROM kategori 
ORDER BY nama_kategori ASC";
$resultKategori = $conn->query($queryKategori);

$current_user_id = isset($_SESSION['id_siswa']) ? $_SESSION['id_siswa']
    : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'GUEST');

// Pagination setup
$per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Hitung total data
$total_query = "SELECT COUNT(*) AS total FROM input_aspirasi WHERE id_siswa = '$current_user_id'";
$total_result = mysqli_fetch_assoc(mysqli_query($conn, $total_query));
$total_data = $total_result['total'];
$total_pages = ceil($total_data / $per_page);

// Query dengan LIMIT
$query = "SELECT 
            ia.id_input, 
            ia.lokasi, 
            ia.keterangan, 
            ia.tanggal_pengaduan,
            ia.status_pengaduan,
            k.nama_kategori
          FROM input_aspirasi ia
          LEFT JOIN kategori k ON ia.id_kategori = k.id_kategori
          WHERE ia.id_siswa = '$current_user_id'
          ORDER BY ia.tanggal_pengaduan DESC
          LIMIT $per_page OFFSET $offset";

$result = mysqli_query($conn, $query);
?>


<!-- buat alert -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <title>Siswa Dashboard</title>
    <!-- vendor css -->
    <link href="../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- azia CSS -->
    <link rel="stylesheet" href="../assets/css/azia.css">
</head>

<body class="bg-light">
    <div class="az-header sticky-top">
        <div class="container">
            <div class="az-header-left">
                <a href="dashboard.php" class="az-logo"><span></span>
                    <img src="../assets/img/logosekolah2.png" width="52px" style="margin: 0 10px 0 0;">
                    SMK KUSUMA JAYA
                </a>
                <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
            </div>
            <div class="az-header-menu">
                <div class="az-header-menu-header">
                    <a href="dashboard.php" class="az-logo"><span></span> SMK KUSUMA JAYA</a>
                    <a href="" class="close">&times;</a>
                </div>
                <ul class="nav">
                    <li class="nav-item ">
                        <a href="dashboard.php" class="nav-link"><i class="typcn typcn-chart-area-outline">
                            </i> Dashboard</a>
                    </li>
                    <li class="nav-item ">
                        <a href="#histori" class="nav-link"><i class="bi bi-clock-history">
                            </i> Aspirasi Saya</a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="typcn typcn-calendar-outline"></i>
                            <?php echo date('d M Y'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="az-header-right">
                <div class="dropdown az-profile-menu">
                    <a href="" class="az-img-user"><img src="../assets/img/iconsiswa.png" alt=""></a>
                    <div class="dropdown-menu">
                        <div class="az-dropdown-header d-sm-none">
                            <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
                        </div>
                        <div class="az-header-profile">
                            <div class="az-img-user">
                                <img src="../assets/img/iconsiswa.png" alt="">
                            </div>
                            <h6><?= htmlspecialchars($nama) ?></h6>
                        </div>
                        <a href="../app/logout.php" onclick="return confirm('Yakin ingin keluar akun?')"
                            class="dropdown-item"><i class="typcn typcn-power-outline"></i>Keluar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="col">
            <div class="col">
                <!-- ===== PROFIL SISWA ===== -->
                <div class="card profile-card mb-4">
                    <div class="card-header-profile">
                        <div class="profile-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="profile-title">Profil Siswa</h5>
                    </div>
                    <div class="card-body-profile">
                        <div class="profile-info-item">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Nama Lengkap</span>
                                <span class="info-value"><?= $_SESSION['nama']; ?></span>
                            </div>
                        </div>

                        <div class="profile-info-item">
                            <div class="info-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">NIS</span>
                                <span class="info-value"><?= $_SESSION['nis']; ?></span>
                            </div>
                        </div>

                        <div class="profile-status">
                            <span class="status-badge">
                                <i class="fas fa-check-circle"></i> Login Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="aspirasi-container">

                    <!-- ===== FORM ASPIRASI ===== -->
                    <div class="card aspirasi-form-card">
                        <div class="aspirasi-form-header">
                            <h5> 📝 Aspirasi Siswa</h5>
                        </div>
                        <div class="aspirasi-form-content">
                            <div class="aspirasi-info-badge">
                                <i class="fas fa-info-circle"></i>
                                <strong>Petunjuk:</strong> Sampaikan aspirasi Anda dengan jelas
                                dan detail agar dapat ditindaklanjuti dengan baik.
                            </div>
                            <!-- siswa input aspirasi disini -->
                            <form action="../app/proses_aspirasi.php" method="POST">
                                <div class="aspirasi-form-group">
                                    <label class="aspirasi-form-label">
                                        <i class="fas fa-heading"></i>
                                        Lokasi
                                    </label>
                                    <div class="aspirasi-input-wrapper">
                                        <i class="aspirasi-input-icon fas fa-lightbulb"></i>
                                        <input
                                            type="text"
                                            name="lokasi"
                                            class="aspirasi-form-control"
                                            placeholder="Contoh: Lab Akuntansi Lt.2"
                                            required>
                                    </div>
                                </div>

                                <div class="aspirasi-form-group">
                                    <label class="aspirasi-form-label">
                                        <i class="fas fa-list"></i>
                                        Kategori
                                    </label>
                                    <div class="aspirasi-input-wrapper">
                                        <i class="aspirasi-input-icon fas fa-folder"></i>
                                        <select name="id_kategori" class="aspirasi-form-select" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php
                                            mysqli_data_seek($resultKategori, 0);
                                            while ($kat = $resultKategori->fetch_assoc()):
                                            ?>
                                                <option value="<?= $kat['id_kategori']; ?>">
                                                    <?= $kat['nama_kategori']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="aspirasi-form-group">
                                    <label class="aspirasi-form-label">
                                        <i class="fas fa-comment-dots"></i>
                                        Isi Aspirasi
                                    </label>
                                    <div class="aspirasi-input-wrapper">
                                        <i class="aspirasi-input-icon fas fa-edit" style="top: 20px;"></i>
                                        <textarea
                                            name="keterangan"
                                            rows="5"
                                            class="aspirasi-form-control"
                                            placeholder="Jelaskan kondisi secara detail..."
                                            required>
                    </textarea>
                                    </div>
                                </div>

                                <div class="aspirasi-button-group">
                                    <button type="submit" class="aspirasi-btn aspirasi-btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                        Kirim Aspirasi
                                    </button>
                                </div>
                            </form>

                        </div><!-- /.aspirasi-form-content -->
                    </div><!-- /.card aspirasi-form-card -->
                    <!-- ^^^ FORM CARD DITUTUP DI SINI ^^^ -->

                    <!-- ===== HISTORI ASPIRASI (SECTION TERPISAH) ===== -->
                    <div id="histori" class="mt-5">

                        <div class="histori-wrapper">

                            <div class="histori-header">
                                <h5 class="mb-0 fw-semibold text-center" style="color:#1e3a8a;">
                                    <i class="bi bi-clock-history"></i> Riwayat Aspirasi saya
                                </h5>
                            </div>

                            <div class="histori-body">
                                <div class="row">
                                    <?php
                                    $db2 = new Database();
                                    $connection = $db2->koneksi();

                                    $total_menunggu = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS total FROM input_aspirasi WHERE status_pengaduan = 'menunggu' && id_siswa = '$current_user_id'"))['total'];
                                    $total_memproses = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS total FROM input_aspirasi WHERE status_pengaduan = 'memproses' && id_siswa = '$current_user_id'"))['total'];
                                    $total_selesai = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS total FROM input_aspirasi WHERE status_pengaduan = 'selesai' && id_siswa = '$current_user_id'"))['total'];
                                    ?>
                                    <div class="d-flex justify-content-center gap-5 mb-5">
                                        <div class="w-100">
                                            <div class="dashboard-card card-input">
                                                <div class="card-icon-wrapper">
                                                    <div class="card-icon">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                </div>
                                                <div class="card-content ">
                                                    <h6 class="card-label">Menunggu</h6>
                                                    <h2 class="card-value"><?php echo $total_menunggu; ?></h2>
                                                    <div class="card-trend">
                                                        <i class="fas fa-arrow-up"></i>
                                                        <span>Aktif</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-100">
                                            <div class="dashboard-card card-pending">
                                                <div class="card-icon-wrapper">
                                                    <div class="card-icon">
                                                        <i class="fas fa-clock"></i>
                                                    </div>
                                                </div>
                                                <div class="card-content">
                                                    <h6 class="card-label">Diproses</h6>
                                                    <h2 class="card-value"><?php echo $total_memproses; ?></h2>
                                                    <div class="card-trend">
                                                        <i class="fas fa-hourglass-half"></i>
                                                        <span>Pending</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class=" w-100">
                                            <div class="dashboard-card card-selesai">
                                                <div class="card-icon-wrapper">
                                                    <div class="card-icon">
                                                        <i class="fas fa-check-double"></i>
                                                    </div>
                                                </div>
                                                <div class="card-content">
                                                    <h6 class="card-label">Aspirasi Selesai</h6>
                                                    <h2 class="card-value"><?php echo $total_selesai; ?></h2>
                                                    <div class="card-trend">
                                                        <i class="fas fa-thumbs-up"></i>
                                                        <span>Complete</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <?php
                                        mysqli_data_seek($result, 0);
                                        if ($result && mysqli_num_rows($result) > 0) {
                                            $count = 0;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $count++;
                                                $id_aspirasi_induk = $row['id_input'];

                                                $query_feedback = "SELECT umpan_balik, tanggal_pengaduan 
                    FROM aspirasi 
                    WHERE id_input_aspirasi = '$id_aspirasi_induk' 
                    ORDER BY tanggal_pengaduan ASC";
                                                $result_feedback = mysqli_query($conn, $query_feedback);
                                                $feedbacks = mysqli_fetch_all($result_feedback, MYSQLI_ASSOC);
                                                $has_feedback = count($feedbacks) > 0;
                                        ?>

                                                <div class="col-12">
                                                    <div class="aspiration-card p-4">
                                                        <div class="d-flex align-items-start justify-content-between">
                                                            <div class="flex-grow-1">
                                                                <div class="mb-3">
                                                                    <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
                                                                        <h3 class="card-title text-dark mb-0">
                                                                            <?php echo htmlspecialchars(substr($row['keterangan'], 0, 50)) .
                                                                                (strlen($row['keterangan']) > 50 ? '...' : ''); ?>
                                                                        </h3>
                                                                        <?php
                                                                        $status = strtolower($row['status_pengaduan']);
                                                                        $badge_class = 'secondary';
                                                                        if ($status == 'menunggu') $badge_class = 'warning';
                                                                        elseif ($status == 'memproses') $badge_class = 'info';
                                                                        elseif ($status == 'selesai') $badge_class = 'success';
                                                                        ?>
                                                                        <span class="badge bg-<?= $badge_class ?>"><?= ucfirst($row['status_pengaduan']) ?></span>
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex flex-wrap gap-3 meta-item">
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="bi bi-tag"></i>
                                                                        <span>
                                                                            <?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada kategori'); ?>
                                                                        </span>
                                                                    </span>
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="bi bi-geo-alt"></i>
                                                                        <span>
                                                                            <?php echo htmlspecialchars($row['lokasi']); ?>
                                                                        </span>
                                                                    </span>
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="bi bi-calendar"></i>
                                                                        <span>
                                                                            <?php echo date('d M Y', strtotime($row['tanggal_pengaduan'])); ?>
                                                                        </span>
                                                                    </span>
                                                                    <?php if ($has_feedback): ?>
                                                                        <span class="d-flex align-items-center gap-1 text-success fw-semibold">
                                                                            <i class="bi bi-chat-dots"></i>
                                                                            <span>Ada Tanggapan</span>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex gap-2 ms-3">
                                                                <button class="btn btn-primary action-btn"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#detailModal<?php echo $row['id_input']; ?>"
                                                                    title="Lihat Detail">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Detail Modal -->
                                                <div class="modal fade" id="detailModal<?php echo $row['id_input']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="bi bi-file-text"></i>
                                                                    Detail Aspirasi
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Kategori</div>
                                                                        <div class="detail-value">
                                                                            <i class="bi bi-tag-fill text-primary"></i>
                                                                            <?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada kategori'); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Status</div>
                                                                        <div class="detail-value">
                                                                            <span class="badge bg-<?= $badge_class ?>"><?= ucfirst($row['status_pengaduan']) ?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Lokasi</div>
                                                                        <div class="detail-value">
                                                                            <i class="bi bi-geo-alt-fill text-danger"></i>
                                                                            <?php echo htmlspecialchars($row['lokasi']); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="detail-label">Tanggal Pengaduan</div>
                                                                        <div class="detail-value">
                                                                            <i class="bi bi-calendar-check text-primary"></i>
                                                                            <?php echo date('d F Y', strtotime($row['tanggal_pengaduan'])); ?>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="detail-label">Keterangan</div>
                                                                        <div class="detail-value">
                                                                            <div class="p-3 bg-light rounded">
                                                                                <?php echo nl2br(htmlspecialchars($row['keterangan'])); ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <?php if ($has_feedback): ?>
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="detail-label">
                                                                                <i class="bi bi-chat-left-quote-fill text-success"></i>
                                                                                Tanggapan dari Admin
                                                                            </div>
                                                                            <?php foreach ($feedbacks as $fb): ?>
                                                                                <div class="feedback-box mb-3" style="border-left: 4px solid #0072ff; background: #f0f7ff;">
                                                                                    <div class="fw-bold mb-1 text-primary">Admin:</div>
                                                                                    <?php echo nl2br(htmlspecialchars($fb['umpan_balik'])); ?>
                                                                                    <div class="mt-2 text-muted small" style="font-size: 0.75rem;">
                                                                                        <i class="bi bi-clock"></i>
                                                                                        <?php echo date('d F Y, H:i', strtotime($fb['tanggal_pengaduan'])); ?>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endforeach; ?>

                                                                        </div>
                                                                    </div>

                                                                <?php else: ?>
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="alert alert-info mb-0">
                                                                                <i class="bi bi-info-circle"></i>
                                                                                Belum ada tanggapan dari admin. Mohon menunggu.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <div class="col-12">
                                                <div class="empty-state">
                                                    <i class="bi bi-inbox"></i>
                                                    <h5>Belum Ada Aspirasi</h5>
                                                    <p class="text-muted">Anda belum memiliki riwayat aspirasi.
                                                        Mulai kirim aspirasi pertama Anda!</p>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div> <!-- /.row -->
                                </div> <!-- /.histori-body -->

                                <?php if ($total_pages > 1): ?>
                                    <div class="d-flex justify-content-center align-items-center gap-2 mt-4 flex-wrap">

                                        <!-- Tombol Prev -->
                                        <?php if ($page > 1): ?>
                                            <a href="?page=<?= $page - 1 ?>#histori" class="btn btn-sm btn-outline-primary px-3">
                                                <i class="bi bi-chevron-left"></i> Prev
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary px-3" disabled>
                                                <i class="bi bi-chevron-left"></i> Prev
                                            </button>
                                        <?php endif; ?>

                                        <!-- Nomor Halaman -->
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <a href="?page=<?= $i ?>#histori"
                                                class="btn btn-sm <?= $i == $page ? 'btn-primary' : 'btn-outline-primary' ?> px-3">
                                                <?= $i ?>
                                            </a>
                                        <?php endfor; ?>

                                        <!-- Tombol Next -->
                                        <?php if ($page < $total_pages): ?>
                                            <a href="?page=<?= $page + 1 ?>#histori" class="btn btn-sm btn-outline-primary px-3">
                                                Next <i class="bi bi-chevron-right"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary px-3" disabled>
                                                Next <i class="bi bi-chevron-right"></i>
                                            </button>
                                        <?php endif; ?>

                                    </div>

                                    <!-- Info halaman -->
                                    <div class="text-center text-muted mt-2" style="font-size: 12px;">
                                        Halaman <?= $page ?> dari <?= $total_pages ?>
                                        &nbsp;·&nbsp; Total <?= $total_data ?> aspirasi
                                    </div>
                                <?php endif; ?>

                            </div> <!-- /.histori-wrapper -->
                        </div><!-- /.row histori -->
                    </div><!-- /#histori -->
                   <!-- histori section end -->

                </div><!-- /.aspirasi-container -->
            </div><!-- /.col -->
        </div><!-- /.col -->
    </div><!-- /.container -->

    <div class="az-footer ht-40">
        <div class="container ht-100p pd-t-0-f">
            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">
                Copyright ©smkkusumajaya.com 2025
            </span>
        </div>
    </div>

    <script src="../assets/lib/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/ionicons/ionicons.js"></script>
    <script src="../assets/js/azia.js"></script>
</body>

</html>