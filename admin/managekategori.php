<?php
session_start();
require_once '../models/database.php';
require_once '../models/auth.php';
$auth = new Auth();
$auth->restrictToAdmin();

$database = new Database();
$conn     = $database->koneksi();

$result = $conn->query("SELECT * FROM kategori ORDER BY id_kategori ASC");
if (!$result) die("Query error: " . $conn->error);

$total = $result->num_rows;
$no    = 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori — UJIKOM</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/sdb-style.css">

</head>

<body class="sdb-root kat-root">
    <!-- NAVBAR — az-header -->
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
                    <li class="nav-item">
                        <a href="dashboard-admin.php" class="nav-link">
                            <i class="typcn typcn-chart-area-outline"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item active show">
                        <a href="" class="nav-link with-sub">
                            <i class="typcn typcn-document"></i> Manage
                        </a>
                        <nav class="az-menu-sub">
                            <a href="managesiswa.php" class="nav-link">Siswa Management</a>
                            <a href="managekategori.php" class="nav-link">Kategori Management</a>
                        </nav>
                    </li>
                    <div class="kat-date-box">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <?php echo date('d M Y'); ?>
                    </div>
                </ul>
            </div>

            <div class="az-header-right">
                <a href="../app/logout.php" class="sdb-btn sdb-btn--secondary" style="font-size:0.78rem;padding:0.35rem 0.8rem;">
                    Logout
                </a>
            </div>

        </div>
    </div>
    <!-- ═══════════════════════════════════════════════════════════
     KONTEN UTAMA
════════════════════════════════════════════════════════════ -->
    <div class="kat-container">

        <!-- ALERTS -->
        <?php
        $alertConfig = null;

        if (isset($_GET['success'])) {
            $s = $_GET['success'];
            $alertMap = [
                'tambah' => [
                    'type'  => 'success',
                    'badge' => 'Tambah',
                    'badge_class' => 'kat-alert__badge--green',
                    'msg'   => 'Kategori baru berhasil ditambahkan.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>',
                ],
                'edit' => [
                    'type'  => 'info',
                    'badge' => 'Edit',
                    'badge_class' => 'kat-alert__badge--blue',
                    'msg'   => 'Kategori berhasil diperbarui.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
                ],
                'hapus' => [
                    'type'  => 'warning',
                    'badge' => 'Hapus',
                    'badge_class' => 'kat-alert__badge--red',
                    'msg'   => 'Kategori berhasil dihapus permanen.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>',
                ],
            ];
            $alertConfig = $alertMap[$s] ?? [
                'type' => 'success',
                'badge' => 'OK',
                'badge_class' => 'kat-alert__badge--green',
                'msg' => 'Operasi berhasil.',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
            ];
        }

        if (isset($_GET['error'])) {
            $e = $_GET['error'];
            $errMap = [
                'empty'    => ['badge' => 'Input Kosong',    'msg' => 'Nama kategori wajib diisi.'],
                'duplikat' => ['badge' => 'Duplikat',        'msg' => 'Nama kategori sudah ada, gunakan nama lain.'],
                'query'    => ['badge' => 'Database Error',  'msg' => 'Terjadi kesalahan pada database.'],
                'notfound' => ['badge' => 'Tidak Ditemukan', 'msg' => 'Kategori tidak ditemukan.'],
                'invalid'  => ['badge' => 'ID Tidak Valid',  'msg' => 'ID kategori tidak valid.'],
            ];
            $err = $errMap[$e] ?? ['badge' => 'Error', 'msg' => 'Terjadi kesalahan.'];
            $alertConfig = [
                'type'        => 'error',
                'badge'       => $err['badge'],
                'badge_class' => 'kat-alert__badge--red',
                'msg'         => $err['msg'],
                'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
            ];
        }

        if ($alertConfig):
            $typeClass = [
                'success' => 'kat-alert--success',
                'info'    => 'kat-alert--info',
                'warning' => 'kat-alert--warning',
                'error'   => 'kat-alert--error',
            ][$alertConfig['type']] ?? 'kat-alert--error';
        ?>
            <div class="kat-alert <?= $typeClass ?>" id="katAlertBox">
                <div class="kat-alert__left">
                    <svg class="kat-alert__icon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <?= $alertConfig['icon'] ?>
                    </svg>
                    <span class="kat-alert__badge <?= $alertConfig['badge_class'] ?>">
                        <?= htmlspecialchars($alertConfig['badge']) ?>
                    </span>
                    <span class="kat-alert__msg"><?= htmlspecialchars($alertConfig['msg']) ?></span>
                </div>
                <button class="kat-alert__close" onclick="document.getElementById('katAlertBox').remove()" title="Tutup">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <!-- PAGE HEADER -->
        <div class="kat-page-header">
            <div class="kat-page-title">
                <h1>Manajemen Kategori</h1>
                <p>Tambah, ubah, dan hapus kategori pengaduan</p>
            </div>
            <div class="kat-header-right">

                <button class="kat-btn kat-btn--primary" onclick="katOpenModal('kat-modal-tambah')">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kategori
                </button>
            </div>
        </div>

        <!-- STATS -->
        <div class="kat-stats-row">
            <div class="kat-stat-card">
                <div class="kat-stat-card__label">Total Kategori</div>
                <div class="kat-stat-card__value"><?= $total ?></div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="kat-table-container">
            <div class="kat-table-header">
                <h3>Daftar Kategori</h3>
                <div class="kat-search-box">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                    </svg>
                    <input type="text" placeholder="Cari kategori..." id="katSearchInput" onkeyup="katSearchTable()">
                </div>
            </div>

            <table class="kat-table" id="katTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total > 0):
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="kat-td--no"><?= $no++ ?></td>
                                <td class="kat-td--id"><?= (int)$row['id_kategori'] ?></td>
                                <td class="kat-td--name"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                <td>
                                    <div class="kat-actions">
                                        <button class="kat-btn kat-btn--edit"
                                            onclick="katOpenEdit(
                                    <?= (int)$row['id_kategori'] ?>,
                                    '<?= htmlspecialchars(addslashes($row['nama_kategori'])) ?>'
                                )">
                                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                           m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button class="kat-btn kat-btn--delete"
                                            onclick="katOpenHapus(
                                    <?= (int)$row['id_kategori'] ?>,
                                    '<?= htmlspecialchars(addslashes($row['nama_kategori'])) ?>'
                                )">
                                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                           L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="kat-empty-state">
                                    <svg width="44" height="44" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7
                                       a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <p>Belum ada kategori. Klik <strong>Tambah Kategori</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /kat-container -->


    <!-- ═══════════════════════════════════════════════════════════
     MODAL TAMBAH
