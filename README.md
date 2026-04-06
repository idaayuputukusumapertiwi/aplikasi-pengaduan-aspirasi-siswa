# ��� Aplikasi Pengaduan & Aspirasi Siswa
### SMK KUSUMA JAYA

![PHP](https://img.shields.io/badge/PHP-7.4+-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange?logo=mysql)
![XAMPP](https://img.shields.io/badge/Server-XAMPP-red?logo=apache)

Aplikasi berbasis web untuk mengelola pengaduan dan aspirasi siswa di SMK Kusuma Jaya. Siswa dapat menyampaikan aspirasi secara digital, dan admin dapat mengelola serta menanggapi setiap pengaduan.

---

## ✨ Fitur Utama

### ���‍��� Admin
- Login admin yang aman
- Dashboard untuk melihat semua aspirasi masuk
- Filter aspirasi berdasarkan kelas
- Memberi tanggapan pada aspirasi siswa
- Mengubah status aspirasi (Pending / Diproses / Selesai)
- Menghapus aspirasi
- Manajemen data siswa (tambah, edit, hapus)
- Manajemen kategori pengaduan
- Pagination dan AJAX handler

### ��� Siswa
- Login siswa
- Dashboard pribadi
- Kirim pengaduan/aspirasi
- Lihat status pengaduan yang dikirim

---

## ���️ Teknologi yang Digunakan

| Teknologi | Keterangan |
|-----------|------------|
| PHP | Backend scripting |
| MySQL | Database |
| XAMPP | Local server (Apache + MySQL) |
| HTML/CSS | Tampilan antarmuka |
| JavaScript | Interaktivitas & AJAX |
| Bootstrap | Framework CSS |

---

## ��� Struktur Folder
UJIKOM/
├── admin/                  # Halaman & fitur admin
│   ├── dashboard-admin.php
│   ├── login-admin.php
│   ├── managesiswa.php
│   └── managekategori.php
├── app/                    # Logika aplikasi
│   ├── edit-siswa.php
│   ├── hapus-siswa.php
│   ├── login-process.php
│   ├── proses_aspirasi.php
│   └── ...
├── assets/                 # CSS, JS, Gambar
│   ├── css/
│   ├── js/
│   ├── img/
│   └── ...
├── models/                 # Model database & auth
│   ├── database.php
│   ├── auth.php
│   └── function.php
├── siswa/                  # Halaman siswa
│   ├── dashboard.php
│   └── login-siswa.php
├── utils/                  # Utility functions
├── config/                 # Konfigurasi (tidak diupload)
├── index.php
├── login-as.php
└── pengaduan_siswa.sql     # File database

---

## ⚙️ Cara Instalasi

### Prasyarat
- XAMPP (PHP 7.4+ & MySQL 5.7+)
- Browser modern

### Langkah Instalasi

1. **Clone repository ini**
```bash
   git clone https://github.com/USERNAME/aplikasi-pengaduan-aspirasi-siswa.git
```

2. **Pindahkan ke folder XAMPP**
```bash
   # Pindahkan folder ke:
   C:/xampp/htdocs/UJIKOM
```

3. **Import database**
   - Buka `phpMyAdmin` → `http://localhost/phpmyadmin`
   - Buat database baru: `pengaduan_siswa`
   - Import file `pengaduan_siswa.sql`

4. **Konfigurasi koneksi database**
   - Salin file `config/config.example.php` → `config/config.php`
   - Sesuaikan isi file:
```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'pengaduan_siswa');
```

5. **Jalankan aplikasi**
   - Buka browser: `http://localhost/UJIKOM`

---

## ��� Akun Default

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | (sesuai database) |
| Siswa | (NIS siswa) | (sesuai database) |

---

## ��� Screenshots

> *(Tambahkan screenshot aplikasi di sini)*

---

## ���‍��� Developer

**Ida Ayu Putukusuma Pertiwi**
SMK KUSUMA JAYA

---

## ��� Lisensi

Project ini dibuat untuk keperluan tugas akhir / UJIKOM SMK Kusuma Jaya.
