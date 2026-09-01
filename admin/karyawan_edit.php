<?php
session_start();
require_once("../koneksi.php");
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}

$id = $_GET['id_karyawan'] ?? '';
$stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_karyawan WHERE id_karyawan = ?");
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_assoc($data);

if (!$d) {
    echo "<script>alert('Data karyawan tidak ditemukan'); window.location.href = 'datakaryawan.php';</script>";
    exit;
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ubah Data Karyawan — Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">
    <!-- CSS -->
    <link href="../css/font-face.css" rel="stylesheet">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../css/modern-custom.css" rel="stylesheet">

    <style>
        .menu-sidebar { background-color: #170d2b !important; }
        .menu-sidebar .logo { background-color: #170d2b !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; }
        .header-mobile { background: #170d2b !important; }
        .header-mobile .navbar-mobile, .header-mobile .navbar-mobile .navbar-mobile__list { background: #170d2b !important; }
    </style>
</head>

<body>
    <div class="page-wrapper">        <!-- HEADER MOBILE -->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a href="admin.php" class="logo">
                            <h3><i class="fas fa-fingerprint mr-2" style="color:#818cf8;"></i>ABSENSI</h3>
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
                        <li><a href="admin.php"><i class="fas fa-chart-line"></i> Beranda Admin</a></li>
                        <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE -->

        <!-- MENU SIDEBAR -->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo" style="background-color:#170d2b; border-bottom:1px solid rgba(255,255,255,0.08);">
                <a href="admin.php">
                    <h3 style="color:#ffffff;"><i class="fas fa-fingerprint mr-2" style="color:#818cf8;"></i>ABSENSI</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li><a href="admin.php"><i class="fas fa-chart-line"></i> Beranda Admin</a></li>
                        <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR -->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP -->
            <header class="header-desktop d-none d-lg-block" style="background:#fff;border-bottom:1px solid var(--color-border);box-shadow:var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Edit Karyawan</h4>
                                <small class="text-muted">Perbarui Data Karyawan</small>
                            </div>
                            <div class="account-item clearfix">
                                <div class="content d-flex align-items-center">
                                    <div class="avatar-initial avatar-sm mr-2"><?= strtoupper(substr($username, 0, 1)) ?></div>
                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($username) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP -->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive table--no-card m-b-30">
                                    <form action="proedit_karyawan.php" method="POST">
                                        <input type="hidden" name="id_karyawan" value="<?= htmlspecialchars($d['id_karyawan']) ?>">
                                        <table class="table table-borderless table-striped table-earning">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Form Ubah Data Karyawan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="width: 25%;">Tanggal Masuk</td>
                                                    <td>
                                                        <input type="text" class="form-control bg-light font-weight-bold" readonly value="<?= htmlspecialchars($d['id_karyawan']) ?>">
                                                        <small class="form-text text-muted">Masa Kerja saat ini: <strong style="color: #4f46e5;"><?= hitungMasaKerja($d['id_karyawan']) ?></strong></small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Username</td>
                                                    <td><input type="text" class="form-control" name="username" required value="<?= htmlspecialchars($d['username']) ?>" placeholder="Masukkan username karyawan"></td>
                                                </tr>
                                                <tr>
                                                    <td>Password Baru (Opsional)</td>
                                                    <td><input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah password"></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama Lengkap</td>
                                                    <td><input type="text" class="form-control" name="nama" required value="<?= htmlspecialchars($d['nama']) ?>" placeholder="Masukkan nama lengkap karyawan"></td>
                                                </tr>
                                                <tr>
                                                    <td>Jenis Kelamin</td>
                                                    <td>
                                                        <select class="form-control" name="jenkel">
                                                            <option value="Laki-laki" <?= ($d['jenkel'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                                            <option value="Perempuan" <?= ($d['jenkel'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Nomor Telepon</td>
                                                    <td><input type="text" class="form-control" name="no_tel" value="<?= htmlspecialchars($d['no_tel']) ?>" placeholder="Contoh: 081234567890"></td>
                                                </tr>
                                                <tr>
                                                    <td>Jabatan</td>
                                                    <td>
                                                        <select class="form-control" name="jabatan">
                                                            <?php 
                                                            $sql_jab = mysqli_query($koneksi, "SELECT * FROM tb_jabatan ORDER BY jabatan ASC");
                                                            while ($dj = mysqli_fetch_assoc($sql_jab)) {
                                                                $sel = ($dj['jabatan'] == $d['jabatan']) ? 'selected' : '';
                                                                echo "<option value='".htmlspecialchars($dj['jabatan'])."' $sel>".htmlspecialchars($dj['jabatan'])."</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <button type="submit" name="ubahdata" class="btn btn-primary mr-2">Simpan Perubahan</button>
                                                        <a href="datakaryawan.php" class="btn btn-danger">Batal</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
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

    <!-- Jquery JS-->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
