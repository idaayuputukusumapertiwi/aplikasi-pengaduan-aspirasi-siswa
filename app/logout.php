<?php
/**
 * Logout Handler
 */

// Include functions
require_once "../models/function.php";

// Start session
session_start();

// Initialize auth class
$auth = new Auth();

// Get user type before logout
$user_type = $auth->getUserType();

// Logout user
$auth->logout();

// Redirect based on user type
if ($user_type === 'admin') {
    redirect('../admin/login-admin.php?logout=berhasil');
} elseif ($user_type === 'siswa') {
    redirect('../siswa/login-siswa.php?logout=berhasil');
} else {
    redirect('../login-as.php');
}
?>