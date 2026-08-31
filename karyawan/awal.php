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
    <title>Portal Absensi Karyawan — Absensi</title>
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
                        <li class="active"><a href="index.php?m=awal"><i class="fas fa-calendar-check"></i> Absensi</a></li>
                        <li><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Izin / Sakit</a></li>
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
                        <li class="active"><a href="index.php?m=awal"><i class="fas fa-calendar-check"></i> Absensi Harian</a></li>
                        <li><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Pengajuan Izin</a></li>
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
                                <h4 class="font-weight-bold mb-0 text-dark">Presensi Karyawan</h4>
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
                    <div class="container-fluid" style="max-width: 800px; margin: 0 auto;">

                        <!-- DIGITAL CLOCK & CHECK-IN CARD -->
                        <div class="digital-clock-container mb-4">
                            <div class="clock-greeting">Selamat Datang,</div>
                            <div class="clock-name"><?= htmlspecialchars($nama) ?></div>
                            
                            <div class="clock-time" id="realtimeClock">--:--:--</div>
                            <div class="clock-date" id="realtimeDate"><?= date('l, d F Y') ?></div>

                            <form action="dt_absen_sv.php" method="POST" id="formAbsen">
                                <input type="hidden" name="id_karyawan" value="<?= htmlspecialchars($id_karyawan) ?>">
                                <input type="hidden" name="nama" value="<?= htmlspecialchars($nama) ?>">
                                <input type="hidden" name="waktu" id="inputWaktu" value="<?= date('l, d-m-Y h:i:s a') ?>">
                                
                                <button type="submit" name="simpan" class="clock-btn-absen">
                                    <i class="fas fa-fingerprint"></i> KLIK UNTUK ABSEN SEKARANG
                                </button>
                            </form>
                        </div>

                        <!-- CARD PENGAJUAN IZIN / SAKIT -->
                        <div class="card p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial avatar-md mr-3" style="background: #fffbeb; color: #d97706;">
                                        <i class="fas fa-file-medical"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-weight-bold text-dark mb-1">Berhalangan Hadir Hari Ini?</h5>
                                        <p class="text-muted mb-0 small">Ajukan izin kerja atau surat keterangan sakit tanpa ribet.</p>
                                    </div>
                                </div>
                                <a href="index.php?m=karyawan&s=title" class="btn btn-outline-warning font-weight-bold">
                                    Ajukan Izin <i class="fas fa-arrow-right ml-1"></i>
                                </a>
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

    <script>
    // Realtime digital clock update
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const timeString = `${hours}:${minutes}:${seconds} WIB`;
        document.getElementById('realtimeClock').textContent = timeString;

        // Update hidden field
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const dayName = days[now.getDay()];
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const ampm = now.getHours() >= 12 ? 'pm' : 'am';
        
        const formattedDate = `${dayName}, ${day}-${month}-${year} ${hours}:${minutes}:${seconds} ${ampm}`;
        const inputWaktu = document.getElementById('inputWaktu');
        if (inputWaktu) inputWaktu.value = formattedDate;
    }
    setInterval(updateClock, 1000);
    updateClock();
    </script>
</body>
</html>