════════════════════════════════════════════════════════════ -->
    <div class="kat-modal-backdrop" id="kat-modal-tambah">
        <div class="kat-modal">
            <div class="kat-modal__header">
                <div class="kat-modal__title">Tambah Kategori</div>
                <button class="kat-modal__close" onclick="katCloseModal('kat-modal-tambah')">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="../app/tambah-kategori.php" method="POST">
                <div class="kat-form-group">
                    <label class="kat-form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="kat-form-control"
                        placeholder="Contoh: Fasilitas Sekolah" required>
                </div>
                <div class="kat-modal__footer">
                    <button type="button" class="kat-btn kat-btn--secondary"
                        onclick="katCloseModal('kat-modal-tambah')">Batal</button>
                    <button type="submit" class="kat-btn kat-btn--primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <!-- ═══════════════════════════════════════════════════════════
     MODAL EDIT
════════════════════════════════════════════════════════════ -->
    <div class="kat-modal-backdrop" id="kat-modal-edit">
        <div class="kat-modal">
            <div class="kat-modal__header">
                <div class="kat-modal__title">Edit Kategori</div>
                <button class="kat-modal__close" onclick="katCloseModal('kat-modal-edit')">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="../app/edit-kategori.php" method="POST">
                <input type="hidden" name="id_kategori" id="kat-edit-id">
                <div class="kat-form-group">
                    <label class="kat-form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="kat-edit-nama"
                        class="kat-form-control" required>
                </div>
                <div class="kat-modal__footer">
                    <button type="button" class="kat-btn kat-btn--secondary"
                        onclick="katCloseModal('kat-modal-edit')">Batal</button>
                    <button type="submit" class="kat-btn kat-btn--primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>



    <!-- MODAL HAPUS -->

    <div class="kat-modal-backdrop" id="kat-modal-hapus">
        <div class="kat-modal kat-modal--sm">
            <div class="kat-confirm__header">
                <div class="kat-confirm__icon">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94
                           a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
                <div class="kat-confirm__title">Hapus Kategori?</div>
                <p class="kat-confirm__text" id="kat-hapus-text">
                    Kategori ini akan dihapus permanen.
                </p>
            </div>
            <form action="../app/hapus-kategori.php" method="POST">
                <input type="hidden" name="id_kategori" id="kat-hapus-id">
                <div class="kat-modal__footer kat-modal__footer--center">
                    <button type="button" class="kat-btn kat-btn--secondary"
                        onclick="katCloseModal('kat-modal-hapus')">Batal</button>
                    <button type="submit" class="kat-btn kat-btn--delete kat-btn--delete-lg">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- JAVASCRIPT -->

    <script>
        function katOpenModal(id) {
            document.getElementById(id).classList.add('kat-active');
        }

        function katCloseModal(id) {
            document.getElementById(id).classList.remove('kat-active');
        }

        document.querySelectorAll('.kat-modal-backdrop').forEach(function(el) {
            el.addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('kat-active');
            });
        });

        function katOpenEdit(id, nama) {
            document.getElementById('kat-edit-id').value = id;
            document.getElementById('kat-edit-nama').value = nama;
            katOpenModal('kat-modal-edit');
        }

        function katOpenHapus(id, nama) {
            document.getElementById('kat-hapus-id').value = id;
            document.getElementById('kat-hapus-text').textContent =
                'Kategori "' + nama + '" akan dihapus permanen dan tidak bisa dikembalikan.';
            katOpenModal('kat-modal-hapus');
        }

        function katSearchTable() {
            var input = document.getElementById('katSearchInput').value.toLowerCase();
            document.querySelectorAll('#katTable tbody tr').forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(input) ? '' : 'none';
            });
        }

        // Auto-dismiss alert setelah 4 detik
        (function() {
            var box = document.getElementById('katAlertBox');
            if (!box) return;
            setTimeout(function() {
                box.style.transition = 'opacity .4s, transform .4s';
                box.style.opacity = '0';
                box.style.transform = 'translateY(-6px)';
                setTimeout(function() {
                    box.remove();
                }, 400);
            }, 4000);
        })();

        // Toggle mobile menu
        var menuBtn = document.getElementById('azMenuShow');
        var menuEl = document.getElementById('azHeaderMenu');
        if (menuBtn && menuEl) {
            menuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                menuEl.classList.toggle('show');
            });
        }
    </script>

</body>

</html>