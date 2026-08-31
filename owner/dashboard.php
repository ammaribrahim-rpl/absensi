<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';
$owner_nama = $_SESSION['owner_nama'] ?? 'Owner Executive';

// Parameter Filter Periode untuk Dashboard
$filter_per = $_GET['periode'] ?? '1bulan'; // default 1 bulan
$now = time();
$cutoff = 0;
$label_periode = '1 Bulan Terakhir';
if ($filter_per === '1pekan') {
    $cutoff = $now - (7 * 86400);
    $label_periode = '1 Pekan Terakhir';
} elseif ($filter_per === '1bulan') {
    $cutoff = $now - (30 * 86400);
    $label_periode = '1 Bulan Terakhir';
} elseif ($filter_per === '6bulan') {
    $cutoff = $now - (180 * 86400);
    $label_periode = '6 Bulan Terakhir';
} elseif ($filter_per === '1tahun') {
    $cutoff = $now - (365 * 86400);
    $label_periode = '1 Tahun Terakhir';
} elseif ($filter_per === 'semua') {
    $cutoff = 0;
    $label_periode = 'Semua Waktu';
}

// Hitung total statistik berdasarkan periode
$count_hadir = 0;
$count_izin  = 0;
$count_cuti  = 0;

$q_all_absen = mysqli_query($koneksi, "SELECT waktu FROM tb_absen");
while ($row = mysqli_fetch_assoc($q_all_absen)) {
    $ts = parseWaktuToTimestamp($row['waktu']);
    if ($cutoff > 0 && $ts < $cutoff) continue;
    $count_hadir++;
}

$q_all_ket = mysqli_query($koneksi, "SELECT keterangan, waktu FROM tb_keterangan");
while ($row = mysqli_fetch_assoc($q_all_ket)) {
    $ts = parseWaktuToTimestamp($row['waktu']);
    if ($cutoff > 0 && $ts < $cutoff) continue;
    $kat = ($row['keterangan'] === 'Cuti' || $row['keterangan'] === 'Sakit') ? 'Cuti' : 'Izin';
    if ($kat === 'Cuti') {
        $count_cuti++;
    } else {
        $count_izin++;
    }
}

$total_karyawan = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_karyawan"))[0] ?? 0;
$total_admin    = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_daftar"))[0] ?? 0;
$total_jabatan  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_jabatan"))[0] ?? 0;

