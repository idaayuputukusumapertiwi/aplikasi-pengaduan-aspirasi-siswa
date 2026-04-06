<?php
/**
 * ================================================
 * Aplikasi Pengaduan Siswa - SMK KUSUMA JAYA
 * ================================================
 * File    : database.php
 * Modul   : Koneksi Database
 * Fungsi  : Mengelola koneksi ke database MySQL
 *           menggunakan class Database berbasis OOP
 * ================================================
 */

// class database
class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $db = "pengaduan_siswa";
    private $connection;

    // method untuk membuat koneksi
    public function koneksi(){
        $this->connection = new mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->db
        );
        //cek koneksi
        if($this->connection->connect_error){
            die("Koneksi ke database gagal!:" .$this->connection->connect_error);
        }
        return $this->connection;
    }
}
?>