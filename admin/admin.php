<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location: ../index.php");
    exit;
}
include '../koneksi.php';
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Beranda Admin — Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">

    <!-- Fontfaces CSS-->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="../css/theme.css" rel="stylesheet" media="all">
    <link href="../css/modern-custom.css" rel="stylesheet" media="all">
</head>

<body>
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a href="admin.php" class="logo">
                            <h3><i class="fas fa-fingerprint mr-2"></i>ABSENSI</h3>
                        </a>
                        <button class="hamburger" type="button">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
            <nav class="navbar-mobile">
                <div class="container-fluid">
                    <ul class="navbar-mobile__list list-unstyled">
                        <li class="active">
                            <a href="admin.php"><i class="fas fa-tachometer-alt"></i>Beranda</a>
                        </li>
                        <li>
                            <a href="datakaryawan.php"><i class="fas fa-users"></i>Data Karyawan</a>
                        </li>
                        <li>
                            <a href="datauser.php"><i class="fas fa-user-shield"></i>Data User</a>
                        </li>
                        <li>
                            <a href="datajabatan.php"><i class="far fa-check-square"></i>Data Jabatan</a>
                        </li>
                        <li>
                            <a href="data_absen.php"><i class="fas fa-calendar-alt"></i>Data Absen</a>
                        </li>
                        <li>
                            <a href="data_keterangan.php"><i class="fas fa-table"></i>Data Keterangan</a>
                        </li>
                        <li>
                            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="admin.php">
                    <h3><i class="fas fa-fingerprint mr-2"></i>ABSENSI</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="active">
                            <a href="admin.php"><i class="fas fa-tachometer-alt"></i>Beranda</a>
                        </li>
                        <li>
                            <a href="datakaryawan.php"><i class="fas fa-users"></i>Data Karyawan</a>
                        </li>
                        <li>
                            <a href="datauser.php"><i class="fas fa-user-shield"></i>Data User</a>
                        </li>
                        <li>
                            <a href="datajabatan.php"><i class="far fa-check-square"></i>Data Jabatan</a>
                        </li>
                        <li>
                            <a href="data_absen.php"><i class="fas fa-calendar-alt"></i>Data Absen</a>
                        </li>
                        <li>
                            <a href="data_keterangan.php"><i class="fas fa-table"></i>Data Keterangan</a>
                        </li>
                        <li>
                            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <h4>Dashboard Administrator</h4>
                            <div class="header-button">
                                <div class="account-wrap">
                                    <div class="account-item clearfix">
                                        <div class="content d-flex align-items-center">
                                            <div class="avatar-initial avatar-sm mr-2"><?= strtoupper(substr($username, 0, 1)) ?></div>
                                            <span class="font-weight-bold text-dark"><?= htmlspecialchars($username) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">Selamat Datang, <?= htmlspecialchars($username) ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="row m-t-25">
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c1">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-account-o"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_karyawan"))[0] ?? 0 ?></h2>
                                                <span>Data Karyawan</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c2">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-calendar-check"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_absen"))[0] ?? 0 ?></h2>
                                                <span>Total Presensi</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c3">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-file-text"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_keterangan"))[0] ?? 0 ?></h2>
                                                <span>Izin / Sakit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c4">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-case"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_jabatan"))[0] ?? 0 ?></h2>
                                                <span>Data Jabatan</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT-->
        </div>
        <!-- END PAGE CONTAINER-->
    </div>

    <!-- Jquery JS-->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>