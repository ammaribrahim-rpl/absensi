<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';
$owner_nama = $_SESSION['owner_nama'] ?? 'Owner Executive';

// Parameter Filter
$filter_kat = $_GET['kategori'] ?? 'semua';
$filter_kar = $_GET['id_karyawan'] ?? 'semua';
$filter_per = $_GET['periode'] ?? 'semua';
$cari       = trim($_GET['cari'] ?? '');

$now = time();
$cutoff = 0;
$label_periode = 'Semua Waktu';
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
}

$records = [];
$total_hadir = 0;
$total_izin  = 0;
$total_cuti  = 0;

// 1. Ambil Data Absensi (Hadir)
if ($filter_kat === 'semua' || $filter_kat === 'absen') {
    $sql_absen = "SELECT * FROM tb_absen ORDER BY id DESC";
    $q_absen = mysqli_query($koneksi, $sql_absen);
    while ($ra = mysqli_fetch_assoc($q_absen)) {
        $ts = parseWaktuToTimestamp($ra['waktu']);
        if ($cutoff > 0 && $ts < $cutoff) continue;
        if ($filter_kar !== 'semua' && $ra['id_karyawan'] !== $filter_kar) continue;
        if (!empty($cari) && (stripos($ra['nama'], $cari) === false && stripos($ra['id_karyawan'], $cari) === false)) continue;

        $total_hadir++;
        $records[] = [
            'timestamp'   => $ts,
            'id_karyawan' => $ra['id_karyawan'],
            'nama'        => $ra['nama'],
            'kategori'    => 'Hadir',
            'alasan'      => 'Presensi Masuk Harian',
            'waktu'       => $ra['waktu'],
            'bukti'       => '-'
        ];
    }
}

// 2. Ambil Data Keterangan (Izin & Cuti)
if ($filter_kat === 'semua' || $filter_kat === 'izin' || $filter_kat === 'cuti') {
    $sql_ket = "SELECT * FROM tb_keterangan ORDER BY id DESC";
    $q_ket = mysqli_query($koneksi, $sql_ket);
    while ($rk = mysqli_fetch_assoc($q_ket)) {
        $ts = parseWaktuToTimestamp($rk['waktu']);
        if ($cutoff > 0 && $ts < $cutoff) continue;
        if ($filter_kar !== 'semua' && $rk['id_karyawan'] !== $filter_kar) continue;
        if (!empty($cari) && (stripos($rk['nama'], $cari) === false && stripos($rk['id_karyawan'], $cari) === false && stripos($rk['alasan'] ?? '', $cari) === false)) continue;

        $kat = ($rk['keterangan'] === 'Cuti' || $rk['keterangan'] === 'Sakit') ? 'Cuti' : 'Izin';
        if ($filter_kat === 'izin' && $kat !== 'Izin') continue;
        if ($filter_kat === 'cuti' && $kat !== 'Cuti') continue;

        if ($kat === 'Izin') {
            $total_izin++;
        } else {
            $total_cuti++;
        }

        $records[] = [
            'timestamp'   => $ts,
            'id_karyawan' => $rk['id_karyawan'],
            'nama'        => $rk['nama'],
            'kategori'    => $kat,
            'alasan'      => $rk['alasan'] ?? '-',
            'waktu'       => $rk['waktu'],
            'bukti'       => $rk['bukti'] ?? '-'
        ];
    }
}

// Urutkan waktu terbaru
usort($records, function($a, $b) {
    return $b['timestamp'] <=> $a['timestamp'];
});

