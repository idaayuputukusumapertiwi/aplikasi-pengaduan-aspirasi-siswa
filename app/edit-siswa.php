<?php
// app/edit-siswa.php
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
$nis      = trim($_POST['nis']      ?? '');
$nama     = trim($_POST['nama']     ?? '');
$kelas    = trim($_POST['kelas']    ?? '');
$password =      $_POST['password'] ?? '';

if ($id_siswa <= 0 || empty($nis) || empty($nama) || empty($kelas)) {
    header("Location: ../admin/managesiswa.php?error=empty");
    exit;
}

$nis_safe   = $conn->real_escape_string($nis);
$nama_safe  = $conn->real_escape_string($nama);
$kelas_safe = $conn->real_escape_string($kelas);

$cek_ada = $conn->query("SELECT id_siswa FROM siswa WHERE id_siswa = $id_siswa");
if (!$cek_ada || $cek_ada->num_rows === 0) {
    header("Location: ../admin/managesiswa.php?error=notfound");
    exit;
}

$cek_nis = $conn->query(
    "SELECT id_siswa FROM siswa WHERE nis = '$nis_safe' AND id_siswa != $id_siswa"
);
if (!$cek_nis) {
    header("Location: ../admin/managesiswa.php?error=query");
    exit;
}
if ($cek_nis->num_rows > 0) {
    header("Location: ../admin/managesiswa.php?error=nis_duplikat&from=edit");
    exit;
}

if (!empty($password)) {
    $hash  = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE siswa
              SET nis='$nis_safe', nama='$nama_safe', kelas='$kelas_safe', password='$hash'
              WHERE id_siswa = $id_siswa";
} else {
    $query = "UPDATE siswa
              SET nis='$nis_safe', nama='$nama_safe', kelas='$kelas_safe'
              WHERE id_siswa = $id_siswa";
}

if ($conn->query($query)) {
    header("Location: ../admin/managesiswa.php?success=edit");
} else {
    header("Location: ../admin/managesiswa.php?error=query");
}
exit;
