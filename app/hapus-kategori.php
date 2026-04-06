<?php
// app/hapus-kategori.php

require_once '../models/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/managekategori.php");
    exit;
}

$database = new Database();
$conn     = $database->koneksi();

$id_kategori = (int) ($_POST['id_kategori'] ?? 0);

if ($id_kategori <= 0) {
    header("Location: ../admin/managekategori.php?error=invalid");
    exit;
}

$cek = $conn->query("SELECT id_kategori FROM kategori WHERE id_kategori = $id_kategori");
if (!$cek) {
    header("Location: ../admin/managekategori.php?error=query");
    exit;
}
if ($cek->num_rows === 0) {
    header("Location: ../admin/managekategori.php?error=notfound");
    exit;
}

if ($conn->query("DELETE FROM kategori WHERE id_kategori = $id_kategori")) {
    header("Location: ../admin/managekategori.php?success=hapus");
} else {
    header("Location: ../admin/managekategori.php?error=query");
}
exit;
?>