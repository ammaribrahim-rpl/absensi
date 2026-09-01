<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['idsi'])) {
    header('location: login_karyawan.php');
    exit;
}
require_once __DIR__ . '/modul/karyawan/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$nama        = $_SESSION['namasi'] ?? 'Karyawan';
$id_karyawan = $_SESSION['idsi']   ?? '';
$initial     = strtoupper(substr($nama, 0, 1));
$tanggal_hari = date('d-m-Y');

// ─── Info masa kerja & tanggal masuk ─────────────────────────────────────────
$tgl_masuk_karyawan = $id_karyawan;
if (!empty($id_karyawan)) {
    $stmt_tm = mysqli_prepare($koneksi, "SELECT tgl_masuk FROM tb_karyawan WHERE id_karyawan = ? LIMIT 1");
    if ($stmt_tm) {
        mysqli_stmt_bind_param($stmt_tm, 's', $id_karyawan);
        mysqli_stmt_execute($stmt_tm);
        $res_tm = mysqli_stmt_get_result($stmt_tm);
        if ($row_tm = mysqli_fetch_assoc($res_tm)) {
            if (!empty($row_tm['tgl_masuk'])) {
                $tgl_masuk_karyawan = $row_tm['tgl_masuk'];
            }
        }
        mysqli_stmt_close($stmt_tm);
    }
}
$tgl_masuk_display  = getFormattedTglMasuk($tgl_masuk_karyawan);
$masa_kerja_display = hitungMasaKerja($tgl_masuk_karyawan);

