<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location: ../index.php");
    exit;
}
include '../koneksi.php';
$username = $_SESSION['username'];
$cari = trim($_POST['cari'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hasil Pencarian Karyawan — Absensi</title>
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
                        <li><a href="admin.php"><i class="fas fa-home"></i> Beranda</a></li>
                        <li class="active"><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
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
                        <li><a href="admin.php"><i class="fas fa-home"></i> Beranda</a></li>
                        <li class="active"><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
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
                                <h4 class="font-weight-bold mb-0 text-dark">Hasil Pencarian Karyawan</h4>
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
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0">Hasil Pencarian untuk: "<em><?= htmlspecialchars($cari) ?></em>"</h5>
                                </div>
                                <a href="datakaryawan.php" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Semua Data
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Masa Kerja</th>
                                            <th>Nama</th>
                                            <th>Gender</th>
                                            <th>Telepon</th>
                                            <th>Jabatan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $cari_param = '%' . $cari . '%';
                                        $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_karyawan WHERE id_karyawan LIKE ? OR nama LIKE ?");
                                        mysqli_stmt_bind_param($stmt, 'ss', $cari_param, $cari_param);
                                        mysqli_stmt_execute($stmt);
                                        $query = mysqli_stmt_get_result($stmt);
                                        $no = 1;

                                        if (mysqli_num_rows($query) == 0):
                                        ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle mr-1"></i> Tidak ditemukan karyawan dengan kata kunci "<?= htmlspecialchars($cari) ?>"
                                            </td>
                                        </tr>
                                        <?php
                                        endif;

                                        while ($row = mysqli_fetch_assoc($query)) {
                                            $initial = strtoupper(substr($row['nama'], 0, 1));
                                            $color_idx = (abs(crc32($row['nama'])) % 7) + 1;
                                            $avatar_class = "avatar-c" . $color_idx;
                                            $masa_kerja = hitungMasaKerja($row['id_karyawan']);
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="font-weight-bold" style="color: #2563eb;"><?= htmlspecialchars($row['id_karyawan']) ?></td>
                                            <td><span class="badge badge-info" style="font-size: 0.85rem; padding: 5px 9px;"><?= $masa_kerja ?></span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-initial avatar-sm mr-2 <?= $avatar_class ?>"><?= $initial ?></div>
                                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($row['nama']) ?></span>
                                                </div>
                                            </td>
                                            <td><span class="badge-modern bg-light text-dark border"><?= htmlspecialchars($row['jenkel']) ?></span></td>
                                            <td><?= htmlspecialchars($row['no_tel']) ?></td>
                                            <td><span class="badge-modern badge-jabatan"><i class="<?= htmlspecialchars(getJabatanIcon($row['jabatan'])) ?> mr-1"></i><?= htmlspecialchars($row['jabatan']) ?></span></td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                <a href="karyawan_edit.php?id_karyawan=<?= urlencode($row['id_karyawan']) ?>" class="btn btn-sm btn-primary mr-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="hapus.php?id_karyawan=<?= urlencode($row['id_karyawan']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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

    <!-- Scripts -->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
