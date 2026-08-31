<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';
$owner_nama = $_SESSION['owner_nama'] ?? 'Owner Executive';

// Handle Hapus Absen
if (isset($_GET['hapus_absen']) && is_numeric($_GET['hapus_absen'])) {
    $del_id = (int) $_GET['hapus_absen'];
    $stmt_del = mysqli_prepare($koneksi, "DELETE FROM tb_absen WHERE id=?");
    mysqli_stmt_bind_param($stmt_del, 'i', $del_id);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
    $_SESSION['msg'] = "Data absensi berhasil dihapus.";
    $params = $_GET; unset($params['hapus_absen']);
    header("Location: laporan.php?" . http_build_query($params));
    exit;
}
if (isset($_GET['hapus_ket']) && is_numeric($_GET['hapus_ket'])) {
    $del_id = (int) $_GET['hapus_ket'];
    $stmt_del = mysqli_prepare($koneksi, "DELETE FROM tb_keterangan WHERE id=?");
    mysqli_stmt_bind_param($stmt_del, 'i', $del_id);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
    $_SESSION['msg'] = "Data izin/cuti berhasil dihapus.";
    $params = $_GET; unset($params['hapus_ket']);
    header("Location: laporan.php?" . http_build_query($params));
    exit;
}

// Parameter Filter
$filter_kat = $_GET['kategori']    ?? 'semua';
$filter_kar = $_GET['id_karyawan'] ?? 'semua';
$filter_per = $_GET['periode']     ?? 'semua';
$cari       = trim($_GET['cari']   ?? '');

$now = time();
$cutoff = 0;
$label_periode = 'Semua Waktu';
if ($filter_per === '1pekan')     { $cutoff = $now - (7 * 86400);   $label_periode = '1 Pekan Terakhir'; }
elseif ($filter_per === '1bulan') { $cutoff = $now - (30 * 86400);  $label_periode = '1 Bulan Terakhir'; }
elseif ($filter_per === '6bulan') { $cutoff = $now - (180 * 86400); $label_periode = '6 Bulan Terakhir'; }
elseif ($filter_per === '1tahun') { $cutoff = $now - (365 * 86400); $label_periode = '1 Tahun Terakhir'; }

$records      = [];
$total_hadir  = 0;
$total_telat  = 0;
$total_izin   = 0;
$total_cuti   = 0;
$total_pulang = 0;

// 1. Ambil Data Absensi dari tb_absen
$kat_absen_filter = ['semua', 'absen', 'telat', 'istirahat', 'pulang'];
if (in_array($filter_kat, $kat_absen_filter)) {
    $sql_absen = "SELECT id, id_karyawan, nama, waktu,
                  COALESCE(tipe_absen, 'masuk') AS tipe_absen,
                  COALESCE(is_telat, 0) AS is_telat
                  FROM tb_absen ORDER BY id DESC";
    $q_absen = mysqli_query($koneksi, $sql_absen);
    while ($ra = mysqli_fetch_assoc($q_absen)) {
        $ts       = parseWaktuToTimestamp($ra['waktu']);
        $tipe     = $ra['tipe_absen'];
        $is_telat = (int) $ra['is_telat'];

        if ($cutoff > 0 && $ts < $cutoff) continue;
        if ($filter_kar !== 'semua' && $ra['id_karyawan'] !== $filter_kar) continue;
        if (!empty($cari) && stripos($ra['nama'], $cari) === false && stripos($ra['id_karyawan'], $cari) === false) continue;

        if ($filter_kat === 'telat'     && !$is_telat) continue;
        if ($filter_kat === 'pulang'    && $tipe !== 'pulang') continue;
        if ($filter_kat === 'istirahat' && !in_array($tipe, ['istirahat_mulai', 'istirahat_selesai'])) continue;
        if ($filter_kat === 'absen'     && $tipe !== 'masuk') continue;

        $label_kat = match($tipe) {
            'masuk'             => $is_telat ? 'Telat Masuk' : 'Hadir',
            'istirahat_mulai'   => $is_telat ? 'Istirahat (Telat)' : 'Mulai Istirahat',
            'istirahat_selesai' => $is_telat ? 'Kembali (Telat)'   : 'Kembali Istirahat',
            'pulang'            => 'Pulang',
            default             => 'Hadir',
        };

        if ($tipe === 'masuk') $total_hadir++;
        if ($is_telat)         $total_telat++;
        if ($tipe === 'pulang') $total_pulang++;

        $records[] = [
            'record_id'   => (int) $ra['id'],
            'record_type' => 'absen',
            'timestamp'   => $ts,
            'id_karyawan' => $ra['id_karyawan'],
            'nama'        => $ra['nama'],
            'kategori'    => $label_kat,
            'alasan'      => 'Presensi ' . $label_kat,
            'waktu'       => $ra['waktu'],
            'is_telat'    => $is_telat,
        ];
    }
}