// ─── Status Absensi Hari Ini ──────────────────────────────────────────────────
// Ambil semua record absen hari ini berdasar tipe
$absen_hari_ini = [];
$stmt_hari = mysqli_prepare($koneksi,
    "SELECT tipe_absen, waktu, is_telat FROM tb_absen
     WHERE id_karyawan = ? AND waktu LIKE ?
     ORDER BY id ASC");
if ($stmt_hari) {
    $like_tgl = "%$tanggal_hari%";
    mysqli_stmt_bind_param($stmt_hari, 'ss', $id_karyawan, $like_tgl);
    mysqli_stmt_execute($stmt_hari);
    $res_hari = mysqli_stmt_get_result($stmt_hari);
    while ($row_h = mysqli_fetch_assoc($res_hari)) {
        $absen_hari_ini[$row_h['tipe_absen']] = $row_h;
    }
    mysqli_stmt_close($stmt_hari);
}

$sudah_masuk             = isset($absen_hari_ini['masuk']);
$sudah_istirahat_mulai   = isset($absen_hari_ini['istirahat_mulai']);
$sudah_istirahat_selesai = isset($absen_hari_ini['istirahat_selesai']);
$sudah_pulang            = isset($absen_hari_ini['pulang']);

// Hitung sisa waktu istirahat (jika sedang istirahat)
$sisa_istirahat_detik = 0;
$sedang_istirahat = $sudah_istirahat_mulai && !$sudah_istirahat_selesai;
if ($sedang_istirahat) {
    $ts_mulai = parseWaktuToTimestamp($absen_hari_ini['istirahat_mulai']['waktu']);
    $ts_batas = $ts_mulai + 3600; // +1 jam
    $sisa_istirahat_detik = max(0, $ts_batas - time());
}

// ─── Notifikasi belum dibaca ─────────────────────────────────────────────────
$notif_count = 0;
$stmt_notif = mysqli_prepare($koneksi,
    "SELECT COUNT(*) as cnt FROM tb_notifikasi WHERE id_karyawan=? AND dibaca=0");
if ($stmt_notif) {
    mysqli_stmt_bind_param($stmt_notif, 's', $id_karyawan);
    mysqli_stmt_execute($stmt_notif);
    $res_notif = mysqli_stmt_get_result($stmt_notif);
    if ($rn = mysqli_fetch_assoc($res_notif)) {
        $notif_count = (int) $rn['cnt'];
    }
    mysqli_stmt_close($stmt_notif);
}

// Tentukan tombol yang tampil
// Status: 0=tampil masuk | 1=tampil istirahat | 2=tampil kembali_istirahat | 3=tampil pulang | 4=selesai
if (!$sudah_masuk) {
    $status_absen = 0;
} elseif ($sudah_masuk && !$sudah_istirahat_mulai) {
    $status_absen = 1;
} elseif ($sudah_istirahat_mulai && !$sudah_istirahat_selesai) {
    $status_absen = 2;
} elseif ($sudah_istirahat_selesai && !$sudah_pulang) {
    $status_absen = 3;
} else {
    $status_absen = 4; // Sudah pulang
}

// Ambil pesan flash hasil absensi jika ada
$flash_absen = $_SESSION['flash_absen'] ?? null;
unset($_SESSION['flash_absen']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Portal Absensi Karyawan</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">
    <link href="../css/font-face.css" rel="stylesheet">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../css/modern-custom.css" rel="stylesheet">
    <style>
        /* ── Theme Overrides (Purple Dark Sidebar) ── */
        .menu-sidebar { background-color: #170d2b !important; }
        .menu-sidebar .logo { background-color: #170d2b !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; }
        .header-mobile { background: #170d2b !important; }
        .header-mobile .navbar-mobile, .header-mobile .navbar-mobile .navbar-mobile__list { background: #170d2b !important; }

        /* ── Absensi Button Grid ── */
        .absen-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 20px;
        }
        .btn-absen-type {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 24px;
            border: none;
            border-radius: 10px;
            font-family: var(--font-main);
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            letter-spacing: 0.03em;
            width: 100%;
        }
        .btn-absen-type:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
        .btn-absen-type:active { transform: scale(0.97); }
        .btn-absen-type i { font-size: 1.2rem; }

        .btn-masuk    { background: #4f46e5; color: #fff; box-shadow: 0 3px 10px rgba(79,70,229,0.3); }
        .btn-istirahat{ background: #d97706; color: #fff; box-shadow: 0 3px 10px rgba(217,119,6,0.3); }
        .btn-kembali  { background: #0891b2; color: #fff; box-shadow: 0 3px 10px rgba(8,145,178,0.3); }
        .btn-pulang   { background: #dc2626; color: #fff; box-shadow: 0 3px 10px rgba(220,38,38,0.3); }
        .btn-done     { background: #16a34a; color: #fff; cursor: default; }
        .btn-disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }
        .btn-disabled:hover { transform: none; box-shadow: none; }

        /* ── Timeline Status ── */
        .timeline-status {
            display: flex;
            justify-content: space-around;
            align-items: center;
            background: #f9fafb;
            border: 1px solid var(--color-border);
            border-radius: 10px;
            padding: 14px 8px;
            margin-bottom: 16px;
        }
        .ts-item { text-align: center; flex: 1; }
        .ts-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 4px;
            font-size: 0.85rem;
        }
        .ts-done  { background: #dcfce7; color: #16a34a; }
        .ts-active{ background: #4f46e5; color: #fff; }
        .ts-empty { background: #e5e7eb; color: #9ca3af; }
        .ts-label { font-size: 0.65rem; font-weight: 600; color: var(--color-muted); text-transform: uppercase; }
        .ts-time  { font-size: 0.72rem; color: var(--color-text); font-weight: 700; }
        .ts-divider { height: 2px; flex: 0.5; background: #e5e7eb; margin-bottom: 20px; }
        .ts-divider.done { background: #16a34a; }

        /* ── Countdown Istirahat ── */
        .countdown-box {
            background: linear-gradient(135deg, #0891b2, #0e7490);
            color: #fff;
            border-radius: 10px;
            padding: 14px 20px;
            text-align: center;
            margin-bottom: 12px;
        }
        .countdown-box .cd-label { font-size: 0.78rem; opacity: 0.85; margin-bottom: 4px; }
        .countdown-box .cd-time  { font-size: 2rem; font-weight: 800; letter-spacing: -0.03em; }
        .countdown-box .cd-sub   { font-size: 0.72rem; opacity: 0.75; margin-top: 2px; }

        /* ── Bell Notification ── */
        .notif-bell-wrapper {
            position: relative;
            display: inline-flex;
        }
        .notif-badge {
            position: absolute;
            top: -4px; right: -4px;
            background: #dc2626;
            color: #fff;
            font-size: 0.6rem;
            font-weight: 700;
            min-width: 16px; height: 16px;
            border-radius: 99px;
            display: flex; align-items: center; justify-content: center;
            line-height: 1;
            padding: 0 3px;
        }

        /* ── Selesai Card ── */
        .done-card {
            background: linear-gradient(135deg, #16a34a, #059669);
            color: #fff;
            border-radius: 12px;
            padding: 24px 20px;
            text-align: center;
        }
        .done-card i { font-size: 2.5rem; margin-bottom: 10px; opacity: 0.9; }
        .done-card h5 { color: #fff !important; font-size: 1.1rem; }
        .done-card p { color: rgba(255,255,255,0.8); font-size: 0.85rem; margin: 0; }
    </style>
</head>
<body>
<div class="page-wrapper">

    <!-- HEADER MOBILE -->
    <header class="header-mobile d-block d-lg-none">
        <div class="header-mobile__bar">
            <div class="container-fluid">
                <div class="header-mobile-inner">
                    <a href="index.php?m=awal" class="logo">
                        <h3><i class="fas fa-fingerprint mr-2" style="color:#818cf8;"></i>ABSENSI</h3>
                    </a>
                    <div class="d-flex align-items-center gap-2">
                        <a href="notifikasi.php" class="notif-bell-wrapper mr-3" style="color:#fff;">
                            <i class="fas fa-bell" style="font-size:1.2rem;"></i>
                            <?php if ($notif_count > 0): ?>
                            <span class="notif-badge" id="notifBadgeMobile"><?= $notif_count ?></span>
                            <?php else: ?>
                            <span class="notif-badge" id="notifBadgeMobile" style="display:none;">0</span>
                            <?php endif; ?>
                        </a>
                        <button class="hamburger" type="button">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <nav class="navbar-mobile">
            <div class="container-fluid">
                <ul class="navbar-mobile__list list-unstyled">
                    <li class="active"><a href="index.php?m=awal"><i class="fas fa-calendar-check"></i> Absensi</a></li>
                    <li><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Izin / Cuti</a></li>
                    <li><a href="notifikasi.php"><i class="fas fa-bell"></i> Notifikasi <?php if ($notif_count > 0) echo "<span class='badge badge-danger ml-1'>$notif_count</span>"; ?></a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- MENU SIDEBAR -->
    <aside class="menu-sidebar d-none d-lg-block">
        <div class="logo" style="background-color:#170d2b; border-bottom:1px solid rgba(255,255,255,0.08);">
            <a href="index.php?m=awal">
                <h3 style="color:#ffffff;"><i class="fas fa-fingerprint mr-2" style="color:#818cf8;"></i>ABSENSI</h3>
            </a>
        </div>
        <div class="menu-sidebar__content js-scrollbar1">
            <nav class="navbar-sidebar">
                <ul class="list-unstyled navbar__list">
                    <li class="active"><a href="index.php?m=awal"><i class="fas fa-calendar-check"></i> Absensi Harian</a></li>
                    <li><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Pengajuan Izin / Cuti</a></li>
                    <li>
                        <a href="notifikasi.php">
                            <i class="fas fa-bell"></i> Notifikasi
                            <?php if ($notif_count > 0): ?>
                            <span class="badge badge-danger ml-auto" style="font-size:0.68rem;"><?= $notif_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- PAGE CONTAINER -->
    <div class="page-container">
        <header class="header-desktop d-none d-lg-block" style="background:#fff;border-bottom:1px solid var(--color-border);box-shadow:var(--shadow-sm);">
            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="header-wrap">
                        <div>
                            <h4 class="font-weight-bold mb-0 text-dark">Presensi Karyawan</h4>
                        </div>
                        <div class="d-flex align-items-center">
                            <!-- Bell Notifikasi Desktop -->
                            <a href="notifikasi.php" class="notif-bell-wrapper mr-4" style="color:#4b5563;">
                                <i class="fas fa-bell" style="font-size:1.1rem;"></i>
                                <?php if ($notif_count > 0): ?>
                                <span class="notif-badge" id="notifBadgeDesktop"><?= $notif_count ?></span>
                                <?php else: ?>
                                <span class="notif-badge" id="notifBadgeDesktop" style="display:none;">0</span>
                                <?php endif; ?>
                            </a>
                            <div class="account-item clearfix">
                                <div class="content d-flex align-items-center">
                                    <div class="avatar-initial avatar-sm mr-2" style="background:linear-gradient(135deg,#10b981,#059669);"><?= $initial ?></div>
                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($nama) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="section__content section__content--p30">
                <div class="container-fluid" style="max-width:720px;margin:0 auto;">

                    <!-- CLOCK CARD -->
                    <div class="digital-clock-container mb-3 text-center">
                        <div class="clock-greeting">Selamat Datang,</div>
                        <div class="clock-name mb-1"><?= htmlspecialchars($nama) ?></div>

                        <div class="d-flex flex-column align-items-center mb-3">
                            <span class="text-muted small mb-1">
                                <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                Masuk: <strong><?= htmlspecialchars($tgl_masuk_display) ?></strong>
                            </span>
                            <span class="text-muted small">
                                <i class="fas fa-business-time text-success mr-1"></i>
                                Masa Kerja: <strong style="color:#059669;"><?= $masa_kerja_display ?></strong>
                            </span>
                        </div>

                        <div class="clock-time" id="realtimeClock">--:--:--</div>
                        <div class="clock-date"><?= date('l, d F Y') ?></div>
                    </div>

                    <!-- TIMELINE STATUS ABSEN -->
                    <div class="timeline-status mb-3">
                        <!-- Masuk -->
                        <div class="ts-item">
                            <div class="ts-icon <?= $sudah_masuk ? 'ts-done' : ($status_absen === 0 ? 'ts-active' : 'ts-empty') ?>">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="ts-label">Masuk</div>
                            <div class="ts-time"><?= $sudah_masuk ? substr($absen_hari_ini['masuk']['waktu'], -14, 8) : '--:--' ?></div>
                        </div>
                        <div class="ts-divider <?= $sudah_masuk ? 'done' : '' ?>"></div>
                        <!-- Istirahat -->
                        <div class="ts-item">
                            <div class="ts-icon <?= $sudah_istirahat_mulai ? 'ts-done' : ($status_absen === 1 ? 'ts-active' : 'ts-empty') ?>">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="ts-label">Istirahat</div>
                            <div class="ts-time"><?= $sudah_istirahat_mulai ? substr($absen_hari_ini['istirahat_mulai']['waktu'], -14, 8) : '--:--' ?></div>
                        </div>
                        <div class="ts-divider <?= $sudah_istirahat_selesai ? 'done' : '' ?>"></div>
                        <!-- Kembali -->
                        <div class="ts-item">
                            <div class="ts-icon <?= $sudah_istirahat_selesai ? 'ts-done' : ($status_absen === 2 ? 'ts-active' : 'ts-empty') ?>">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div class="ts-label">Kembali</div>
                            <div class="ts-time"><?= $sudah_istirahat_selesai ? substr($absen_hari_ini['istirahat_selesai']['waktu'], -14, 8) : '--:--' ?></div>
                        </div>
                        <div class="ts-divider <?= $sudah_pulang ? 'done' : '' ?>"></div>
                        <!-- Pulang -->
                        <div class="ts-item">
                            <div class="ts-icon <?= $sudah_pulang ? 'ts-done' : ($status_absen === 3 ? 'ts-active' : 'ts-empty') ?>">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="ts-label">Pulang</div>
                            <div class="ts-time"><?= $sudah_pulang ? substr($absen_hari_ini['pulang']['waktu'], -14, 8) : '--:--' ?></div>
                        </div>
                    </div>

                    <?php if ($status_absen === 4): ?>
                    <!-- SELESAI -->
                    <div class="done-card mb-3">
                        <i class="fas fa-check-circle"></i>
                        <h5 class="font-weight-bold mt-2 mb-1">Absensi Hari Ini Lengkap!</h5>
                        <p>Terima kasih, kamu sudah menyelesaikan semua presensi hari ini. Sampai jumpa besok! 👋</p>
                    </div>

                    <?php elseif ($status_absen === 2 && $sisa_istirahat_detik > 0): ?>
                    <!-- COUNTDOWN ISTIRAHAT -->
                    <div class="countdown-box mb-3">
                        <div class="cd-label"><i class="fas fa-utensils mr-1"></i> Waktu Istirahat Tersisa</div>
                        <div class="cd-time" id="countdownTimer">--:--</div>
                        <div class="cd-sub">Kembali sebelum waktu habis agar tidak tercatat terlambat</div>
                    </div>

                    <?php endif; ?>

                    <?php if ($status_absen < 4): ?>
                    <!-- TOMBOL ABSENSI -->
                    <form action="dt_absen_sv.php" method="POST" id="formAbsen">
                        <input type="hidden" name="id_karyawan" value="<?= htmlspecialchars($id_karyawan) ?>">
                        <input type="hidden" name="nama" value="<?= htmlspecialchars($nama) ?>">
                        <input type="hidden" name="tipe_absen" id="inputTipeAbsen" value="">
                        <div class="absen-grid">
                            <?php if ($status_absen === 0): ?>
                            <!-- TOMBOL MASUK -->
                            <button type="button" class="btn-absen-type btn-masuk" onclick="konfirmasiAbsen('masuk','Absen Masuk Kerja')">
                                <i class="fas fa-sign-in-alt"></i> ABSEN MASUK
                            </button>
                            <!-- Disabled: Istirahat & Pulang -->
                            <button type="button" class="btn-absen-type btn-disabled" disabled>
                                <i class="fas fa-utensils"></i> MULAI ISTIRAHAT
                            </button>
                            <button type="button" class="btn-absen-type btn-disabled" disabled>
                                <i class="fas fa-sign-out-alt"></i> ABSEN PULANG
                            </button>

                            <?php elseif ($status_absen === 1): ?>
                            <!-- Sudah masuk, belum istirahat -->
                            <button type="button" class="btn-absen-type btn-done" disabled>
                                <i class="fas fa-check"></i> SUDAH MASUK — <?= $sudah_masuk ? substr($absen_hari_ini['masuk']['waktu'], -14, 8) : '' ?> <?= isset($absen_hari_ini['masuk']['is_telat']) && $absen_hari_ini['masuk']['is_telat'] ? '⚠️ TELAT' : '' ?>
                            </button>
                            <button type="button" class="btn-absen-type btn-istirahat" onclick="konfirmasiAbsen('istirahat_mulai','Mulai Istirahat (1 jam)')">
                                <i class="fas fa-utensils"></i> MULAI ISTIRAHAT
                            </button>
                            <button type="button" class="btn-absen-type btn-pulang" onclick="konfirmasiAbsen('pulang','Absen Pulang Kerja')">
                                <i class="fas fa-sign-out-alt"></i> ABSEN PULANG
                            </button>

                            <?php elseif ($status_absen === 2): ?>
                            <!-- Sedang istirahat -->
                            <button type="button" class="btn-absen-type btn-done" disabled>
                                <i class="fas fa-check"></i> SUDAH MASUK
                            </button>
                            <button type="button" class="btn-absen-type btn-done" disabled>
                                <i class="fas fa-utensils"></i> ISTIRAHAT — <?= $sudah_istirahat_mulai ? substr($absen_hari_ini['istirahat_mulai']['waktu'], -14, 8) : '' ?>
                            </button>
                            <button type="button" class="btn-absen-type btn-kembali" onclick="konfirmasiAbsen('istirahat_selesai','Selesai Istirahat')">
                                <i class="fas fa-undo"></i> SELESAI ISTIRAHAT
                            </button>

                            <?php elseif ($status_absen === 3): ?>
                            <!-- Sudah kembali istirahat, belum pulang -->
                            <button type="button" class="btn-absen-type btn-done" disabled>
                                <i class="fas fa-check"></i> SUDAH MASUK
                            </button>
                            <button type="button" class="btn-absen-type btn-done" disabled>
                                <i class="fas fa-check"></i> SUDAH ISTIRAHAT <?= isset($absen_hari_ini['istirahat_selesai']['is_telat']) && $absen_hari_ini['istirahat_selesai']['is_telat'] ? '⚠️ TELAT' : '' ?>
                            </button>
                            <button type="button" class="btn-absen-type btn-pulang" onclick="konfirmasiAbsen('pulang','Absen Pulang Kerja')">
                                <i class="fas fa-sign-out-alt"></i> ABSEN PULANG
                            </button>
                            <?php endif; ?>
                        </div>
                    </form>
                    <?php endif; ?>

                    <!-- CARD PENGAJUAN IZIN -->
                    <div class="card p-3 mt-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center mb-2 mb-sm-0">
                                <div class="avatar-initial avatar-md mr-3" style="background:#fffbeb;color:#d97706;">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0" style="font-size:0.95rem;">Berhalangan Hadir?</h5>
                                    <p class="text-muted mb-0 small">Ajukan izin atau cuti tanpa ribet.</p>
                                </div>
                            </div>
                            <a href="index.php?m=karyawan&s=title" class="btn btn-outline-warning font-weight-bold btn-sm" style="white-space:nowrap;">
                                Ajukan Izin / Cuti <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- CARD KONTROL AUDIO & TEST SUARA -->
                    <div class="card p-3 mt-3" style="border: 1px dashed var(--color-border); background: #fdfdfd;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center mb-2 mb-sm-0">
                                <div class="avatar-initial avatar-sm mr-2" style="background:#eef2ff;color:#4f46e5;">
                                    <i class="fas fa-volume-up"></i>
                                </div>
                                <div>
                                    <span class="font-weight-bold text-dark d-block" style="font-size:0.85rem;">Notifikasi Suara Aktif</span>
                                    <span class="text-muted small">Folder: <code>absensi/audio/</code></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold" onclick="AbsenAudio.test('terlambat')" title="Tes suara saat terlambat">
                                    <i class="fas fa-play mr-1"></i> Tes "Terlambat"
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning font-weight-bold" onclick="AbsenAudio.test('sisa_5menit')" title="Tes suara saat sisa 5 menit">
                                    <i class="fas fa-play mr-1"></i> Tes "5 Menit"
                                </button>
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
<script src="../vendor/sweetalert/sweetalert.min.js"></script>
<script src="../js/main.js"></script>
<script src="../js/audio_notif.js"></script>

<script>
// ── Flash Absensi & Trigger Suara Terlambat ──────────────────────────────────
<?php if ($flash_absen): ?>
(function() {
    const flash = <?= json_encode($flash_absen) ?>;
    if (flash.success) {
        if (flash.is_telat === 1) {
            // Putar audio terlambat
            AbsenAudio.playTerlambat();
            swal({
                title: "⚠️ Tercatat Terlambat",
                text: `${flash.label} berhasil dicatat pada ${flash.waktu}.\n${flash.telat_msg ? flash.telat_msg.trim() : 'Kamu tercatat melebihi batas waktu.'}`,
                icon: "warning",
                button: "Mengerti"
            });
        } else {
            swal({
                title: "Absensi Berhasil",
                text: `${flash.label} berhasil dicatat pada ${flash.waktu}.`,
                icon: "success",
                button: "Tutup"
            });
        }
    } else {
        swal({
            title: "Gagal",
            text: flash.message || "Gagal menyimpan absensi.",
            icon: "error",
            button: "Tutup"
        });
    }
})();
<?php endif; ?>

// ── Realtime Clock ────────────────────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    document.getElementById('realtimeClock').textContent =
        `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())} WIB`;
}
setInterval(updateClock, 1000);
updateClock();

// ── Countdown Istirahat & Trigger Audio Peringatan ────────────────────────────
<?php if ($status_absen === 2 && $sisa_istirahat_detik > 0): ?>
let sisaDetik = <?= (int) $sisa_istirahat_detik ?>;
let played5MinWarning = <?= ($sisa_istirahat_detik <= 300) ? 'true' : 'false' ?>;
let playedOvertimeWarning = false;
const cdEl = document.getElementById('countdownTimer');

function updateCountdown() {
    if (!cdEl) return;
    if (sisaDetik <= 0) {
        cdEl.textContent = '00:00 ⚠️';
        cdEl.style.color = '#fca5a5';
        if (!playedOvertimeWarning) {
            playedOvertimeWarning = true;
            AbsenAudio.playTerlambat();
            swal({
                title: "⏰ Waktu Istirahat Habis!",
                text: "Durasi istirahat 1 jam telah terlewati. Segera lakukan absensi 'Selesai Istirahat' agar tidak tercatat semakin telat.",
                icon: "warning",
                button: "Absen Sekarang"
            });
        }
        return;
    }

    // Trigger suara saat tersisa tepat 5 menit (300 detik) atau pertama kali masuk < 5 menit
    if (sisaDetik === 300 || (sisaDetik < 300 && !played5MinWarning)) {
        played5MinWarning = true;
        AbsenAudio.playSisa5Menit();
        swal({
            title: "⏰ Waktu Tersisa 5 Menit!",
            text: "Waktu istirahat kamu tinggal 5 menit lagi. Harap bersiap kembali bekerja dan lakukan absensi tepat waktu.",
            icon: "info",
            button: "Mengerti"
        });
    }

    const m = String(Math.floor(sisaDetik / 60)).padStart(2, '0');
    const s = String(sisaDetik % 60).padStart(2, '0');
    cdEl.textContent = `${m}:${s}`;
    sisaDetik--;
}
setInterval(updateCountdown, 1000);
updateCountdown();
<?php endif; ?>

// ── Konfirmasi Absen ──────────────────────────────────────────────────────────
function konfirmasiAbsen(tipe, label) {
    if (!confirm(`Konfirmasi: ${label}?\nWaktu: ${new Date().toLocaleTimeString('id-ID')}`)) return;
    document.getElementById('inputTipeAbsen').value = tipe;
    document.getElementById('formAbsen').submit();
}

// ── Polling Notifikasi (setiap 30 detik) ─────────────────────────────────────
let lastNotifCount = <?= (int) $notif_count ?>;
function pollNotifikasi() {
    fetch('api_notif.php')
        .then(r => r.json())
        .then(data => {
            const cnt = data.count || 0;
            if (cnt > lastNotifCount && data.items && data.items.length > 0) {
                const newest = data.items[0];
                if (newest.tipe === 'telat_masuk' || newest.tipe === 'telat_istirahat') {
                    AbsenAudio.playTerlambat();
                }
            }
            lastNotifCount = cnt;
            ['notifBadgeMobile', 'notifBadgeDesktop'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                if (cnt > 0) {
                    el.textContent = cnt;
                    el.style.display = 'flex';
                } else {
                    el.style.display = 'none';
                }
            });
        })
        .catch(() => {});
}
setInterval(pollNotifikasi, 30000);
</script>
</body>
</html>
