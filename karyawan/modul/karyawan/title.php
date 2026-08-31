<?php 
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: login_karyawan.php');
    exit;
}
date_default_timezone_set('Asia/Jakarta');
$nama = $_SESSION['namasi'] ?? 'Karyawan';
$id_karyawan = $_SESSION['idsi'] ?? '';
$initial = strtoupper(substr($nama, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Form Pengajuan Izin / Sakit — Absensi</title>
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
                        <li class="active"><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Izin / Sakit</a></li>
                        <li><a href="index.php?m=karyawan&s=profil"><i class="fas fa-user"></i> Profil Saya</a></li>
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
                        <li class="active"><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Pengajuan Izin</a></li>
                        <li><a href="index.php?m=karyawan&s=profil"><i class="fas fa-user"></i> Profil Saya</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                <div>
                                    <h4 class="font-weight-bold mb-1 text-dark">Form Pengajuan Izin / Sakit</h4>
                                    <p class="text-muted small mb-0">Isi keterangan di bawah ini jika Anda tidak dapat hadir bekerja.</p>
                                </div>
                                <a href="index.php?m=awal" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>

                            <form action="modul/karyawan/keterangan_sv.php" method="POST">
                                <input type="hidden" name="id_karyawan" value="<?= htmlspecialchars($id_karyawan) ?>">
                                <input type="hidden" name="nama" value="<?= htmlspecialchars($nama) ?>">
                                <input type="hidden" name="waktu" value="<?= date('l, d-m-Y h:i:s a') ?>">

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">NIP KARYAWAN</label>
                                        <input type="text" class="form-control bg-light" readonly value="<?= htmlspecialchars($id_karyawan) ?>">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">NAMA LENGKAP</label>
                                        <input type="text" class="form-control bg-light" readonly value="<?= htmlspecialchars($nama) ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">KATEGORI KETERANGAN</label>
                                    <select name="keterangan" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Izin">Izin</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">ALASAN / DETAIL KETERANGAN</label>
                                    <textarea name="alasan" class="form-control" rows="4" placeholder="Tuliskan alasan lengkap tidak dapat hadir..." required></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">WAKTU PENGAJUAN</label>
                                    <input type="text" class="form-control bg-light" readonly value="<?= date('l, d F Y — H:i:s') ?> WIB">
                                </div>

                                <div class="mt-4 pt-2 border-top d-flex justify-content-end">
                                    <a href="index.php?m=awal" class="btn btn-secondary mr-2">Batal</a>
                                    <button type="submit" name="simpan" class="btn btn-primary font-weight-bold">
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
