<?php
// app/tambah-kategori.php

require_once '../models/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/managekategori.php");
    exit;
}

$database = new Database();
$conn     = $database->koneksi();

$nama_kategori = trim($_POST['nama_kategori'] ?? '');

if (empty($nama_kategori)) {
    header("Location: ../admin/managekategori.php?error=empty");
    exit;
}

$nama_safe = $conn->real_escape_string($nama_kategori);

// Cek duplikat
$cek = $conn->query("SELECT id_kategori FROM kategori WHERE nama_kategori = '$nama_safe'");
if (!$cek) {
    header("Location: ../admin/managekategori.php?error=query");
    exit;
}
if ($cek->num_rows > 0) {
    header("Location: ../admin/managekategori.php?error=duplikat");
    exit;
}

$query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_safe')";

if ($conn->query($query)) {
    header("Location: ../admin/managekategori.php?success=tambah");
} else {
    header("Location: ../admin/managekategori.php?error=query");
}
exit;
?>