// 2. Ambil Data Keterangan (Izin & Cuti)
if (in_array($filter_kat, ['semua', 'izin', 'cuti'])) {
    $q_ket = mysqli_query($koneksi, "SELECT * FROM tb_keterangan ORDER BY id DESC");
    while ($rk = mysqli_fetch_assoc($q_ket)) {
        $ts = parseWaktuToTimestamp($rk['waktu']);
        if ($cutoff > 0 && $ts < $cutoff) continue;
        if ($filter_kar !== 'semua' && $rk['id_karyawan'] !== $filter_kar) continue;
        if (!empty($cari) && stripos($rk['nama'], $cari) === false && stripos($rk['id_karyawan'], $cari) === false && stripos($rk['alasan'] ?? '', $cari) === false) continue;

        $kat = ($rk['keterangan'] === 'Cuti' || $rk['keterangan'] === 'Sakit') ? 'Cuti' : 'Izin';
        if ($filter_kat === 'izin' && $kat !== 'Izin') continue;
        if ($filter_kat === 'cuti' && $kat !== 'Cuti') continue;

        ($kat === 'Izin') ? $total_izin++ : $total_cuti++;

        $tgl_m = !empty($rk['tgl_mulai']) ? date('d/m/Y', strtotime($rk['tgl_mulai'])) : '';
        $tgl_s = !empty($rk['tgl_selesai']) ? date('d/m/Y', strtotime($rk['tgl_selesai'])) : '';
        $periode_str = '';
        if (!empty($tgl_m) && !empty($tgl_s)) {
            $periode_str = ($tgl_m === $tgl_s) ? $tgl_m : "$tgl_m s/d $tgl_s";
        }

        $records[] = [
            'record_id'   => (int) $rk['id'],
            'record_type' => 'keterangan',
            'timestamp'   => $ts,
            'id_karyawan' => $rk['id_karyawan'],
            'nama'        => $rk['nama'],
            'kategori'    => $kat,
            'alasan'      => $rk['alasan'] ?? '-',
            'waktu'       => $rk['waktu'],
            'periode'     => $periode_str,
            'is_telat'    => 0,
        ];
    }
}

