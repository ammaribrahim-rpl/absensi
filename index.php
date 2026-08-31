<?php
session_start();
include("koneksi.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Absensi — Portal Login System</title>
  <link rel="icon" href="img/Fevicon.png" type="image/png">
  <link href="vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
  <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
  <link href="css/modern-custom.css" rel="stylesheet" media="all">
  <style>
    .btn-portal-owner {
      background-color: #7e22ce !important;
      color: #ffffff !important;
      border: 1px solid #9333ea !important;
    }
    .btn-portal-owner:hover {
      background-color: #6b21a8 !important;
      color: #ffffff !important;
    }
  </style>
</head>
<body class="landing-page">

  <div class="landing-card" style="max-width: 400px;">
    <div style="font-size:2rem;color:#facc15;margin-bottom:8px;">
      <i class="fas fa-fingerprint"></i>
    </div>
    <h1>ABSENSI</h1>
    <p>Sistem Manajemen Presensi Karyawan</p>

    <div style="display:flex;flex-direction:column;gap:12px;">
      <a href="owner/login_owner.php" class="btn-portal btn-portal-owner">
        <i class="fas fa-crown mr-2" style="color:#facc15;"></i> Login Owner
      </a>
      <a href="login.php" class="btn-portal btn-portal-admin">
        <i class="fas fa-user-shield mr-2"></i> Login Administrator
      </a>
      <a href="karyawan/login_karyawan.php" class="btn-portal btn-portal-karyawan">
        <i class="fas fa-users mr-2" style="color:var(--color-accent);"></i> Login Karyawan
      </a>
    </div>
  </div>

  <script src="vendor/jquery-3.2.1.min.js"></script>
  <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
</body>
</html>