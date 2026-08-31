<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';
$owner_nama = $_SESSION['owner_nama'] ?? 'Owner Executive';

date_default_timezone_set('Asia/Jakarta');
$today_str = date('d-m-Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Executive Dashboard — Owner Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">

    <!-- CSS -->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="../css/theme.css" rel="stylesheet" media="all">
    <link href="../css/modern-custom.css" rel="stylesheet" media="all">
    
    <style>
        .badge-owner {
            background-color: #f3e8ff !important;
            color: #7e22ce !important;
            border: 1px solid #e9d5ff !important;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 0.78rem;
        }
        .overview-item--owner {
            border-left-color: #7e22ce !important;
        }
        .avatar-owner {
            background-color: #7e22ce !important;
            color: #ffffff !important;
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a href="dashboard.php" class="logo">
                            <h3><i class="fas fa-crown mr-2" style="color:#c084fc;"></i>ABSENSI</h3>
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
                        <li class="active"><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a></li>
                        <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Laporan Kehadiran</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout Owner</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo" style="background-color: #170d2b; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <a href="dashboard.php">
                    <h3 style="color:#ffffff;"><i class="fas fa-crown mr-2" style="color:#c084fc;"></i>OWNER PORTAL</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="active">
                            <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a>
                        </li>
                        <li>
                            <a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Laporan Kehadiran</a>
                        </li>
                        <li>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout Owner</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div class="d-flex align-items-center">
                                <h4 class="font-weight-bold mb-0 text-dark mr-3">Executive Dashboard</h4>
                                <span class="badge-owner"><i class="fas fa-shield-alt mr-1"></i> Owner Role</span>
                            </div>
                            <div class="account-item clearfix">
                                <div class="content d-flex align-items-center">
                                    <div class="avatar-initial avatar-sm mr-2 avatar-owner">O</div>
                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($owner_nama) ?></span>
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
                        
                        <!-- WELCOME BANNER -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card p-4" style="background: linear-gradient(135deg, #2e1065, #4c1d95); color: white; border: none;">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <div>
                                            <h3 class="font-weight-bold text-black mb-1"><i class="fas fa-crown text-warning mr-2"></i> Selamat Datang, <?= htmlspecialchars($owner_nama) ?></h3>
                                            <p class="mb-0 text-dark" style="opacity: 0.88;">Ringkasan pengawasan kinerja presensi & data operasional perusahaan secara realtime.</p>
                                        </div>
                                        <div class="mt-3 mt-md-0">
                                            <a href="laporan.php" class="btn btn-light text-dark font-weight-bold shadow-sm">
                                                <i class="fas fa-download mr-1 text-primary"></i> Cetak Laporan Kehadiran
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STAT CARDS -->
                        <div class="row">
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--owner">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-accounts-alt" style="color:#7e22ce;"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_karyawan"))[0] ?? 0 ?></h2>
                                                <span>Total Karyawan</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c1">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-account-box" style="color:#4f46e5;"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_daftar"))[0] ?? 0 ?></h2>
                                                <span>Total Admin</span>
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
                                                <i class="zmdi zmdi-calendar-check" style="color:#16a34a;"></i>
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
                                <div class="overview-item overview-item--c4">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-file-text" style="color:#d97706;"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_keterangan"))[0] ?? 0 ?></h2>
                                                <span>Pengajuan Izin/Sakit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RECENT LOG TABLES -->
                        <div class="row mt-3">

                            <!-- LOG ABSENSI RECENT -->
                            <div class="col-md-7 mb-4">
                                <div class="card p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-0">Log Presensi Karyawan Terbaru</h5>
                                            <small class="text-muted">Riwayat absensi harian karyawan</small>
                                        </div>
                                        <a href="laporan.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>NIP</th>
                                                    <th>Nama Karyawan</th>
                                                    <th>Waktu Absen</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $q_absen = mysqli_query($koneksi, "SELECT * FROM tb_absen ORDER BY id DESC LIMIT 5");
                                                if (mysqli_num_rows($q_absen) == 0):
                                                ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">Belum ada catatan presensi.</td>
                                                </tr>
                                                <?php
                                                endif;
                                                while ($ra = mysqli_fetch_assoc($q_absen)):
                                                    $init = strtoupper(substr($ra['nama'], 0, 1));
                                                ?>
                                                <tr>
                                                    <td class="font-weight-bold text-primary"><?= htmlspecialchars($ra['id_karyawan']) ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-initial avatar-sm mr-2"><?= $init ?></div>
                                                            <span class="font-weight-bold text-dark"><?= htmlspecialchars($ra['nama']) ?></span>
                                                        </div>
                                                    </td>
                                                    <td><small><i class="far fa-clock text-muted mr-1"></i><?= htmlspecialchars($ra['waktu']) ?></small></td>
                                                    <td><span class="badge-modern badge-absen">Hadir</span></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- LOG IZIN / SAKIT RECENT -->
                            <div class="col-md-5 mb-4">
                                <div class="card p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-0">Izin / Sakit Terbaru</h5>
                                            <small class="text-muted">Pengajuan keterangan karyawan</small>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Kategori</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $q_ket = mysqli_query($koneksi, "SELECT * FROM tb_keterangan ORDER BY id DESC LIMIT 5");
                                                if (mysqli_num_rows($q_ket) == 0):
                                                ?>
                                                <tr>
                                                    <td colspan="3" class="text-center py-3 text-muted">Belum ada pengajuan izin/sakit.</td>
                                                </tr>
                                                <?php
                                                endif;
                                                while ($rk = mysqli_fetch_assoc($q_ket)):
                                                    $b_class = ($rk['keterangan'] === 'Sakit') ? 'badge-sakit' : 'badge-izin';
                                                ?>
                                                <tr>
                                                    <td class="font-weight-bold text-dark"><?= htmlspecialchars($rk['nama']) ?></td>
                                                    <td><span class="badge-modern <?= $b_class ?>"><?= htmlspecialchars($rk['keterangan']) ?></span></td>
                                                    <td><small class="text-muted"><?= htmlspecialchars(substr($rk['alasan'], 0, 30)) ?>...</small></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
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

    <!-- Scripts -->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
