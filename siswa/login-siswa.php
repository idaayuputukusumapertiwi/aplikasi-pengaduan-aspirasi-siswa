<?php
session_start();
require_once '../models/auth.php';
$auth = new Auth();
$auth->redirectIfLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90680653-2"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag("js", new Date());
    gtag("config", "UA-90680653-2");
  </script>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Responsive Bootstrap 4 Dashboard Template" />
  <meta name="author" content="BootstrapDash" />

  <title>Log In</title>

  <link href="../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet" />
  <link href="../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet" />
  <link href="../assets/lib/typicons.font/typicons.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/azia.css" />
</head>

<body class="az-body">
  <div class="az-signin-wrapper">
    <div class="az-card-signin">

      <!-- Tombol Kembali -->
      <a href="../login-as.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>

      <!-- Logo -->
      <h1 class="az-logo text-center">
        <img class="text-center" src="../assets/img/students.jpg" width="145" />
      </h1>

      <!-- Alert Logout -->
      <?php if (isset($_GET['logout']) && $_GET['logout'] === 'berhasil'): ?>
        <div class="alert alert-danger text-center" role="alert">
          <i class="fas fa-check-circle mr-1"></i> Logout Berhasil
        </div>
      <?php endif; ?>

      <div class="az-signin-header">
        <h4>Please Log In to continue</h4>
        <!-- Alert Error -->
        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger text-center" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i>
            <?php
            if ($_GET['error'] === 'invalid') {
              echo 'NIS atau password salah!';
            } elseif ($_GET['error'] === 'empty') {
              echo 'NIS dan password harus diisi!';
            } elseif ($_GET['error'] === 'unauthorized') {
              echo 'Anda harus login terlebih dahulu!';
            }
            ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="../app/login-process.php" name="loginSiswa">
          <input type="hidden" name="login_type" value="siswa">

          <div class="form-group">
            <label>NIS</label>
            <input
              type="text"
              name="nis"
              class="form-control"
              placeholder="Masukkan NIS kamu" />
          </div>

          <div class="form-group">
            <label>Password</label>
            <input
              type="password"
              name="password"
              class="form-control"
              placeholder="Masukkan password kamu" />
          </div>

          <input type="submit" class="btn btn-az-primary btn-block" value="Log In">
        </form>
      </div>

    </div>
    <!-- az-card-signin -->
  </div>
  <!-- az-signin-wrapper -->

  <script src="../lib/jquery/jquery.min.js"></script>
  <script src="../lib/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../lib/ionicons/ionicons.js"></script>
  <script src="../js/jquery.cookie.js" type="text/javascript"></script>
  <script src="../js/azia.js"></script>
  <script>
    $(function() {
      "use strict";
    });
  </script>
</body>

</html>