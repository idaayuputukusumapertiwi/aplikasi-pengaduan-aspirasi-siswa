<?php
/**
 * ================================================
 * Aplikasi Pengaduan Siswa - SMK KUSUMA JAYA
 * ================================================
 * File    : auth.php
 * Modul   : Autentikasi
 * Fungsi  : Mengelola proses login, logout, dan
 *           otorisasi akses halaman untuk Admin
 *           maupun Siswa menggunakan session PHP.
 * ================================================
 */

/**
 * Class Auth
 *
 * Bertanggung jawab atas seluruh proses autentikasi
 * dan otorisasi dalam aplikasi. Menggunakan session PHP
 * untuk menyimpan status login user.
 *
 * Fitur utama:
 * - Login untuk Admin (berdasarkan username & password)
 * - Login untuk Siswa (berdasarkan NIS & password)
 * - Pengecekan status login
 * - Proteksi halaman dari akses tidak sah
 * - Logout dan penghapusan session
 */
class Auth {
    private $conn;

    /**
     * Set database connection
     */
    public function setConnection($connection) {
        $this->conn = $connection;
    }

    /**
     * Login Admin
     */
    public function loginAdmin($username, $password) {
        // Sanitize input
        $username = $this->conn->real_escape_string($username);
        
        // Query to check admin credentials
        $query = "SELECT * FROM admin WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $admin['password'])) {
                // Set session
                $_SESSION['user_id'] = $admin['id_admin'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['nama'] = $admin['nama'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['login_time'] = time();
                
                return true;
            } 
        }
        
        return false;
    }

    /**
     * Login Siswa
     */
  public function loginSiswa($nis, $password)
{
    $query = "SELECT id_siswa, nis, nama, password
              FROM siswa
              WHERE nis = ?
              LIMIT 1";

    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $nis);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $siswa = mysqli_fetch_assoc($result);

    if ($siswa && password_verify($password, $siswa['password'])) {
        return $siswa;
    }

    return false;
}

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
    }

    /**
     * Get current user type
     */
    public function getUserType() {
        return $_SESSION['user_type'] ?? null;
    }

    /**
     * Logout user
     */
    public function logout() {
        $user_type = $_SESSION['user_type'] ?? null;
        
        // Destroy session
        session_unset();
        session_destroy();
        
        return $user_type;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    }

    /**
     * Check if user is siswa
     */
    public function isSiswa() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'siswa';
    }

    /**
     * Redirect if not authorized as Admin
     */
    public function restrictToAdmin()
    {
        if (!$this->isAdmin()) {
            header("Location: login-admin.php"); // Path to your login page
            exit();
        }
    }

    /**
     * Redirect if not authorized as Siswa
     */
    public function restrictToSiswa()
    {
        if (!$this->isSiswa()) {
            header("Location: login-siswa.php");
            exit();
        }
    }

    /**
     * Redirect if already logged in (for login/index pages)
     */
    public function redirectIfLoggedIn()
    {
        if ($this->isLoggedIn()) {
            if ($this->getUserType() === 'admin') {
                header("Location: dashboard-admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        }
    }
}
?>