$total_semua = count($records);
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
                        <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a></li>
                        <li><a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a></li>
                        <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li class="active"><a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Kehadiran</a></li>
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
                        <li>
                            <a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a>
                        </li>
                        <li>
                            <a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a>
                        </li>
                        <li>
                            <a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a>
                        </li>
                        <li class="active">
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
                                <h4 class="font-weight-bold mb-0 text-dark">Rekap & Analisis Kehadiran Karyawan</h4>
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

                        <!-- PERIOD SELECTOR PILLS -->
                        <div class="card p-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="mb-2 mb-md-0">
                                    <span class="font-weight-bold text-muted small mr-2"><i class="fas fa-calendar-alt text-primary mr-1"></i> KURUN WAKTU:</span>
                                    <a href="laporan.php?periode=1pekan&kategori=<?= urlencode($filter_kat) ?>&id_karyawan=<?= urlencode($filter_kar) ?>&cari=<?= urlencode($cari) ?>" class="period-pill <?= $filter_per === '1pekan' ? 'active' : '' ?>">1 Pekan</a>
                                    <a href="laporan.php?periode=1bulan&kategori=<?= urlencode($filter_kat) ?>&id_karyawan=<?= urlencode($filter_kar) ?>&cari=<?= urlencode($cari) ?>" class="period-pill <?= $filter_per === '1bulan' ? 'active' : '' ?>">1 Bulan</a>
                                    <a href="laporan.php?periode=6bulan&kategori=<?= urlencode($filter_kat) ?>&id_karyawan=<?= urlencode($filter_kar) ?>&cari=<?= urlencode($cari) ?>" class="period-pill <?= $filter_per === '6bulan' ? 'active' : '' ?>">6 Bulan</a>
                                    <a href="laporan.php?periode=1tahun&kategori=<?= urlencode($filter_kat) ?>&id_karyawan=<?= urlencode($filter_kar) ?>&cari=<?= urlencode($cari) ?>" class="period-pill <?= $filter_per === '1tahun' ? 'active' : '' ?>">1 Tahun</a>
                                    <a href="laporan.php?periode=semua&kategori=<?= urlencode($filter_kat) ?>&id_karyawan=<?= urlencode($filter_kar) ?>&cari=<?= urlencode($cari) ?>" class="period-pill <?= $filter_per === 'semua' ? 'active' : '' ?>">Semua Waktu</a>
                                </div>
                                <div>
                                    <a href="export_excel.php?type=rekap&periode=<?= urlencode($filter_per) ?>&kategori=<?= urlencode($filter_kat) ?>&id_karyawan=<?= urlencode($filter_kar) ?>" class="btn btn-success font-weight-bold btn-sm mr-1">
                                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                                    </a>
                                    <a href="export_pdf.php?type=rekap&periode=<?= urlencode($filter_per) ?>&kategori=<?= urlencode($filter_kat) ?>&id_karyawan=<?= urlencode($filter_kar) ?>" target="_blank" class="btn btn-danger font-weight-bold btn-sm">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak / PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- STATISTIC COUNTER CARDS -->
                        <div class="row mb-3">
                            <div class="col-6 col-md-3 mb-2">
                                <div class="card p-3 h-100 border-left-success" style="border-left: 4px solid #16a34a;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small font-weight-bold">TOTAL HADIR</div>
                                            <h3 class="font-weight-bold text-success mb-0"><?= $total_hadir ?></h3>
                                        </div>
                                        <div class="avatar-initial avatar-md" style="background: #dcfce7; color: #16a34a;">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="card p-3 h-100 border-left-warning" style="border-left: 4px solid #d97706;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small font-weight-bold">TOTAL IZIN</div>
                                            <h3 class="font-weight-bold text-warning mb-0"><?= $total_izin ?></h3>
                                        </div>
                                        <div class="avatar-initial avatar-md" style="background: #fef3c7; color: #d97706;">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="card p-3 h-100" style="border-left: 4px solid #ea580c;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small font-weight-bold">TOTAL CUTI</div>
                                            <h3 class="font-weight-bold" style="color: #ea580c;"><?= $total_cuti ?></h3>
                                        </div>
                                        <div class="avatar-initial avatar-md" style="background: #ffedd5; color: #ea580c;">
                                            <i class="fas fa-calendar-minus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <div class="card p-3 h-100" style="border-left: 4px solid #7e22ce;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small font-weight-bold">TOTAL AKTIVITAS</div>
                                            <h3 class="font-weight-bold" style="color: #7e22ce;"><?= $total_semua ?></h3>
                                        </div>
                                        <div class="avatar-initial avatar-md" style="background: #f3e8ff; color: #7e22ce;">
                                            <i class="fas fa-chart-pie"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FILTER FORM -->
                        <div class="card p-3 mb-4">
                            <form method="GET" action="laporan.php" class="row align-items-end">
                                <input type="hidden" name="periode" value="<?= htmlspecialchars($filter_per) ?>">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="font-weight-bold text-muted small">FILTER KATEGORI</label>
                                    <select name="kategori" class="form-control">
                                        <option value="semua" <?= $filter_kat === 'semua' ? 'selected' : '' ?>>-- Semua Kategori (Hadir, Izin & Cuti) --</option>
                                        <option value="absen" <?= $filter_kat === 'absen' ? 'selected' : '' ?>>Hanya Hadir (Absensi Masuk)</option>
                                        <option value="izin" <?= $filter_kat === 'izin' ? 'selected' : '' ?>>Hanya Izin</option>
                                        <option value="cuti" <?= $filter_kat === 'cuti' ? 'selected' : '' ?>>Hanya Cuti</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <label class="font-weight-bold text-muted small">FILTER KARYAWAN</label>
                                    <select name="id_karyawan" class="form-control">
                                        <option value="semua">-- Semua Karyawan --</option>
                                        <?php
                                        $q_kars = mysqli_query($koneksi, "SELECT id_karyawan, nama FROM tb_karyawan ORDER BY nama ASC");
                                        while ($k = mysqli_fetch_assoc($q_kars)) {
                                            $sel = ($filter_kar === $k['id_karyawan']) ? 'selected' : '';
                                            echo '<option value="'.htmlspecialchars($k['id_karyawan']).'" '.$sel.'>'.htmlspecialchars($k['nama']).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <label class="font-weight-bold text-muted small">CARI KATA KUNCI</label>
                                    <input type="text" name="cari" class="form-control" placeholder="Nama / ID / Alasan..." value="<?= htmlspecialchars($cari) ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                                        <i class="fas fa-filter mr-1"></i> Terapkan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- TABEL REKAP KEHADIRAN -->
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0">Hasil Rekap Kehadiran (<?= count($records) ?> Catatan)</h5>
                                    <small class="text-muted">Kurun waktu: <strong><?= $label_periode ?></strong> | Kategori: <strong><?= strtoupper($filter_kat) ?></strong></small>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Masuk / ID</th>
                                            <th>Nama Karyawan</th>
                                            <th class="text-center">Kategori</th>
                                            <th>Keterangan / Alasan</th>
                                            <th>Waktu Dicatat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (empty($records)):
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block text-muted" style="opacity: 0.4;"></i>
                                                Tidak ada data kehadiran yang sesuai dengan filter.
                                            </td>
                                        </tr>
                                        <?php
                                        endif;
                                        $no = 1;
                                        foreach ($records as $row):
                                            $b_class = ($row['kategori'] === 'Hadir') ? 'badge-hadir' : (($row['kategori'] === 'Cuti') ? 'badge-cuti' : 'badge-izin');
                                            $ic = ($row['kategori'] === 'Hadir') ? 'fa-calendar-check' : (($row['kategori'] === 'Cuti') ? 'fa-calendar-minus' : 'fa-file-alt');
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="font-weight-bold text-primary"><?= htmlspecialchars($row['id_karyawan']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-initial avatar-sm mr-2"><?= strtoupper(substr($row['nama'], 0, 1)) ?></div>
                                                    <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-modern <?= $b_class ?>">
                                                    <i class="fas <?= $ic ?> mr-1"></i> <?= htmlspecialchars($row['kategori']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($row['alasan']) ?></td>
                                            <td class="text-muted small"><?= htmlspecialchars($row['waktu']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
