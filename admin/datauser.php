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
    <title>Data User Admin — Absensi</title>
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
                        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Beranda</a></li>
                        <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li class="active"><a href="datauser.php"><i class="fas fa-user-shield"></i> Data User</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="data_absen.php"><i class="fas fa-calendar-check"></i> Data Absen</a></li>
                        <li><a href="data_keterangan.php"><i class="fas fa-file-medical"></i> Data Keterangan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>

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
                        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Beranda</a></li>
                        <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li class="active"><a href="datauser.php"><i class="fas fa-user-shield"></i> Data User</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="data_absen.php"><i class="fas fa-calendar-check"></i> Data Absen</a></li>
                        <li><a href="data_keterangan.php"><i class="fas fa-file-medical"></i> Data Keterangan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                                <h4 class="font-weight-bold mb-0 text-dark">Kelola Akun Administrator</h4>
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

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- FORM TAMBAH ADMIN -->
                            <div class="col-md-5 mb-4">
                                <div class="card p-4">
                                    <h5 class="font-weight-bold text-dark mb-3">
                                        <i class="fas fa-user-shield text-primary mr-2"></i> Tambah Admin Baru
                                    </h5>
                                    <form action="admin_save.php" method="POST">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted small">USERNAME</label>
                                            <input type="text" class="form-control" name="username" placeholder="Username admin" required autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted small">PASSWORD</label>
                                            <input type="password" class="form-control" name="password" placeholder="Password admin" required autocomplete="new-password">
                                        </div>
                                        <button type="submit" name="simpan" class="btn btn-primary btn-block font-weight-bold mt-3">
                                            <i class="fas fa-save mr-1"></i> Simpan Admin
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- TABEL DAFTAR ADMIN -->
                            <div class="col-md-7">
                                <div class="card p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="font-weight-bold text-dark mb-0">Daftar Akun Admin</h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Username</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $sql = "SELECT id, username FROM tb_daftar ORDER BY id ASC";
                                                $query = mysqli_query($koneksi, $sql);
                                                $no = 1;
                                                while ($row = mysqli_fetch_assoc($query)) {
                                                    $initial = strtoupper(substr($row['username'], 0, 1));
                                                    $is_current = ($row['username'] === $username);
                                                ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-initial avatar-sm mr-2"><?= $initial ?></div>
                                                            <span class="font-weight-bold text-dark"><?= htmlspecialchars($row['username']) ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($is_current): ?>
                                                            <span class="badge-modern badge-absen">Aktif (Anda)</span>
                                                        <?php else: ?>
                                                            <span class="badge-modern bg-light text-muted border">Admin</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (!$is_current): ?>
                                                            <a href="admin_hapus.php?id=<?= urlencode($row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus akun admin <?= htmlspecialchars(addslashes($row['username'])) ?>?');">
                                                                <i class="fas fa-trash mr-1"></i> Hapus
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted small"><em>Akun Utama</em></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
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
