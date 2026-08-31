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
    <title>Edit Profil Saya — Absensi</title>
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
                                <h4 class="font-weight-bold mb-0 text-dark">Edit Profil Saya</h4>
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
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial avatar-md mr-3" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);"><?= $initial ?></div>
                                    <div>
                                        <h4 class="font-weight-bold mb-0 text-dark">Form Perbarui Profil</h4>
                                        <small class="text-muted">NIP: <?= htmlspecialchars($r['id_karyawan']) ?></small>
                                    </div>
                                </div>
                                <a href="?m=karyawan&s=profil" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Batal
                                </a>
                            </div>

                            <form action="modul/karyawan/update.php" method="POST">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">NIP (NOMOR INDUK)</label>
                                        <input type="text" class="form-control bg-light" readonly value="<?= htmlspecialchars($r['id_karyawan']) ?>">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">USERNAME</label>
                                        <input type="text" class="form-control" name="username" required value="<?= htmlspecialchars($r['username']) ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">NAMA LENGKAP</label>
                                    <input type="text" class="form-control" name="nama" required value="<?= htmlspecialchars($r['nama']) ?>">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">PASSWORD BARU (KOSONGKAN JIKA TIDAK DIUBAH)</label>
                                    <input type="password" class="form-control" name="password" placeholder="Ketik password baru jika ingin mengganti password">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">TEMPAT & TANGGAL LAHIR</label>
                                        <input type="text" class="form-control" name="tmp_tgl_lahir" value="<?= htmlspecialchars($r['tmp_tgl_lahir']) ?>">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">JENIS KELAMIN</label>
                                        <select class="form-control" name="jenkel">
                                            <option value="Laki-laki" <?= ($r['jenkel'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="Perempuan" <?= ($r['jenkel'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">AGAMA</label>
                                        <select class="form-control" name="agama">
                                            <?php
                                            $agamas = ['Islam', 'Kristen', 'Katholik', 'Hindu', 'Buddha', 'KongHuCu'];
                                            foreach ($agamas as $ag) {
                                                $sel = ($r['agama'] == $ag) ? 'selected' : '';
                                                echo "<option value='$ag' $sel>$ag</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-muted small">NOMOR TELEPON</label>
                                        <input type="text" class="form-control" name="no_tel" value="<?= htmlspecialchars($r['no_tel']) ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">JABATAN</label>
                                    <input type="text" class="form-control bg-light" name="jabatan" readonly value="<?= htmlspecialchars($r['jabatan']) ?>">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">ALAMAT TINGGAL</label>
                                    <textarea class="form-control" name="alamat" rows="3"><?= htmlspecialchars($r['alamat']) ?></textarea>
                                </div>

                                <div class="mt-4 pt-2 border-top d-flex justify-content-end">
                                    <a href="?m=karyawan&s=profil" class="btn btn-secondary mr-2">Batal</a>
                                    <button type="submit" name="simpan" class="btn btn-primary font-weight-bold">
                                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
