<?php
// app/hapus-siswa.php
// Pindah dari admin/ ke app/
// Perubahan: database  ../models/  | redirect  ../admin/managesiswa.php

require_once '../models/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/managesiswa.php");
    exit;
}

$database = new Database();
$conn     = $database->koneksi();

$id_siswa = (int) ($_POST['id_siswa'] ?? 0);

if ($id_siswa <= 0) {
    header("Location: ../admin/managesiswa.php?error=invalid");
    exit;
}

$cek = $conn->query("SELECT id_siswa FROM siswa WHERE id_siswa = $id_siswa");
if (!$cek) {
    header("Location: ../admin/managesiswa.php?error=query");
    exit;
}
if ($cek->num_rows === 0) {
    header("Location: ../admin/managesiswa.php?error=notfound");
    exit;
}

if ($conn->query("DELETE FROM siswa WHERE id_siswa = $id_siswa")) {
    header("Location: ../admin/managesiswa.php?success=hapus");
} else {
    header("Location: ../admin/managesiswa.php?error=query");
}
exit;
?>