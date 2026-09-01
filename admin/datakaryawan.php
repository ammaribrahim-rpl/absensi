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
    <title>Data Karyawan — Absensi Admin</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="../css/theme.css" rel="stylesheet" media="all">
    <link href="../css/modern-custom.css" rel="stylesheet" media="all">
    <style>
        /* ── Admin Sidebar Ungu Gelap ── */
        .menu-sidebar { background-color: #170d2b !important; }
        .menu-sidebar .logo { background-color: #170d2b !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; }
        .header-mobile { background: #170d2b !important; }
        .header-mobile .navbar-mobile, .header-mobile .navbar-mobile .navbar-mobile__list { background: #170d2b !important; }

        /* ── Table Compact ── */
        .table-compact th {
            font-size: 0.7rem !important; font-weight: 700 !important;
            letter-spacing: 0.04em !important; text-transform: uppercase !important;
            color: #6b7280 !important; padding: 10px 10px !important;
            white-space: nowrap !important; background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important; border-top: none !important;
        }
        .table-compact td {
            font-size: 0.8rem !important; padding: 9px 10px !important;
            vertical-align: middle !important; border-top: 1px solid #f1f5f9 !important;
            color: #1e2228 !important;
        }
        .avatar-initial-compact {
            width: 28px; height: 28px; min-width: 28px; font-size: 0.72rem; font-weight: 700;
            border-radius: 50%; background: #eef2ff; color: #4f46e5;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .btn-action-compact {
            padding: 4px 8px !important; font-size: 0.73rem !important; font-weight: 600 !important;
            border-radius: 6px !important; display: inline-flex; align-items: center; gap: 4px; line-height: 1.2 !important;
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
                        <li class="active"><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- MENU SIDEBAR -->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo" style="background-color: #170d2b; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <a href="admin.php">
                    <h3 style="color:#ffffff;"><i class="fas fa-fingerprint mr-2" style="color:#818cf8;"></i>ABSENSI</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li><a href="admin.php"><i class="fas fa-chart-line"></i> Beranda Admin</a></li>
                        <li class="active"><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                                <h4 class="font-weight-bold mb-0 text-dark">Data Karyawan</h4>
                                <small class="text-muted">Kelola Data Karyawan</small>
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

            <!-- MAIN CONTENT -->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">

                        <?php if (($_GET['pesan'] ?? '') === 'sukses_tambah'): ?>
                        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-1"></i> Data karyawan berhasil ditambahkan! <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
                        <?php elseif (($_GET['pesan'] ?? '') === 'sukses_hapus'): ?>
                        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-1"></i> Data karyawan berhasil dihapus! <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
                        <?php endif; ?>

                        <!-- TOOLBAR -->
                        <div class="card p-3 mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <form method="GET" action="prospenkar.php" class="d-flex">
                                        <input type="text" name="cari" class="form-control mr-2" placeholder="Cari nama atau tanggal masuk..." value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>">
                                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-search mr-1"></i> Cari</button>
                                    </form>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#modalTambahKaryawan">
                                        <i class="fas fa-user-plus mr-1"></i> Tambah Karyawan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL DATA KARYAWAN -->
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="font-weight-bold text-dark mb-0">Daftar Data Karyawan</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-compact">
                                    <thead>
                                        <tr>
                                            <th style="width: 38px; text-align: center;">No</th>
                                            <th style="width: 24%;">Nama Karyawan</th>
                                            <th style="width: 15%;">Jabatan</th>
                                            <th style="width: 11%; text-align: center;">Jenis Kelamin</th>
                                            <th style="width: 14%;">No. Telepon</th>
                                            <th style="width: 13%;">Tanggal Masuk</th>
                                            <th style="width: 14%;">Masa Kerja</th>
                                            <th style="width: 100px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php include 'paging.php'; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PAGINATION -->
                            <ul class="pagination justify-content-center mt-3">
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
    </div>

    <!-- MODAL TAMBAH KARYAWAN -->
    <div class="modal fade" id="modalTambahKaryawan" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-2 text-primary"></i>Tambah Data Karyawan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="dt_karyawan_sv.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">TANGGAL MASUK (DD-MM-YYYY)</label>
                                <input type="text" class="form-control" name="id_karyawan" id="inputTglMasuk" required autocomplete="off" placeholder="Contoh: 15-01-2024" maxlength="10" oninput="formatDateDDMMYYYY(this)">
                                <div id="previewMasaKerja" class="mt-1 font-weight-bold" style="color:#4f46e5; display:none; font-size:0.8rem;"></div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">NAMA LENGKAP</label>
                                <input type="text" class="form-control" name="nama" placeholder="Contoh: Budi Santoso" required autocomplete="off">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">USERNAME LOGIN</label>
                                <input type="text" class="form-control" name="username" placeholder="Contoh: budi_santoso" required autocomplete="off">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">PASSWORD LOGIN</label>
                                <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter" required autocomplete="off">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">JENIS KELAMIN</label>
                                <select name="jenkel" class="form-control">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">NO. TELEPON / WHATSAPP</label>
                                <input type="text" class="form-control" name="no_tel" placeholder="Contoh: 081234567890" autocomplete="off">
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="font-weight-bold text-muted small">POSISI / JABATAN</label>
                                <select name="jabatan" class="form-control">
                                    <?php
                                    $sql_j = mysqli_query($koneksi, "SELECT * FROM tb_jabatan ORDER BY jabatan ASC");
                                    while ($dj = mysqli_fetch_assoc($sql_j)) {
                                        echo "<option value='" . htmlspecialchars($dj['jabatan']) . "'>" . htmlspecialchars($dj['jabatan']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="simpan" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Karyawan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../js/main.js"></script>
    <script>
    function formatDateDDMMYYYY(input) {
        let v = input.value.replace(/\D/g, '').slice(0, 8);
        if (v.length >= 5) {
            input.value = v.slice(0, 2) + '-' + v.slice(2, 4) + '-' + v.slice(4);
        } else if (v.length >= 3) {
            input.value = v.slice(0, 2) + '-' + v.slice(2);
        } else {
            input.value = v;
        }
        const preview = document.getElementById('previewMasaKerja');
        if (v.length === 8) {
            const d = parseInt(v.slice(0, 2), 10);
            const m = parseInt(v.slice(2, 4), 10) - 1;
            const y = parseInt(v.slice(4), 10);
            const tglMasuk = new Date(y, m, d);
            const now = new Date();
            if (!isNaN(tglMasuk.getTime())) {
                let diffYears = now.getFullYear() - tglMasuk.getFullYear();
                let diffMonths = now.getMonth() - tglMasuk.getMonth();
                if (now.getDate() < tglMasuk.getDate()) diffMonths--;
                if (diffMonths < 0) { diffYears--; diffMonths += 12; }
                if (diffYears < 0) {
                    preview.textContent = "⏱️ Estimasi: Baru Bergabung";
                } else if (diffYears === 0) {
                    preview.textContent = `⏱️ Estimasi Masa Kerja: ${diffMonths} Bulan`;
                } else if (diffMonths === 0) {
                    preview.textContent = `⏱️ Estimasi Masa Kerja: ${diffYears} Tahun`;
                } else {
                    preview.textContent = `⏱️ Estimasi Masa Kerja: ${diffYears} Tahun ${diffMonths} Bulan`;
                }
                preview.style.display = 'block';
            }
        } else if (preview) {
            preview.style.display = 'none';
        }
    }
    </script>
</body>
</html>
