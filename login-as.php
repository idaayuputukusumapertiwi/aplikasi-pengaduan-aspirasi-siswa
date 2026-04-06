<?php
// PHP native (belum ada proses, hanya landing)
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Sekolah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/login-as.css" rel="stylesheet">
    <!-- <link href="assets/css/custom.css" rel="stylesheet"> -->
</head>

<body style="background-color: #e8edf8 !important; min-height: 100vh;">

    <div class="login-container">
        <div class="card login-card">
            <!-- Header -->
            <div class="card-header-custom">
                <div class="logo-wrapper">
                    <img src="assets/img/logosekolah2.png" alt="Logo Sekolah">
                </div>
                <h5 class="login-title">Sistem Pengaduan Siswa</h5>
                <p class="login-subtitle">Silakan pilih peran Anda untuk melanjutkan</p>
            </div>

            <!-- Body Card -->
            <div class="card-body-custom">
                <div class="role-selector">
                    <span class="role-label">Login Sebagai</span>

                    <!-- Login Admin -->
                    <a href="admin/login-admin.php" class="btn btn-login btn-admin w-100 mb-3">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin</span>
                    </a>

                    <!-- Divider -->
                    <div class="divider">
                        <span>ATAU</span>
                    </div>

                    <!-- Login Siswa -->
                    <a href="siswa/login-siswa.php" class="btn btn-login btn-siswa w-100">
                        <i class="fas fa-user-graduate"></i>
                        <span>Siswa</span>
                    </a>
                </div>

                <!-- Footer Text -->
                <div class="footer-text">
                    <!-- Butuh bantuan? <a href="#">Hubungi Admin</a>
                </div> -->
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>