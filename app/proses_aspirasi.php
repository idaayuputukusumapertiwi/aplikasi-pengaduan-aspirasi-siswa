<?php
session_start();
require_once '../models/database.php';


// Ambil data dari session & form
$id_siswa    = $_SESSION['user_id'];
$lokasi      = $_POST['lokasi'];
$keterangan  = $_POST['keterangan'];
$id_kategori = $_POST['id_kategori'];
$tanggal     = date('Y-m-d'); // otomatis tanggal pengaduan

// Koneksi database
$db   = new Database();
$conn = $db->koneksi();

// Query insert ke tabel input_aspirasi
$query = "INSERT INTO input_aspirasi 
          (id_siswa, lokasi, keterangan, id_kategori, tanggal_pengaduan) 
          VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);

// Bind parameter
mysqli_stmt_bind_param(
    $stmt,
    "sssis",
    $id_siswa,
    $lokasi,
    $keterangan,
    $id_kategori,
    $tanggal
);

// Eksekusi
if (mysqli_stmt_execute($stmt)) {
      $_SESSION['success'] = "Aspirasi berhasil dikirim. Terima kasih atas masukannya 🙌";
    header("Location: ../siswa/dashboard.php");
    exit;
} else {
    echo "❌ Gagal menyimpan aspirasi.";
}
