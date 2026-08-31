<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location: ../index.php");
    exit;
}
include '../koneksi.php';
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Data Karyawan — Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">

    <!-- Fontfaces CSS-->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
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
                        <a href="admin.php" class="logo">
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
                        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i>Beranda</a></li>
                        <li class="active"><a href="datakaryawan.php"><i class="fas fa-users"></i>Data Karyawan</a></li>
                        <li><a href="datauser.php"><i class="fas fa-user-shield"></i>Data User</a></li>
                        <li><a href="datajabatan.php"><i class="far fa-check-square"></i>Data Jabatan</a></li>
                        <li><a href="data_absen.php"><i class="fas fa-calendar-alt"></i>Data Absen</a></li>
                        <li><a href="data_keterangan.php"><i class="fas fa-table"></i>Data Keterangan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="admin.php">
                    <h3><i class="fas fa-fingerprint mr-2"></i>ABSENSI</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i>Beranda</a></li>
                        <li class="active"><a href="datakaryawan.php"><i class="fas fa-users"></i>Data Karyawan</a></li>
                        <li><a href="datauser.php"><i class="fas fa-user-shield"></i>Data User</a></li>
                        <li><a href="datajabatan.php"><i class="far fa-check-square"></i>Data Jabatan</a></li>
                        <li><a href="data_absen.php"><i class="fas fa-calendar-alt"></i>Data Absen</a></li>
                        <li><a href="data_keterangan.php"><i class="fas fa-table"></i>Data Keterangan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <h4>Kelola Data Karyawan</h4>
                            <div class="header-button">
                                <div class="account-wrap">
                                    <div class="account-item clearfix">
                                        <div class="content d-flex align-items-center">
                                            <div class="avatar-initial avatar-sm mr-2"><?= strtoupper(substr($username, 0, 1)) ?></div>
                                            <span class="font-weight-bold text-dark"><?= htmlspecialchars($username) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">

                        <!-- FORM TAMBAH KARYAWAN -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive table--no-card m-b-30">
                                    <form action="dt_karyawan_sv.php" method="post">
                                        <table class="table table-borderless table-striped table-earning">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Form Tambah Karyawan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="width: 25%;">NIP</td>
                                                    <td><input type="text" class="form-control" name="id_karyawan" required autocomplete="off"></td>
                                                </tr>
                                                <tr>
                                                    <td>Username</td>
                                                    <td><input type="text" class="form-control" name="username" required autocomplete="off"></td>
                                                </tr>
                                                <tr>
                                                    <td>Password</td>
                                                    <td><input type="password" class="form-control" name="password" required autocomplete="off"></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama Lengkap</td>
                                                    <td><input type="text" class="form-control" name="nama" required autocomplete="off"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tempat & Tanggal Lahir</td>
                                                    <td><input type="text" class="form-control" name="tmp_tgl_lahir" autocomplete="off" placeholder="Contoh: Jakarta / 15-05-1995"></td>
                                                </tr>
                                                <tr>
                                                    <td>Jenis Kelamin</td>
                                                    <td>
                                                        <select class="form-control" name="jenkel">
                                                            <option value="Laki-laki">Laki-laki</option>
                                                            <option value="Perempuan">Perempuan</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Agama</td>
                                                    <td>
                                                        <select class="form-control" name="agama">
                                                            <option value="Islam">Islam</option>
                                                            <option value="Kristen">Kristen</option>
                                                            <option value="Katholik">Katholik</option>
                                                            <option value="Hindu">Hindu</option>
                                                            <option value="Buddha">Buddha</option>
                                                            <option value="KongHuCu">KongHuCu</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat</td>
                                                    <td><textarea class="form-control" name="alamat" rows="2"></textarea></td>
                                                </tr>
                                                <tr>
                                                    <td>Nomor Telepon</td>
                                                    <td><input type="text" class="form-control" name="no_tel" autocomplete="off"></td>
                                                </tr>
                                                <tr>
                                                    <td>Jabatan</td>
                                                    <td>
                                                        <select class="form-control" name="jabatan">
                                                            <?php 
                                                            $sql_j = mysqli_query($koneksi, "SELECT * FROM tb_jabatan ORDER BY jabatan ASC");
                                                            while ($dj = mysqli_fetch_assoc($sql_j)) {
                                                                echo "<option value='".htmlspecialchars($dj['jabatan'])."'>".htmlspecialchars($dj['jabatan'])."</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <button type="submit" name="simpan" class="btn btn-primary mr-2">Simpan Data</button>
                                                        <button type="reset" class="btn btn-danger">Batal</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL DATA KARYAWAN -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="title-5 mb-0">Daftar Data Karyawan</h4>
                                    <form action="prospenkar.php" method="POST" class="form-inline">
                                        <div class="input-group">
                                            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari NIP / Nama..." required>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="table-responsive table--no-card m-b-30">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead>
                                            <tr>
                                                <th>NIP</th>
                                                <th>Nama</th>
                                                <th>TTL</th>
                                                <th>Gender</th>
                                                <th>Agama</th>
                                                <th>Alamat</th>
                                                <th>Telepon</th>
                                                <th>Jabatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php include 'paging.php'; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- PAGINATION -->
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?halaman=<?= $previous ?>">Previous</a>
                                    </li>
                                    <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                                        <li class="page-item <?= ($halaman == $x) ? 'active' : '' ?>">
                                            <a class="page-link" href="?halaman=<?= $x ?>"><?= $x ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?halaman=<?= $next ?>">Next</a>
                                    </li>
                                </ul>

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
