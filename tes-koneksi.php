<?php
require_once "./models/database.php";

$db = new Database();
$conn = $db->koneksi();

echo "Koneksi Berhasil";
