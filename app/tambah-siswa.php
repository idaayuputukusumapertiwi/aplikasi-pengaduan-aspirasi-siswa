<?php
// admin/tambah-siswa.php

require_once '../models/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: managesiswa.php");
    exit;
}

// Instansiasi class Database
$database = new Database();
$conn     = $database->koneksi();

// Ambil & bersihkan input
$nis      = trim($_POST['nis']      ?? '');
$nama     = trim($_POST['nama']     ?? '');
$kelas    = trim($_POST['kelas']    ?? '');
$password =      $_POST['password'] ?? '';

// Validasi: semua field wajib diisi
if (empty($nis) || empty($nama) || empty($kelas) || empty($password)) {
    header("Location: managesiswa.php?error=empty");
    exit;
}

// Escape input pakai OOP
$nis_safe   = $conn->real_escape_string($nis);
$nama_safe  = $conn->real_escape_string($nama);
$kelas_safe = $conn->real_escape_string($kelas);

// Cek NIS duplikat
$cek = $conn->query("SELECT id_siswa FROM siswa WHERE nis = '$nis_safe'");
if (!$cek) {
    header("Location: managesiswa.php?error=query");
    exit;
}
if ($cek->num_rows > 0) {
    header("Location: ../admin/managesiswa.php?error=nis_duplikat&from=tambah");
    exit;
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert ke database
$query = "INSERT INTO siswa (nis, nama, kelas, password)
          VALUES ('$nis_safe', '$nama_safe', '$kelas_safe', '$hash')";

if ($conn->query($query)) {
    header("Location: ../admin/managesiswa.php?success=tambah");
} else {
    header("Location: ../admin/managesiswa.php?error=query");
}
exit;