$total_aktivitas = $count_hadir + $count_izin + $count_cuti;
$rate_hadir = ($total_aktivitas > 0) ? round(($count_hadir / $total_aktivitas) * 100, 1) : 0;
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
        .avatar-owner {
            background-color: #7e22ce !important;
            color: #ffffff !important;
        }
        .period-pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #475569;
            text-decoration: none !important;
            transition: all 0.2s;
            display: inline-block;
            margin: 2px;
        }
        .period-pill:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        .period-pill.active {
            background: #7e22ce;
            border-color: #7e22ce;
            color: #fff;
            box-shadow: 0 2px 6px rgba(126, 34, 206, 0.3);
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
                        <li><a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a></li>
                        <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Kehadiran</a></li>
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
                            <a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a>
                        </li>
                        <li>
                            <a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a>
                        </li>
                        <li>
                            <a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a>
                        </li>
                        <li>
                            <a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Kehadiran</a>
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
            <header class="header-desktop" style="background: #ffffff; border-bottom: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Executive Dashboard</h4>
                            </div>
                            <div class="account-item clearfix">
                                <div class="content d-flex align-items-center">
                                    <div class="avatar-initial avatar-sm mr-2 avatar-owner">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold text-dark"><?= htmlspecialchars($owner_nama) ?></span>
                                        <span class="badge-owner ml-2">Executive Owner</span>
                                    </div>
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

                        <!-- WELCOME & PERIOD CALCULATOR FILTER -->
                        <div class="card p-4 mb-4">
                            <div class="row align-items-center">
                                <div class="col-lg-6 mb-3 mb-lg-0">
                                    <h3 class="font-weight-bold text-dark mb-1">
                                        Selamat Datang, <?= htmlspecialchars($owner_nama) ?>!
                                    </h3>
                                    <p class="text-muted mb-0">
                                        Pantau metrik kehadiran, izin, cuti, serta kelola seluruh data karyawan dan admin.
                                    </p>
                                </div>
                                <div class="col-lg-6 text-lg-right">
                                    <div class="d-inline-block text-left">
                                        <div class="font-weight-bold text-muted small mb-1">
                                            <i class="fas fa-history text-primary mr-1"></i> HITUNG KURUN WAKTU:
                                        </div>
                                        <div>
                                            <a href="dashboard.php?periode=1pekan" class="period-pill <?= $filter_per === '1pekan' ? 'active' : '' ?>">1 Pekan</a>
                                            <a href="dashboard.php?periode=1bulan" class="period-pill <?= $filter_per === '1bulan' ? 'active' : '' ?>">1 Bulan</a>
                                            <a href="dashboard.php?periode=6bulan" class="period-pill <?= $filter_per === '6bulan' ? 'active' : '' ?>">6 Bulan</a>
                                            <a href="dashboard.php?periode=1tahun" class="period-pill <?= $filter_per === '1tahun' ? 'active' : '' ?>">1 Tahun</a>
                                            <a href="dashboard.php?periode=semua" class="period-pill <?= $filter_per === 'semua' ? 'active' : '' ?>">Semua</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KPI METRIC CARDS -->
                        <div class="row">
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c3">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-calendar-check" style="color:#16a34a;"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= $count_hadir ?></h2>
                                                <span>Total Hadir (<?= $label_periode ?>)</span>
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
                                                <i class="zmdi zmdi-file-text" style="color:#d97706;"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= $count_izin ?></h2>
                                                <span>Total Izin (<?= $label_periode ?>)</span>
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
                                                <i class="zmdi zmdi-time-restore" style="color:#ea580c;"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= $count_cuti ?></h2>
                                                <span>Total Cuti (<?= $label_periode ?>)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--owner">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-accounts-alt" style="color:#7e22ce;"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?= $total_karyawan ?></h2>
                                                <span>Total Karyawan Aktif</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QUICK MANAGEMENT SHORTCUTS -->
                        <div class="row mt-2 mb-4">
                            <div class="col-md-3 mb-2">
                                <a href="karyawan.php" class="card p-3 text-dark text-decoration-none h-100 hover-shadow transition">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial avatar-md mr-3" style="background:#eef2ff; color:#4f46e5;">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">Data Karyawan</div>
                                            <small class="text-muted">Kelola <?= $total_karyawan ?> karyawan & export</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="admin_user.php" class="card p-3 text-dark text-decoration-none h-100 hover-shadow transition">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial avatar-md mr-3" style="background:#ecfdf5; color:#059669;">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">Data Admin</div>
                                            <small class="text-muted">Kelola <?= $total_admin ?> administrator</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="jabatan.php" class="card p-3 text-dark text-decoration-none h-100 hover-shadow transition">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial avatar-md mr-3" style="background:#fef3c7; color:#d97706;">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">Data Jabatan</div>
                                            <small class="text-muted">Atur <?= $total_jabatan ?> jabatan & icon</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="laporan.php?periode=<?= urlencode($filter_per) ?>" class="card p-3 text-dark text-decoration-none h-100 hover-shadow transition">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial avatar-md mr-3" style="background:#f3e8ff; color:#7e22ce;">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">Rekap Lengkap</div>
                                            <small class="text-muted">Filter Hadir, Izin & Cuti</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- RECENT LOG TABLES -->
                        <div class="row">
                            <!-- LOG ABSENSI RECENT -->
                            <div class="col-md-7 mb-4">
                                <div class="card p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-0">Log Presensi Terbaru</h5>
                                            <small class="text-muted">Aktivitas absensi karyawan masuk</small>
                                        </div>
                                        <a href="laporan.php?kategori=absen" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
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
                                                ?>
                                                <tr>
                                                    <td class="font-weight-bold text-primary"><?= htmlspecialchars($ra['id_karyawan']) ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-initial avatar-sm mr-2"><?= strtoupper(substr($ra['nama'], 0, 1)) ?></div>
                                                            <strong><?= htmlspecialchars($ra['nama']) ?></strong>
                                                        </div>
                                                    </td>
                                                    <td class="text-muted small"><?= htmlspecialchars($ra['waktu']) ?></td>
                                                    <td><span class="badge-modern badge-hadir"><i class="fas fa-check-circle mr-1"></i> Hadir</span></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- LOG IZIN / CUTI RECENT -->
                            <div class="col-md-5 mb-4">
                                <div class="card p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="font-weight-bold text-dark mb-0">Pengajuan Izin & Cuti Terbaru</h5>
                                            <small class="text-muted">Keterangan izin dan cuti</small>
                                        </div>
                                        <a href="laporan.php?kategori=izin" class="btn btn-sm btn-outline-warning">Lihat Semua</a>
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
                                                    <td colspan="3" class="text-center py-3 text-muted">Belum ada pengajuan izin/cuti.</td>
                                                </tr>
                                                <?php
                                                endif;
                                                while ($rk = mysqli_fetch_assoc($q_ket)):
                                                    $kat = ($rk['keterangan'] === 'Cuti' || $rk['keterangan'] === 'Sakit') ? 'Cuti' : 'Izin';
                                                    $b_class = ($kat === 'Cuti') ? 'badge-cuti' : 'badge-izin';
                                                    $ic_k = ($kat === 'Cuti') ? 'fa-calendar-minus' : 'fa-file-alt';
                                                ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($rk['nama']) ?></strong></td>
                                                    <td><span class="badge-modern <?= $b_class ?>"><i class="fas <?= $ic_k ?> mr-1"></i> <?= $kat ?></span></td>
                                                    <td class="text-muted small" title="<?= htmlspecialchars($rk['alasan']) ?>">
                                                        <?= htmlspecialchars(mb_strimwidth($rk['alasan'] ?? '-', 0, 25, '...')) ?>
                                                    </td>
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
