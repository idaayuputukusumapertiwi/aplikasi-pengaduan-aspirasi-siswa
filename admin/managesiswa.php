<?php
session_start();
require_once '../models/auth.php';
$auth = new Auth();
$auth->restrictToAdmin();
require_once '../models/database.php';

$database = new Database();
$conn     = $database->koneksi();

$result = $conn->query("SELECT * FROM siswa ORDER BY id_siswa ASC");
if (!$result) die("Query error: " . $conn->error);

$total = $result->num_rows;
$q10   = $conn->query("SELECT COUNT(*) AS jml FROM siswa WHERE kelas='10'")->fetch_assoc()['jml'];
$q11   = $conn->query("SELECT COUNT(*) AS jml FROM siswa WHERE kelas='11'")->fetch_assoc()['jml'];
$q12   = $conn->query("SELECT COUNT(*) AS jml FROM siswa WHERE kelas='12'")->fetch_assoc()['jml'];

$no = 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Siswa — UJIKOM</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/sdb-style.css">
</head>

<body class="sdb-root">

    <!-- ═══════════════════════════════════════════════════════════
     NAVBAR — az-header (AzureLTE)
════════════════════════════════════════════════════════════ -->
    <div class="az-header sticky-top">
        <div class="container">

            <!-- Kiri: logo -->
            <div class="az-header-left">
                <a href="dashboard-admin.php" class="az-logo">
                    <span></span>
                    <img src="../assets/img/logosekolah2.png" width="42px" style="margin:0 10px 0 0;">
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
                    <div class="sdb-date-box">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <?php echo date('d M Y'); ?>
                    </div>
                </ul>
            </div>

            <!-- Kanan: profil -->
            <div class="az-header-right">
                <a href="../app/logout.php" class="sdb-btn sdb-btn--secondary" style="font-size:0.78rem;padding:0.35rem 0.8rem;">
                    Logout
                </a>
            </div>

        </div>
    </div><!-- /az-header -->



    <div class="sdb-container">

        <!-- ALERTS -->
        <?php if (isset($_GET['success'])): ?>
            <div class="sdb-alert sdb-alert--success" style="padding: 10px 10px; margin: 0 0 10px 0;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <?php
                $msg = [
                    'tambah' => 'Data siswa berhasil ditambahkan.',
                    'edit'   => 'Data siswa berhasil diperbarui.',
                    'hapus'  => 'Data siswa berhasil dihapus.',
                ];
                echo htmlspecialchars($msg[$_GET['success']] ?? 'Operasi berhasil.');
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] !== 'nis_duplikat'): ?>
            <div class="sdb-alert sdb-alert--error" style="padding: 10px 10px; margin: 0 0 10px 0;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <?php
                $errMsg = [
                    'empty'        => 'Semua field wajib diisi.',
                    'nis_duplikat' => 'NIS sudah digunakan siswa lain.',
                    'query'        => 'Terjadi kesalahan database.',
                    'notfound'     => 'Data tidak ditemukan.',
                    'invalid'      => 'ID tidak valid.',
                ];
                echo htmlspecialchars($errMsg[$_GET['error']] ?? 'Terjadi kesalahan.');
                ?>
            </div>
        <?php endif; ?>

        <!-- PAGE HEADER -->
        <div class="sdb-page-header">
            <div class="sdb-page-title">
                <h1>Manajemen Siswa</h1>
                <p>Tambah, ubah, dan hapus data siswa</p>
            </div>
            <div class="sdb-header-right">

                <button class="sdb-btn sdb-btn--primary" onclick="sdbOpenModal('sdb-modal-tambah')">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data
                </button>
            </div>
        </div>

        <!-- STATS -->
        <div class="sdb-stats-row">
            <div class="sdb-stat-card">
                <div class="sdb-stat-card__label">Total Siswa</div>
                <div class="sdb-stat-card__value"><?= $total ?></div>
            </div>
            <div class="sdb-stat-card">
                <div class="sdb-stat-card__label">Kelas 10</div>
                <div class="sdb-stat-card__value"><?= $q10 ?></div>
            </div>
            <div class="sdb-stat-card">
                <div class="sdb-stat-card__label">Kelas 11</div>
                <div class="sdb-stat-card__value"><?= $q11 ?></div>
            </div>
            <div class="sdb-stat-card">
                <div class="sdb-stat-card__label">Kelas 12</div>
                <div class="sdb-stat-card__value"><?= $q12 ?></div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="sdb-table-container">
            <div class="sdb-table-header">
                <h3>Daftar Siswa</h3>
                <div class="sdb-search-box">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                    </svg>
                    <input type="text" placeholder="Cari siswa..." id="sdbSearchInput" onkeyup="sdbSearchTable()">
                </div>
            </div>

            <table class="sdb-table" id="sdbSiswaTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total > 0):
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="sdb-td--no"><?= $no++ ?></td>
                                <td class="sdb-td--nis"><?= htmlspecialchars($row['nis']) ?></td>
                                <td class="sdb-td--name"><?= htmlspecialchars($row['nama']) ?></td>
                                <td>
                                    <span class="sdb-badge-kelas sdb-badge-kelas--<?= $row['kelas'] ?>">
                                        Kelas <?= $row['kelas'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="sdb-actions">
                                        <button class="sdb-btn sdb-btn--edit"
                                            onclick="sdbOpenEdit(
                                    <?= (int)$row['id_siswa'] ?>,
                                    '<?= htmlspecialchars(addslashes($row['nis'])) ?>',
                                    '<?= htmlspecialchars(addslashes($row['nama'])) ?>',
                                    '<?= $row['kelas'] ?>'
                                )">
                                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                           m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button class="sdb-btn sdb-btn--delete"
                                            onclick="sdbOpenHapus(
                                    <?= (int)$row['id_siswa'] ?>,
                                    '<?= htmlspecialchars(addslashes($row['nama'])) ?>'
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
                            <td colspan="5">
                                <div class="sdb-empty-state">
                                    <svg width="44" height="44" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p>Belum ada data siswa. Klik <strong>Tambah Data</strong> untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /sdb-container -->


    <!-- ═══════════════════════════════════════════════════════════
     MODAL TAMBAH