usort($records, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
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
                        <li><a href="approval.php"><i class="fas fa-check-double"></i> Approval Cuti</a></li>
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
                            <a href="approval.php"><i class="fas fa-check-double"></i> Approval Cuti</a>
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

                        <!-- FLASH MESSAGE -->
                        <?php if (!empty($_SESSION['msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($_SESSION['msg']) ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        <?php unset($_SESSION['msg']); endif; ?>

                        <!-- STATISTIC COUNTER CARDS -->
                        <div class="row mb-3">
                            <div class="col-6 col-md-2 mb-2">
                                <div class="card p-3 h-100" style="border-left:4px solid #16a34a;">
                                    <div class="text-muted small font-weight-bold">HADIR</div>
                                    <h3 class="font-weight-bold text-success mb-0"><?= $total_hadir ?></h3>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <div class="card p-3 h-100" style="border-left:4px solid #dc2626;">
                                    <div class="text-muted small font-weight-bold">TELAT</div>
                                    <h3 class="font-weight-bold mb-0" style="color:#dc2626;"><?= $total_telat ?></h3>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <div class="card p-3 h-100" style="border-left:4px solid #0891b2;">
                                    <div class="text-muted small font-weight-bold">PULANG</div>
                                    <h3 class="font-weight-bold mb-0" style="color:#0891b2;"><?= $total_pulang ?></h3>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <div class="card p-3 h-100" style="border-left:4px solid #d97706;">
                                    <div class="text-muted small font-weight-bold">IZIN</div>
                                    <h3 class="font-weight-bold text-warning mb-0"><?= $total_izin ?></h3>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <div class="card p-3 h-100" style="border-left:4px solid #ea580c;">
                                    <div class="text-muted small font-weight-bold">CUTI</div>
                                    <h3 class="font-weight-bold mb-0" style="color:#ea580c;"><?= $total_cuti ?></h3>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <div class="card p-3 h-100" style="border-left:4px solid #7e22ce;">
                                    <div class="text-muted small font-weight-bold">TOTAL</div>
                                    <h3 class="font-weight-bold mb-0" style="color:#7e22ce;"><?= $total_semua ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- FILTER FORM -->
                        <div class="card p-3 mb-3">
                            <form method="GET" action="laporan.php">
                                <input type="hidden" name="periode" value="<?= htmlspecialchars($filter_per) ?>">
                                <div class="row align-items-end">
                                    <div class="col-sm-6 col-lg-3 mb-2">
                                        <label class="font-weight-bold text-muted small">FILTER KATEGORI</label>
                                        <select name="kategori" class="form-control">
                                            <option value="semua"     <?= $filter_kat==='semua'     ?'selected':'' ?>>-- Semua --</option>
                                            <option value="absen"     <?= $filter_kat==='absen'     ?'selected':'' ?>>Kehadiran Masuk</option>
                                            <option value="telat"     <?= $filter_kat==='telat'     ?'selected':'' ?>>⚠️ Telat</option>
                                            <option value="istirahat" <?= $filter_kat==='istirahat' ?'selected':'' ?>>Istirahat</option>
                                            <option value="pulang"    <?= $filter_kat==='pulang'    ?'selected':'' ?>>Pulang</option>
                                            <option value="izin"      <?= $filter_kat==='izin'      ?'selected':'' ?>>Izin</option>
                                            <option value="cuti"      <?= $filter_kat==='cuti'      ?'selected':'' ?>>Cuti</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 col-lg-3 mb-2">
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
                                    <div class="col-sm-8 col-lg-4 mb-2">
                                        <label class="font-weight-bold text-muted small">CARI</label>
                                        <input type="text" name="cari" class="form-control" placeholder="Nama / ID / Alasan..." value="<?= htmlspecialchars($cari) ?>">
                                    </div>
                                    <div class="col-sm-4 col-lg-2 mb-2">
                                        <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- TABEL REKAP KEHADIRAN -->
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0">Rekap Kehadiran (<?= count($records) ?> Catatan)</h5>
                                    <small class="text-muted">Periode: <strong><?= $label_periode ?></strong> | Filter: <strong><?= strtoupper($filter_kat) ?></strong></small>
                                </div>
                                <?php if ($filter_kat==='telat'): ?>
                                <span class="badge badge-danger" style="font-size:0.78rem;padding:6px 12px;"><i class="fas fa-exclamation-triangle mr-1"></i> Menampilkan data keterlambatan</span>
                                <?php endif; ?>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;">No</th>
                                            <th>Nama Karyawan</th>
                                            <th class="text-center">Kategori</th>
                                            <th>Keterangan</th>
                                            <th>Tanggal &amp; Waktu</th>
                                            <th class="text-center" style="width:70px;">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($records)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:0.3;"></i>
                                                Tidak ada data yang sesuai filter.
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php $no = 1; foreach ($records as $row):
                                            // Badge class
                                            $is_late = !empty($row['is_telat']);
                                            $kat     = $row['kategori'];
                                            if ($is_late)                       $b_class = 'badge-telat';
                                            elseif ($kat === 'Hadir')           $b_class = 'badge-absen';
                                            elseif (str_contains($kat,'Istirahat') || str_contains($kat,'Kembali')) $b_class = 'badge-istirahat';
                                            elseif ($kat === 'Pulang')          $b_class = 'badge-pulang';
                                            elseif ($kat === 'Cuti')            $b_class = 'badge-cuti';
                                            else                                $b_class = 'badge-izin';
                                            // Icon
                                            $ic = match(true) {
                                                str_contains($kat,'Masuk') || $kat==='Hadir' => 'fa-sign-in-alt',
                                                str_contains($kat,'Istirahat')               => 'fa-utensils',
                                                str_contains($kat,'Kembali')                 => 'fa-undo',
                                                $kat==='Pulang'                              => 'fa-sign-out-alt',
                                                $kat==='Cuti'                                => 'fa-calendar-minus',
                                                default                                      => 'fa-file-alt',
                                            };
                                            // Row styling: merah jika telat
                                            $row_style = $is_late ? 'background:#fff5f5;' : '';
                                            $name_style= $is_late ? 'color:#dc2626;font-weight:700;' : 'font-weight:600;';
                                            // Delete URL
                                            $del_url = $row['record_type']==='absen'
                                                ? 'laporan.php?hapus_absen='.$row['record_id'].'&kategori='.urlencode($filter_kat).'&id_karyawan='.urlencode($filter_kar).'&periode='.urlencode($filter_per).'&cari='.urlencode($cari)
                                                : 'laporan.php?hapus_ket='.$row['record_id'].'&kategori='.urlencode($filter_kat).'&id_karyawan='.urlencode($filter_kar).'&periode='.urlencode($filter_per).'&cari='.urlencode($cari);
                                        ?>
                                        <tr style="<?= $row_style ?>">
                                            <td class="text-muted small"><?= $no++ ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-initial avatar-sm mr-2" style="<?= $is_late ? 'background:#fee2e2;color:#dc2626;' : '' ?>">
                                                        <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <span style="<?= $name_style ?>"><?= htmlspecialchars($row['nama']) ?></span>
                                                        <?php if ($is_late): ?>
                                                        <span title="Terlambat" style="color:#dc2626;font-size:0.72rem;"> ⚠️ Telat</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-modern <?= $b_class ?>">
                                                    <i class="fas <?= $ic ?> mr-1"></i><?= htmlspecialchars($kat) ?>
                                                </span>
                                            </td>
                                            <td class="small">
                                                <?= htmlspecialchars($row['alasan']) ?>
                                                <?php if (!empty($row['periode'])): ?>
                                                    <br><span class="badge badge-light border text-dark font-weight-bold mt-1" style="font-size:0.75rem;"><i class="fas fa-calendar-alt text-primary mr-1"></i>Periode: <?= htmlspecialchars($row['periode']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted small" style="white-space:nowrap;"><?= htmlspecialchars($row['waktu']) ?></td>
                                            <td class="text-center">
                                                <a href="<?= $del_url ?>" class="btn-hapus-rekap"
                                                   onclick="return confirm('Hapus data ini? Tindakan tidak bisa dibatalkan.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <style>
                        .btn-hapus-rekap {
                            display:inline-flex;align-items:center;justify-content:center;
                            width:28px;height:28px;border-radius:6px;
                            background:#fee2e2;color:#dc2626;
                            font-size:0.75rem;text-decoration:none;
                            transition:background 0.15s;
                        }
                        .btn-hapus-rekap:hover{background:#dc2626;color:#fff;}
                        </style>

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
