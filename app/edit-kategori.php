<?php
// app/edit-kategori.php

require_once '../models/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/managekategori.php");
    exit;
}

$database = new Database();
$conn     = $database->koneksi();

$id_kategori   = (int) ($_POST['id_kategori']   ?? 0);
$nama_kategori = trim($_POST['nama_kategori'] ?? '');

if ($id_kategori <= 0 || empty($nama_kategori)) {
    header("Location: ../admin/managekategori.php?error=empty");
    exit;
}

$nama_safe = $conn->real_escape_string($nama_kategori);

// Pastikan data ada
$cek_ada = $conn->query("SELECT id_kategori FROM kategori WHERE id_kategori = $id_kategori");
if (!$cek_ada || $cek_ada->num_rows === 0) {
    header("Location: ../admin/managekategori.php?error=notfound");
    exit;
}

// Cek duplikat nama (kecuali milik dirinya sendiri)
$cek_dup = $conn->query(
    "SELECT id_kategori FROM kategori
     WHERE nama_kategori = '$nama_safe' AND id_kategori != $id_kategori"
);
if (!$cek_dup) {
    header("Location: ../admin/managekategori.php?error=query");
    exit;
}
if ($cek_dup->num_rows > 0) {
    header("Location: ../admin/managekategori.php?error=duplikat");
    exit;
}

$query = "UPDATE kategori SET nama_kategori = '$nama_safe' WHERE id_kategori = $id_kategori";

if ($conn->query($query)) {
    header("Location: ../admin/managekategori.php?success=edit");
} else {
    header("Location: ../admin/managekategori.php?error=query");
}
exit;
?>