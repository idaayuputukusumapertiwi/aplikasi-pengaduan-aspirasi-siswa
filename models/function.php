<?php
/**
 * ================================================
 * Aplikasi Pengaduan Siswa - SMK KUSUMA JAYA
 * ================================================
 * File    : function.php
 * Modul   : Helper / Fungsi Pembantu
 * Fungsi  : Menyediakan fungsi-fungsi pembantu (helper)
 *           yang digunakan di seluruh halaman aplikasi.
 *           File ini juga bertanggung jawab memulai session
 *           dan meng-include file model yang dibutuhkan.
 * Author  : [Nama Lo]
 * Tanggal : [Tanggal Dibuat]
 * Revisi  : [Tanggal Terakhir Diubah]
 * ================================================
 */
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database class
require_once __DIR__ . '/database.php';

// Include authentication class
require_once __DIR__ . '/auth.php';

/**
 * Redirect helper function
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if request is POST
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Generate password hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
?>