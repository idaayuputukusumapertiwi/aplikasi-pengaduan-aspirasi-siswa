<?php
/**
 * Login Process Handler
 * Handles login for both Admin and Siswa
 */

// Include functions
require_once "../models/function.php";

// Start session
session_start();

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get login type
    $login_type = $_POST['login_type'] ?? '';
    
    // Initialize database connection
    $db = new Database();
    $conn = $db->koneksi();
    
    // Initialize auth class
    $auth = new Auth();
    $auth->setConnection($conn);
    
    // Process login based on type
    if ($login_type === 'admin') {
        // Admin Login
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($username) || empty($password)) {
            redirect('../admin/login-admin.php?error=empty');
        }
        
        // Attempt login
        if ($auth->loginAdmin($username, $password)) {
            // Login successful - redirect to admin dashboard
            redirect('../admin/dashboard-admin.php');
        } else {
            // Login failed
            redirect('../admin/login-admin.php?error=invalid');
        }
        
    } elseif ($login_type === 'siswa') {
        // Siswa Login
        $nis = $_POST['nis'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($nis) || empty($password)) {
            redirect('../siswa/login-siswa.php?error=empty');
        }
        
        // Attempt login
        $siswa = $auth->loginSiswa($nis, $password);
        if ($siswa) {
        session_unset(); // bersihin session admin kalo ada

        $_SESSION['user_id']   = $siswa['id_siswa']; 
        $_SESSION['nis']       = $siswa['nis'];
        $_SESSION['nama']      = $siswa['nama'];
        $_SESSION['user_type'] = 'siswa';
        redirect('../siswa/dashboard.php');
        }
        else {
            // Login failed
            redirect('../siswa/login-siswa.php?error=invalid');
        }
        
    } else {
        // Invalid login type
        redirect('../index.php');
    }
    
    // Close database connection
    $db->close();
    
} else {
    // Not a POST request
    redirect('../index.php');
}
?>