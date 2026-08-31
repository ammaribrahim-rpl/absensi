<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';
$owner_nama = $_SESSION['owner_nama'] ?? 'Owner Executive';

$cari = $_GET['cari'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Rekap Laporan Kehadiran — Owner Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">

    <!-- CSS -->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
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
        .avatar-owner {
            background-color: #7e22ce !important;
            color: #ffffff !important;
        }
        @media print {
            .menu-sidebar, .header-desktop, .header-mobile, .no-print {
                display: none !important;
            }
            .page-container {
                padding-left: 0 !important;
            }
            .main-content {
                padding-top: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
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
                        <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a></li>
                        <li class="active"><a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Laporan Kehadiran</a></li>
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
                        <li>
                            <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a>
                        </li>
                        <li class="active">
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
                                <h4 class="font-weight-bold mb-0 text-dark mr-3">Rekap Laporan Kehadiran</h4>
                                <span class="badge-owner"><i class="fas fa-crown mr-1"></i> Executive View</span>
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
                        
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap no-print">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0">Laporan Rekapitulasi Presensi Karyawan</h5>
                                    <small class="text-muted">Cetak dan pantau riwayat lengkap kehadiran karyawan</small>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                                    <form action="" method="GET" class="form-inline mr-2">
                                        <div class="input-group">
                                            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari NIP / Nama..." value="<?= htmlspecialchars($cari) ?>">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                    <button onclick="window.print();" class="btn btn-sm btn-success font-weight-bold">
                                        <i class="fas fa-print mr-1"></i> Cetak Laporan
                                    </button>
                                </div>
                            </div>

                            <!-- HEADER UNTUK CETAK/PRINT -->
                            <div class="d-none d-print-block text-center mb-4">
                                <h2>REKAPITULASI PRESENSI KARYAWAN</h2>
                                <p>Sistem Informasi Absensi Karyawan — Tanggal Cetak: <?= date('d F Y') ?></p>
                                <hr>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama Karyawan</th>
                                            <th>Waktu Presensi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($cari)) {
                                            $stmt_c = mysqli_prepare($koneksi, "SELECT * FROM tb_absen WHERE id_karyawan LIKE ? OR nama LIKE ? ORDER BY id DESC");
                                            $param = "%$cari%";
                                            mysqli_stmt_bind_param($stmt_c, "ss", $param, $param);
                                            mysqli_stmt_execute($stmt_c);
                                            $res = mysqli_stmt_get_result($stmt_c);
                                        } else {
                                            $res = mysqli_query($koneksi, "SELECT * FROM tb_absen ORDER BY id DESC");
                                        }

                                        $no = 1;
                                        if (mysqli_num_rows($res) == 0):
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Data presensi tidak ditemukan.</td>
                                        </tr>
                                        <?php
                                        endif;

                                        while ($r = mysqli_fetch_assoc($res)):
                                            $init = strtoupper(substr($r['nama'], 0, 1));
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="font-weight-bold text-primary"><?= htmlspecialchars($r['id_karyawan']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-initial avatar-sm mr-2 no-print"><?= $init ?></div>
                                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($r['nama']) ?></span>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($r['waktu']) ?></td>
                                            <td><span class="badge-modern badge-absen">Hadir</span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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
