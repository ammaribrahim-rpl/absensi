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
    <!-- CSS -->
    <link href="../css/font-face.css" rel="stylesheet">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../css/modern-custom.css" rel="stylesheet">

</head>

<body>
    <div class="page-wrapper">        <!-- HEADER MOBILE -->
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
                        <li class="active"><a href="admin.php"><i class="fas fa-home"></i> Beranda Admin</a></li>
                        <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE -->

        <!-- MENU SIDEBAR -->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="admin.php">
                    <h3><i class="fas fa-fingerprint mr-2"></i>ABSENSI</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                    <li class="active"><a href="admin.php"><i class="fas fa-home"></i> Beranda Admin</a></li>
                    <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                    <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR -->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP -->
            <header class="header-desktop d-none d-lg-block" style="background:#fff;border-bottom:1px solid var(--color-border);box-shadow:var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Beranda Admin</h4>
                                <small class="text-muted">Kelola Sistem Absensi</small>
                            </div>
                            <div class="account-item clearfix">
                                <div class="content d-flex align-items-center">
                                    <div class="avatar-initial avatar-sm mr-2"><?= strtoupper(substr($username, 0, 1)) ?></div>
                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($username) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP -->

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
                            <div class="col-sm-6 col-lg-6 mb-3">
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
                            <div class="col-sm-6 col-lg-6 mb-3">
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

                        <!-- QUICK ACTIONS -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="card p-4 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-initial avatar-md mr-3" style="background: var(--color-accent-light); color: var(--color-accent);">
                                            <i class="fas fa-users fa-lg"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-1">Kelola Data Karyawan</h5>
                                            <p class="text-muted small mb-0">Tambah karyawan baru, perbarui data dan atur posisi/jabatan.</p>
                                        </div>
                                    </div>
                                    <div class="mt-auto pt-2">
                                        <a href="datakaryawan.php" class="btn btn-primary btn-sm font-weight-bold">
                                            <i class="fas fa-external-link-alt mr-1"></i> Buka Data Karyawan
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card p-4 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-initial avatar-md mr-3" style="background: #eef2ff; color: #4338ca;">
                                            <i class="fas fa-briefcase fa-lg"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-1">Kelola Posisi & Jabatan</h5>
                                            <p class="text-muted small mb-0">Atur daftar jabatan dan kustomisasi ikon posisi karyawan.</p>
                                        </div>
                                    </div>
                                    <div class="mt-auto pt-2">
                                        <a href="datajabatan.php" class="btn btn-outline-primary btn-sm font-weight-bold">
                                            <i class="fas fa-briefcase mr-1"></i> Buka Data Jabatan
                                        </a>
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