════════════════════════════════════════════════════════════ -->
    <div class="sdb-modal-backdrop" id="sdb-modal-tambah">
        <div class="sdb-modal">
            <div class="sdb-modal__header">
                <div class="sdb-modal__title">Tambah Data Siswa</div>
                <button class="sdb-modal__close" onclick="sdbCloseModal('sdb-modal-tambah')">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="../app/tambah-siswa.php" method="POST">
                <div class="sdb-form-group">
                    <label class="sdb-form-label">NIS</label>
                    <input type="text" name="nis" class="sdb-form-control" placeholder="Contoh: 12345" maxlength="5" required>
                </div>
                <div class="sdb-form-group">
                    <label class="sdb-form-label">Nama Siswa</label>
                    <input type="text" name="nama" class="sdb-form-control" placeholder="Nama lengkap siswa" required>
                </div>
                <div class="sdb-form-group">
                    <label class="sdb-form-label">Kelas</label>
                    <select name="kelas" class="sdb-form-control" required>
                        <option value="" disabled selected>Pilih kelas</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>
                </div>
                <div class="sdb-form-group">
                    <label class="sdb-form-label">Password</label>
                    <input type="password" name="password" class="sdb-form-control" placeholder="Password siswa" required>
                </div>
                <div class="sdb-modal__footer">
                    <button type="button" class="sdb-btn sdb-btn--secondary" onclick="sdbCloseModal('sdb-modal-tambah')">Batal</button>
                    <button type="submit" class="sdb-btn sdb-btn--primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <!-- ═══════════════════════════════════════════════════════════
     MODAL EDIT
════════════════════════════════════════════════════════════ -->
    <div class="sdb-modal-backdrop" id="sdb-modal-edit">
        <div class="sdb-modal">
            <div class="sdb-modal__header">
                <div class="sdb-modal__title">Edit Data Siswa</div>
                <button class="sdb-modal__close" onclick="sdbCloseModal('sdb-modal-edit')">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="../app/edit-siswa.php" method="POST">
                <input type="hidden" name="id_siswa" id="sdb-edit-id">
                <div class="sdb-form-group">
                    <label class="sdb-form-label">NIS</label>
                    <input type="text" name="nis" id="sdb-edit-nis" class="sdb-form-control" maxlength="5" required>
                </div>
                <div class="sdb-form-group">
                    <label class="sdb-form-label">Nama Siswa</label>
                    <input type="text" name="nama" id="sdb-edit-nama" class="sdb-form-control" required>
                </div>
                <div class="sdb-form-group">
                    <label class="sdb-form-label">Kelas</label>
                    <select name="kelas" id="sdb-edit-kelas" class="sdb-form-control" required>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>
                </div>
                <div class="sdb-form-group">
                    <label class="sdb-form-label">
                        Password Baru
                        <span class="sdb-form-label__hint">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password" class="sdb-form-control" placeholder="Password baru (opsional)">
                </div>
                <div class="sdb-modal__footer">
                    <button type="button" class="sdb-btn sdb-btn--secondary" onclick="sdbCloseModal('sdb-modal-edit')">Batal</button>
                    <button type="submit" class="sdb-btn sdb-btn--primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    <!-- ═══════════════════════════════════════════════════════════
     MODAL HAPUS
