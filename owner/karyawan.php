<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';
$owner_nama = $_SESSION['owner_nama'] ?? 'Owner Executive';

// Handle Tambah Karyawan via Owner
$pesan = '';
if (isset($_POST['simpan_karyawan'])) {
    $id_karyawan   = trim($_POST['id_karyawan']);
    $username_kar  = trim($_POST['username']);
    $password_kar  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_kar      = trim($_POST['nama']);
    $jenkel        = $_POST['jenkel'];
    $no_tel        = trim($_POST['no_tel']);
    $jabatan       = $_POST['jabatan'];
    $tgl_masuk     = $_POST['tgl_masuk'];

    $cek = mysqli_query($koneksi, "SELECT id_karyawan FROM tb_karyawan WHERE id_karyawan='$id_karyawan' OR username='$username_kar'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = 'error_duplikat';
    } else {
        $sql = "INSERT INTO tb_karyawan (id_karyawan, username, password, nama, tmp_tgl_lahir, jenkel, agama, alamat, no_tel, tgl_masuk, jabatan, foto) 
                VALUES (?, ?, ?, ?, '-', ?, '-', '-', ?, ?, ?, '-')";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssss", $id_karyawan, $username_kar, $password_kar, $nama_kar, $jenkel, $no_tel, $tgl_masuk, $jabatan);
        if (mysqli_stmt_execute($stmt)) {
            $pesan = 'sukses_tambah';
        } else {
            $pesan = 'gagal';
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle Hapus Karyawan via Owner
if (isset($_GET['hapus_id'])) {
    $hid = $_GET['hapus_id'];
    $stmt_del = mysqli_prepare($koneksi, "DELETE FROM tb_karyawan WHERE id_karyawan = ?");
    mysqli_stmt_bind_param($stmt_del, "s", $hid);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
    header("location: karyawan.php?pesan=sukses_hapus");
    exit;
}

$cari = trim($_GET['cari'] ?? '');
$sql_list = "SELECT * FROM tb_karyawan";
if (!empty($cari)) {
    $cari_esc = mysqli_real_escape_string($koneksi, $cari);
    $sql_list .= " WHERE nama LIKE '%$cari_esc%' OR id_karyawan LIKE '%$cari_esc%' OR jabatan LIKE '%$cari_esc%'";
}
$sql_list .= " ORDER BY nama ASC";
$q_list = mysqli_query($koneksi, $sql_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kelola Data Karyawan — Owner Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">

    <!-- CSS -->
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
    </style>
</head>

<body>
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
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

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo" style="background-color: #170d2b; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <a href="dashboard.php">
                    <h3 style="color:#ffffff;"><i class="fas fa-crown mr-2" style="color:#c084fc;"></i>OWNER PORTAL</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li>
                            <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard Executive</a>
                        </li>
                        <li class="active">
                            <a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a>
                        </li>
                        <li>
                            <a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a>
                        </li>
                        <li>
                            <a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a>
                        </li>
                        <li>
                            <a href="approval.php"><i class="fas fa-check-double"></i> Approval Cuti</a>
                        </li>
                        <li>
                            <a href="laporan.php"><i class="fas fa-file-alt"></i> Rekap Kehadiran</a>
                        </li>
                        <li>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout Owner</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop" style="background: #ffffff; border-bottom: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Kelola Data Karyawan (Owner)</h4>
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

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">

                        <?php if ($pesan === 'sukses_tambah' || ($_GET['pesan'] ?? '') === 'sukses_tambah'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-1"></i> Data karyawan baru berhasil ditambahkan!
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        <?php elseif ($pesan === 'error_duplikat'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-1"></i> Gagal: ID Karyawan atau Username sudah terdaftar!
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        <?php elseif (($_GET['pesan'] ?? '') === 'sukses_hapus'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-1"></i> Data karyawan berhasil dihapus!
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        <?php endif; ?>

                        <!-- TOP TOOLBAR & EXPORT -->
                        <div class="card p-3 mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <form method="GET" action="karyawan.php" class="d-flex">
                                        <input type="text" name="cari" class="form-control mr-2" placeholder="Cari nama, ID, atau jabatan..." value="<?= htmlspecialchars($cari) ?>">
                                        <button type="submit" class="btn btn-primary font-weight-bold">
                                            <i class="fas fa-search mr-1"></i> Cari
                                        </button>
                                        <?php if (!empty($cari)): ?>
                                            <a href="karyawan.php" class="btn btn-outline-secondary ml-2">Reset</a>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <button type="button" class="btn btn-primary font-weight-bold mr-2" data-toggle="modal" data-target="#modalTambahKaryawan">
                                        <i class="fas fa-user-plus mr-1"></i> Tambah Karyawan
                                    </button>
                                    <a href="export_excel.php?type=karyawan" class="btn btn-success font-weight-bold mr-1">
                                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                                    </a>
                                    <a href="export_pdf.php?type=karyawan" target="_blank" class="btn btn-danger font-weight-bold">
                                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL KARYAWAN -->
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="font-weight-bold text-dark mb-0">
                                    Daftar Karyawan Aktif (<?= mysqli_num_rows($q_list) ?> Orang)
                                </h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>ID Karyawan</th>
                                            <th>Nama Lengkap</th>
                                            <th>Jenis Kelamin</th>
                                            <th>No. Telepon</th>
                                            <th>Jabatan</th>
                                            <th>Tgl Masuk</th>
                                            <th>Masa Kerja</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($q_list) == 0):
                                        ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">Tidak ada data karyawan yang ditemukan.</td>
                                        </tr>
                                        <?php
                                        endif;
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($q_list)):
                                            $tgl_masuk = $row['tgl_masuk'] ?? '';
                                            $masa_kerja = !empty($tgl_masuk) ? hitungMasaKerja($tgl_masuk) : '-';
                                            $tgl_format = !empty($tgl_masuk) ? date('d/m/Y', strtotime($tgl_masuk)) : '-';
                                            $icon_cls = getJabatanIcon($row['jabatan']);
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="font-weight-bold text-primary"><?= htmlspecialchars($row['id_karyawan']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-initial avatar-sm mr-2"><?= strtoupper(substr($row['nama'], 0, 1)) ?></div>
                                                    <div>
                                                        <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                                        <br><small class="text-muted">@<?= htmlspecialchars($row['username']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge-modern bg-light text-dark border"><?= htmlspecialchars($row['jenkel']) ?></span></td>
                                            <td><?= htmlspecialchars($row['no_tel']) ?></td>
                                            <td>
                                                <span class="badge-modern badge-jabatan">
                                                    <i class="<?= htmlspecialchars($icon_cls) ?> mr-1"></i> <?= htmlspecialchars($row['jabatan']) ?>
                                                </span>
                                            </td>
                                            <td><?= $tgl_format ?></td>
                                            <td><strong style="color: #059669;"><?= $masa_kerja ?></strong></td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                <a href="../admin/karyawan_edit.php?id=<?= urlencode($row['id_karyawan']) ?>" class="btn btn-sm btn-warning font-weight-bold mr-1">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="karyawan.php?hapus_id=<?= urlencode($row['id_karyawan']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data karyawan <?= htmlspecialchars(addslashes($row['nama'])) ?>?');">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
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
                <form action="karyawan.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">ID KARYAWAN (Angka/Kode)</label>
                                <input type="text" class="form-control" name="id_karyawan" placeholder="Contoh: 10829102" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">NAMA LENGKAP</label>
                                <input type="text" class="form-control" name="nama" placeholder="Contoh: Budi Santoso" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">USERNAME LOGIN</label>
                                <input type="text" class="form-control" name="username" placeholder="Contoh: budi_santoso" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">PASSWORD LOGIN</label>
                                <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">JENIS KELAMIN</label>
                                <select name="jenkel" class="form-control" required>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">NO. TELEPON / WHATSAPP</label>
                                <input type="text" class="form-control" name="no_tel" placeholder="Contoh: 081234567890" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">TANGGAL MASUK BEKERJA</label>
                                <input type="date" class="form-control" name="tgl_masuk" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">POSISI / JABATAN</label>
                                <select name="jabatan" class="form-control" required>
                                    <?php
                                    $q_jab = mysqli_query($koneksi, "SELECT * FROM tb_jabatan ORDER BY jabatan ASC");
                                    while ($j = mysqli_fetch_assoc($q_jab)) {
                                        echo '<option value="'.htmlspecialchars($j['jabatan']).'">'.htmlspecialchars($j['jabatan']).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="simpan_karyawan" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Karyawan
                        </button>
                    </div>
                </form>
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
