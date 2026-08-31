<?php 
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: login_karyawan.php');
    exit;
}
include 'koneksi.php';
$id = $_SESSION['idsi'];
$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$r = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

$nama = $r['nama'] ?? $_SESSION['namasi'];
$initial = strtoupper(substr($nama, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Profil Saya — Absensi</title>
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
                        <li><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Izin / Sakit</a></li>
                        <li class="active"><a href="index.php?m=karyawan&s=profil"><i class="fas fa-user"></i> Profil Saya</a></li>
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
                        <li><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Pengajuan Izin</a></li>
                        <li class="active"><a href="index.php?m=karyawan&s=profil"><i class="fas fa-user"></i> Profil Saya</a></li>
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
                                <h4 class="font-weight-bold mb-0 text-dark">Profil Karyawan</h4>
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
                            <!-- PROFIL HEADER -->
                            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between pb-4 mb-4 border-bottom text-center text-sm-left">
                                <div class="d-flex flex-column flex-sm-row align-items-center mb-3 mb-sm-0">
                                    <div class="avatar-initial avatar-lg mr-sm-3 mb-2 mb-sm-0" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                        <?= $initial ?>
                                    </div>
                                    <div>
                                        <h4 class="font-weight-bold mb-1 text-dark"><?= htmlspecialchars($r['nama']) ?></h4>
                                        <span class="badge-modern badge-jabatan mr-2"><?= htmlspecialchars($r['jabatan']) ?></span>
                                        <small class="text-muted">NIP: <?= htmlspecialchars($r['id_karyawan']) ?></small>
                                    </div>
                                </div>
                                <a href="?m=karyawan&s=edit" class="btn btn-primary btn-sm font-weight-bold">
                                    <i class="fas fa-edit mr-1"></i> Edit Profil
                                </a>
                            </div>

                            <!-- DETAIL TABEL -->
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="font-weight-bold text-muted" style="width: 35%;">NIP</td>
                                            <td class="font-weight-bold text-primary"><?= htmlspecialchars($r['id_karyawan']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Username</td>
                                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($r['username']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Nama Lengkap</td>
                                            <td><?= htmlspecialchars($r['nama']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Tempat, Tgl Lahir</td>
                                            <td><?= htmlspecialchars($r['tmp_tgl_lahir']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Jenis Kelamin</td>
                                            <td><span class="badge-modern bg-light text-dark border"><?= htmlspecialchars($r['jenkel']) ?></span></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Agama</td>
                                            <td><?= htmlspecialchars($r['agama']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Nomor Telepon</td>
                                            <td><?= htmlspecialchars($r['no_tel']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Jabatan</td>
                                            <td><span class="badge-modern badge-jabatan"><?= htmlspecialchars($r['jabatan']) ?></span></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Alamat</td>
                                            <td><?= nl2br(htmlspecialchars($r['alamat'])) ?></td>
                                        </tr>
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