════════════════════════════════════════════════════════════ -->
    <div class="sdb-modal-backdrop" id="sdb-modal-hapus">
        <div class="sdb-modal sdb-modal--sm">
            <div class="sdb-confirm__header">
                <div class="sdb-confirm__icon">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3
                           L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
                <div class="sdb-confirm__title">Hapus Data Siswa?</div>
                <p class="sdb-confirm__text" id="sdb-hapus-text">Data siswa ini akan dihapus permanen.</p>
            </div>
            <form action="../app/hapus-siswa.php" method="POST">
                <input type="hidden" name="id_siswa" id="sdb-hapus-id">
                <div class="sdb-modal__footer sdb-modal__footer--center">
                    <button type="button" class="sdb-btn sdb-btn--secondary" onclick="sdbCloseModal('sdb-modal-hapus')">Batal</button>
                    <button type="submit" class="sdb-btn sdb-btn--delete sdb-btn--delete-lg">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>


    <!-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════════════════════ -->
    <script>
        function sdbOpenModal(id) {
            document.getElementById(id).classList.add('sdb-active');
        }

        function sdbCloseModal(id) {
            document.getElementById(id).classList.remove('sdb-active');
        }

        // Tutup modal klik backdrop
        document.querySelectorAll('.sdb-modal-backdrop').forEach(function(el) {
            el.addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('sdb-active');
            });
        });

        // Buka modal edit & isi field
        function sdbOpenEdit(id, nis, nama, kelas) {
            document.getElementById('sdb-edit-id').value = id;
            document.getElementById('sdb-edit-nis').value = nis;
            document.getElementById('sdb-edit-nama').value = nama;
            document.getElementById('sdb-edit-kelas').value = kelas;
            sdbOpenModal('sdb-modal-edit');
        }

        // Buka modal hapus & isi info
        function sdbOpenHapus(id, nama) {
            document.getElementById('sdb-hapus-id').value = id;
            document.getElementById('sdb-hapus-text').textContent =
                'Data "' + nama + '" akan dihapus permanen dan tidak bisa dikembalikan.';
            sdbOpenModal('sdb-modal-hapus');
        }

        // Search tabel
        function sdbSearchTable() {
            var input = document.getElementById('sdbSearchInput').value.toLowerCase();
            document.querySelectorAll('#sdbSiswaTable tbody tr').forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(input) ? '' : 'none';
            });
        }

        // NIS duplikat — buka modal & tampilkan error di field NIS
        (function() {
            var error = <?= json_encode($_GET['error'] ?? '') ?>;
            var from = <?= json_encode($_GET['from']  ?? '') ?>;
            if (error !== 'nis_duplikat') return;

            var modalId = (from === 'edit') ? 'sdb-modal-edit' : 'sdb-modal-tambah';
            sdbOpenModal(modalId);

            var modal = document.getElementById(modalId);
            if (!modal) return;

            var nisInput = modal.querySelector('input[name="nis"]');
            if (!nisInput) return;

            nisInput.style.borderColor = '#fca5a5';
            nisInput.style.backgroundColor = '#fff1f2';
            nisInput.focus();

            var errEl = document.createElement('p');
            errEl.className = 'sdb-nis-error';
            errEl.style.cssText = 'color:#b91c1c;font-size:0.78rem;margin-top:5px;display:flex;align-items:center;gap:4px;';
            errEl.innerHTML = '<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> NIS sudah digunakan siswa lain.';
            nisInput.insertAdjacentElement('afterend', errEl);

            nisInput.addEventListener('input', function() {
                nisInput.style.borderColor = '';
                nisInput.style.backgroundColor = '';
                var el = modal.querySelector('.sdb-nis-error');
                if (el) el.remove();
            }, {
                once: true
            });
        })();

        // Toggle menu mobile
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