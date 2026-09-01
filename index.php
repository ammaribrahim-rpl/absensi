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
    body.landing-page {
      background: linear-gradient(135deg, #170d2b 0%, #2e1065 40%, #1e1b4b 100%) !important;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: #fff;
    }
    .landing-card {
      background: rgba(255, 255, 255, 0.07) !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      border-radius: 18px !important;
      backdrop-filter: blur(16px) !important;
      -webkit-backdrop-filter: blur(16px) !important;
      padding: 44px 36px !important;
      text-align: center;
      max-width: 410px;
      width: 100%;
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45) !important;
    }
    .btn-portal-owner {
      background: linear-gradient(135deg, #7e22ce, #6b21a8) !important;
      color: #ffffff !important;
      border: 1px solid #a855f7 !important;
      box-shadow: 0 4px 14px rgba(126, 34, 206, 0.35) !important;
    }
    .btn-portal-owner:hover {
      background: linear-gradient(135deg, #6b21a8, #581c87) !important;
      color: #ffffff !important;
    }
    .btn-portal-admin {
      background: linear-gradient(135deg, #4f46e5, #3730a3) !important;
      color: #ffffff !important;
      border: 1px solid #6366f1 !important;
      box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35) !important;
    }
    .btn-portal-admin:hover {
      background: linear-gradient(135deg, #4338ca, #312e81) !important;
      color: #ffffff !important;
    }
    .btn-portal-karyawan {
      background: #ffffff !important;
      color: #1e1b4b !important;
      border: 1px solid #ffffff !important;
      box-shadow: 0 4px 14px rgba(255, 255, 255, 0.2) !important;
    }
    .btn-portal-karyawan:hover {
      background: #f3e8ff !important;
      color: #6b21a8 !important;
    }
  </style>
</head>
<body class="landing-page">

  <div class="landing-card">
    <div style="font-size: 2.6rem; color: #818cf8; margin-bottom: 12px;">
      <i class="fas fa-fingerprint"></i>
    </div>
    <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: 0.05em; color: #ffffff; margin-bottom: 4px;">ABSENSI</h1>
    <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.88rem; margin-bottom: 32px;">Sistem Manajemen Presensi Terintegrasi</p>

    <div style="display: flex; flex-direction: column; gap: 12px;">
      <a href="owner/login_owner.php" class="btn-portal btn-portal-owner">
        <i class="fas fa-crown mr-2" style="color: #facc15;"></i> Login Owner
      </a>
      <a href="login.php" class="btn-portal btn-portal-admin">
        <i class="fas fa-user-shield mr-2" style="color: #a5b4fc;"></i> Login Administrator
      </a>
      <a href="karyawan/login_karyawan.php" class="btn-portal btn-portal-karyawan">
        <i class="fas fa-users mr-2" style="color: #7e22ce;"></i> Login Karyawan
      </a>
    </div>
  </div>

  <script src="vendor/jquery-3.2.1.min.js"></script>
  <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
</body>
</html>