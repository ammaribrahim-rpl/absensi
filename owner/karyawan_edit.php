<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header('location: login_owner.php');
    exit;
}
require_once '../koneksi.php';
$owner_nama = $_SESSION['owner_nama'] ?? 'Owner Executive';

$id = $_GET['id_karyawan'] ?? '';
$stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_karyawan WHERE id_karyawan = ?");
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_assoc($data);

if (!$d) {
    echo "<script>alert('Data karyawan tidak ditemukan'); window.location.href = 'karyawan.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Karyawan — Owner Portal</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="../css/theme.css" rel="stylesheet" media="all">
    <link href="../css/modern-custom.css" rel="stylesheet" media="all">
    <style>
        .badge-owner {
            background-color: #f3e8ff !important;
            color: #7e22ce !important;
            border: 1px solid #e9d5ff !important;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 0.78rem;
        }
        .avatar-owner {
            background-color: #7e22ce !important;
            color: #ffffff !important;
        }
        .sidebar-owner-bg {
            background-color: #170d2b !important;
            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
        }
        .edit-card {
            max-width: 680px;
            margin: 0 auto;
        }
        .field-row {
            display: grid;
            grid-template-columns: 220px 1fr;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .field-row:last-child { border-bottom: none; }
        .field-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
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
                        <li class="active"><a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a></li>
                        <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="approval.php"><i class="fas fa-check-double"></i> Approval Cuti</a></li>
                        <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Kehadiran</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout Owner</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- MENU SIDEBAR -->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo sidebar-owner-bg">
                <a href="dashboard.php">
                    <h3 style="color:#ffffff;"><i class="fas fa-crown mr-2" style="color:#c084fc;"></i>OWNER PORTAL</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a></li>
                        <li class="active"><a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a></li>
                        <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="approval.php"><i class="fas fa-check-double"></i> Approval Cuti</a></li>
                        <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Kehadiran</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout Owner</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- PAGE CONTAINER -->
        <div class="page-container">
            <!-- HEADER DESKTOP -->
            <header class="header-desktop" style="background: #ffffff; border-bottom: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Edit Data Karyawan</h4>
                                <small class="text-muted">Perbarui informasi karyawan</small>
                            </div>
                            <div class="account-item clearfix">
                                <div class="content d-flex align-items-center">
                                    <div class="avatar-initial avatar-sm mr-2 avatar-owner">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold text-dark"><?= htmlspecialchars($owner_nama) ?></span>
                                        <span class="badge-owner ml-2">Executive Owner</span>
                                    </div>
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

                        <?php if (($_GET['sukses'] ?? '') === '1'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-1"></i> Data karyawan berhasil diperbarui!
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        <?php elseif (($_GET['error'] ?? '') === '1'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-1"></i> Gagal menyimpan data, silakan coba lagi.
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        <?php endif; ?>

                        <div class="edit-card">
                            <div class="card p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="avatar-initial avatar-md mr-3" style="background:#eef2ff; color:#4f46e5; font-size:1.3rem;">
                                        <?= strtoupper(substr($d['nama'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h5 class="font-weight-bold mb-0 text-dark"><?= htmlspecialchars($d['nama']) ?></h5>
                                        <small class="text-muted">@<?= htmlspecialchars($d['username']) ?> &bull;
                                            <span style="color:#059669; font-weight:600;">
                                                <i class="fas fa-business-time mr-1"></i><?= hitungMasaKerja($d) ?>
                                            </span>
                                        </small>
                                    </div>
                                </div>

                                <form action="proedit_karyawan_owner.php" method="POST">
                                    <input type="hidden" name="id_karyawan" value="<?= htmlspecialchars($d['id_karyawan']) ?>">

                                    <div class="field-row">
                                        <div class="field-label"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Masuk</div>
                                        <div>
                                            <input type="text" class="form-control bg-light font-weight-bold" readonly value="<?= htmlspecialchars(getFormattedTglMasuk($d)) ?>">
                                        </div>
                                    </div>

                                    <div class="field-row">
                                        <div class="field-label"><i class="fas fa-user mr-1"></i> Username</div>
                                        <input type="text" class="form-control" name="username" required value="<?= htmlspecialchars($d['username']) ?>" placeholder="Username login">
                                    </div>

                                    <div class="field-row">
                                        <div class="field-label"><i class="fas fa-key mr-1"></i> Password Baru (Opsional)</div>
                                        <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                                    </div>

                                    <div class="field-row">
                                        <div class="field-label"><i class="fas fa-id-card mr-1"></i> Nama Lengkap</div>
                                        <input type="text" class="form-control" name="nama" required value="<?= htmlspecialchars($d['nama']) ?>" placeholder="Nama lengkap karyawan">
                                    </div>

                                    <div class="field-row">
                                        <div class="field-label"><i class="fas fa-venus-mars mr-1"></i> Jenis Kelamin</div>
                                        <select class="form-control" name="jenkel">
                                            <option value="Laki-laki" <?= ($d['jenkel'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="Perempuan" <?= ($d['jenkel'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="field-row">
                                        <div class="field-label"><i class="fas fa-phone mr-1"></i> Nomor Telepon</div>
                                        <input type="text" class="form-control" name="no_tel" value="<?= htmlspecialchars($d['no_tel']) ?>" placeholder="Contoh: 081234567890">
                                    </div>

                                    <div class="field-row">
                                        <div class="field-label"><i class="fas fa-briefcase mr-1"></i> Jabatan</div>
                                        <select class="form-control" name="jabatan">
                                            <?php
                                            $sql_jab = mysqli_query($koneksi, "SELECT * FROM tb_jabatan ORDER BY jabatan ASC");
                                            while ($dj = mysqli_fetch_assoc($sql_jab)) {
                                                $sel = ($dj['jabatan'] == $d['jabatan']) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($dj['jabatan']) . "' $sel>" . htmlspecialchars($dj['jabatan']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="d-flex mt-4">
                                        <button type="submit" name="ubahdata" class="btn btn-primary font-weight-bold mr-2">
                                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                        </button>
                                        <a href="karyawan.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                                        </a>
                                    </div>
                                </form>
                            </div>
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
