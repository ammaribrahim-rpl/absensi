<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';
$owner_nama     = $_SESSION['owner_nama'] ?? 'Owner Executive';
$owner_username = $_SESSION['owner_username'];

$pesan_sukses = '';
$pesan_error  = '';

if (isset($_POST['simpan_password'])) {
    $pass_lama  = trim($_POST['pass_lama'] ?? '');
    $pass_baru  = trim($_POST['pass_baru'] ?? '');
    $konfirmasi = trim($_POST['konfirmasi_pass'] ?? '');

    if (empty($pass_lama) || empty($pass_baru) || empty($konfirmasi)) {
        $pesan_error = 'Semua bidang form password wajib diisi!';
    } elseif ($pass_baru !== $konfirmasi) {
        $pesan_error = 'Password baru dan konfirmasi password tidak cocok!';
    } elseif (strlen($pass_baru) < 6) {
        $pesan_error = 'Password baru minimal 6 karakter!';
    } else {
        // Cek password lama di tb_owner
        $stmt_cek = mysqli_prepare($koneksi, "SELECT password FROM tb_owner WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt_cek, "s", $owner_username);
        mysqli_stmt_execute($stmt_cek);
        $res_cek = mysqli_stmt_get_result($stmt_cek);
        $data_user = mysqli_fetch_assoc($res_cek);
        mysqli_stmt_close($stmt_cek);

        if ($data_user && password_verify($pass_lama, $data_user['password'])) {
            // Update password baru
            $pass_hash = password_hash($pass_baru, PASSWORD_DEFAULT);
            $stmt_upd  = mysqli_prepare($koneksi, "UPDATE tb_owner SET password = ? WHERE username = ?");
            mysqli_stmt_bind_param($stmt_upd, "ss", $pass_hash, $owner_username);
            if (mysqli_stmt_execute($stmt_upd)) {
                $pesan_sukses = 'Password Owner berhasil diubah! Gunakan password baru untuk login berikutnya.';
            } else {
                $pesan_error = 'Gagal memperbarui password di database.';
            }
            mysqli_stmt_close($stmt_upd);
        } else {
            $pesan_error = 'Password lama yang Anda masukkan salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ganti Password — Owner Portal</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="../css/theme.css" rel="stylesheet" media="all">
    <link href="../css/modern-custom.css" rel="stylesheet" media="all">
    <style>
        .menu-sidebar { background-color: #170d2b !important; }
        .menu-sidebar .logo { background-color: #170d2b !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; }
        .header-mobile { background: #170d2b !important; }
        .header-mobile .navbar-mobile, .header-mobile .navbar-mobile .navbar-mobile__list { background: #170d2b !important; }
        .card-password { max-width: 540px; margin: 0 auto; }
        .avatar-owner { background-color: #7e22ce !important; color: #ffffff !important; }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <!-- HEADER MOBILE -->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a href="dashboard.php" class="logo">
                            <h3><i class="fas fa-crown mr-2" style="color:#c084fc;"></i>ABSENSI</h3>
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
                        <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a></li>
                        <li><a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a></li>
                        <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="approval.php"><i class="fas fa-check-double"></i> Approval Cuti</a></li>
                        <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Kehadiran</a></li>
                        <li class="active"><a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout Owner</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- MENU SIDEBAR -->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo" style="background-color: #170d2b; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <a href="dashboard.php">
                    <h3 style="color:#ffffff;"><i class="fas fa-crown mr-2" style="color:#c084fc;"></i>OWNER PORTAL</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a></li>
                        <li><a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a></li>
                        <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="approval.php"><i class="fas fa-check-double"></i> Approval Cuti</a></li>
                        <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Kehadiran</a></li>
                        <li class="active"><a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout Owner</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- PAGE CONTAINER -->
        <div class="page-container">
            <!-- HEADER DESKTOP -->
            <header class="header-desktop d-none d-lg-block" style="background:#fff; border-bottom:1px solid var(--color-border); box-shadow:var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Ganti Password Owner</h4>
                                <small class="text-muted">Keamanan Akun Owner Executive</small>
                            </div>
                            <div class="account-item clearfix">
                                <div class="content d-flex align-items-center">
                                    <div class="avatar-initial avatar-sm mr-2 avatar-owner"><i class="fas fa-crown" style="font-size:0.75rem;"></i></div>
                                    <span class="font-weight-bold text-dark"><?= htmlspecialchars($owner_nama) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT -->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">

                        <div class="card card-password p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-initial avatar-md mr-3" style="background:#f3e8ff; color:#7e22ce;">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0">Ubah Password Akun Owner</h5>
                                    <p class="text-muted small mb-0">Username: <strong>@<?= htmlspecialchars($owner_username) ?></strong></p>
                                </div>
                            </div>
                            <hr class="mt-2 mb-4">

                            <?php if (!empty($pesan_sukses)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($pesan_sukses) ?>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($pesan_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle mr-1"></i> <?= htmlspecialchars($pesan_error) ?>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                            <?php endif; ?>

                            <form action="" method="POST">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-muted small">PASSWORD LAMA OWNER</label>
                                    <input type="password" name="pass_lama" class="form-control" placeholder="Masukkan password lama Owner" required autocomplete="off">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-muted small">PASSWORD BARU</label>
                                    <input type="password" name="pass_baru" class="form-control" placeholder="Masukkan password baru (minimal 6 karakter)" required autocomplete="off">
                                </div>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-muted small">KONFIRMASI PASSWORD BARU</label>
                                    <input type="password" name="konfirmasi_pass" class="form-control" placeholder="Ulangi password baru Anda" required autocomplete="off">
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="dashboard.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i> Batal
                                    </a>
                                    <button type="submit" name="simpan_password" class="btn font-weight-bold" style="background:#7e22ce; color:#fff;">
                                        <i class="fas fa-save mr-1"></i> Simpan Password Baru
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
