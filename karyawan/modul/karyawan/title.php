<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['idsi'])) {
    header('location: login_karyawan.php');
    exit;
}
require_once __DIR__ . '/koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$nama = $_SESSION['namasi'] ?? 'Karyawan';
$id_karyawan = $_SESSION['idsi'] ?? '';
$initial = strtoupper(substr($nama, 0, 1));

// Ambil tanggal masuk dari database (fallback ke $id_karyawan jika tgl_masuk kosong di DB)
$tgl_masuk_karyawan = $id_karyawan;
if (!empty($id_karyawan)) {
    $stmt_tm = mysqli_prepare($koneksi, "SELECT tgl_masuk FROM tb_karyawan WHERE id_karyawan = ? LIMIT 1");
    if ($stmt_tm) {
        mysqli_stmt_bind_param($stmt_tm, "s", $id_karyawan);
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Form Pengajuan Izin / Cuti — Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">

    <!-- CSS -->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../../../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
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
                        <a href="index.php?m=awal" class="logo">
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
                        <li><a href="index.php?m=awal"><i class="fas fa-calendar-check"></i> Absensi</a></li>
                        <li class="active"><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Izin / Cuti</a></li>

                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="index.php?m=awal">
                    <h3><i class="fas fa-fingerprint mr-2"></i>ABSENSI</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li><a href="index.php?m=awal"><i class="fas fa-calendar-check"></i> Absensi Harian</a></li>
                        <li class="active"><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Pengajuan Izin / Cuti</a></li>

                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <header class="header-desktop d-none d-lg-block" style="background: #ffffff; border-bottom: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Pengajuan Keterangan</h4>
                            </div>
                            <div class="account-item clearfix">
                                <div class="content d-flex align-items-center">
                                    <div class="avatar-initial avatar-sm mr-2" style="background: linear-gradient(135deg, #10b981, #059669);"><?= $initial ?></div>
                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($nama) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid" style="max-width: 720px; margin: 0 auto;">

                        <div class="card p-4">
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between pb-3 mb-3 border-bottom">
                                <div class="mb-3 mb-sm-0">
                                    <h4 class="font-weight-bold mb-1 text-dark">Form Pengajuan Izin / Cuti</h4>
                                    <p class="text-muted small mb-0">Isi keterangan di bawah ini jika Anda tidak dapat hadir bekerja.</p>
                                </div>
                                <a href="index.php?m=awal" class="btn btn-sm btn-outline-secondary w-100 w-sm-auto text-center" style="white-space: nowrap;">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>

                            <form action="modul/karyawan/keterangan_sv.php" method="POST">
                                <input type="hidden" name="id_karyawan" value="<?= htmlspecialchars($id_karyawan) ?>">
                                <input type="hidden" name="nama" value="<?= htmlspecialchars($nama) ?>">
                                <input type="hidden" name="waktu" value="<?= date('l, d-m-Y h:i:s a') ?>">

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">TANGGAL MASUK</label>
                                        <input type="text" class="form-control bg-light font-weight-bold" readonly value="<?= htmlspecialchars($tgl_masuk_display) ?>">
                                        <small class="form-text text-muted">Masa Kerja: <strong style="color: #4f46e5;"><?= $masa_kerja_display ?></strong></small>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">NAMA LENGKAP</label>
                                        <input type="text" class="form-control bg-light font-weight-bold" readonly value="<?= htmlspecialchars($nama) ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">KATEGORI KETERANGAN</label>
                                    <select name="keterangan" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Cuti">Cuti</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">ALASAN / DETAIL KETERANGAN</label>
                                    <textarea name="alasan" class="form-control" rows="4" placeholder="Tuliskan alasan lengkap permohonan izin atau cuti..." required></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">WAKTU PENGAJUAN</label>
                                    <input type="text" class="form-control bg-light" readonly value="<?= date('l, d F Y — H:i:s') ?> WIB">
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row justify-content-end">
                                    <a href="index.php?m=awal" class="btn btn-secondary mb-2 mb-sm-0 mr-0 mr-sm-2 text-center w-100 w-sm-auto">Batal</a>
                                    <button type="submit" name="simpan" class="btn btn-primary font-weight-bold text-center w-100 w-sm-auto">
                                        <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan
                                    </button>
                                </div>
                            </form>
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
