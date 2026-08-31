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
    <title>Data Keterangan — Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">

    <!-- CSS -->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
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
                        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Beranda</a></li>
                        <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datauser.php"><i class="fas fa-user-shield"></i> Data User</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="data_absen.php"><i class="fas fa-calendar-check"></i> Data Absen</a></li>
                        <li class="active"><a href="data_keterangan.php"><i class="fas fa-file-medical"></i> Data Keterangan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>

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
                        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Beranda</a></li>
                        <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datauser.php"><i class="fas fa-user-shield"></i> Data User</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="data_absen.php"><i class="fas fa-calendar-check"></i> Data Absen</a></li>
                        <li class="active"><a href="data_keterangan.php"><i class="fas fa-file-medical"></i> Data Keterangan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <header class="header-desktop" style="background: #ffffff; border-bottom: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Data Pengajuan Izin & Sakit</h4>
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

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0">Daftar Pengajuan Keterangan</h5>
                                    <small class="text-muted">Riwayat izin dan sakit yang diajukan oleh karyawan</small>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>NIP</th>
                                            <th>Nama Karyawan</th>
                                            <th>Kategori</th>
                                            <th>Alasan / Keterangan</th>
                                            <th>Waktu Pengajuan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php include 'paging_ket.php'; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PAGINATION -->
                            <div class="mt-3 d-flex justify-content-center">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?halaman=<?= $previous ?>">Prev</a>
                                    </li>
                                    <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                                        <li class="page-item <?= ($halaman == $x) ? 'active' : '' ?>">
                                            <a class="page-link" href="?halaman=<?= $x ?>"><?= $x ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?halaman=<?= $next ?>">Next</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
