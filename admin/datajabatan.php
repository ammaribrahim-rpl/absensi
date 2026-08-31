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
    <title>Data Jabatan — Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">
    <!-- CSS -->
    <link href="../css/font-face.css" rel="stylesheet">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../css/modern-custom.css" rel="stylesheet">

</head>

<body>
    <div class="page-wrapper">
        <!-- HEADER MOBILE -->
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
                        <li><a href="admin.php"><i class="fas fa-home"></i> Beranda Admin</a></li>
                        <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li class="active"><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE -->

        <!-- MENU SIDEBAR -->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="admin.php">
                    <h3><i class="fas fa-fingerprint mr-2"></i>ABSENSI</h3>
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                    <li><a href="admin.php"><i class="fas fa-home"></i> Beranda Admin</a></li>
                    <li><a href="datakaryawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                    <li class="active"><a href="datajabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR -->


        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <header class="header-desktop" style="background: #ffffff; border-bottom: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">Kelola Data Jabatan</h4>
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
                            <!-- FORM TAMBAH JABATAN -->
                            <div class="col-md-5 mb-4">
                                <div class="card p-4">
                                    <h5 class="font-weight-bold text-dark mb-3">
                                        <i class="fas fa-plus-circle text-primary mr-2"></i> Tambah Jabatan Baru
                                    </h5>
                                    <form action="jabatan_sv.php" method="POST" id="formTambahJabatan">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted small">NAMA JABATAN / POSISI</label>
                                            <input type="text" class="form-control" name="jabatan" id="inputNamaJabatan" placeholder="Contoh: Staff IT, HRD, Supervisor" required autofocus>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted small">ICON JABATAN</label>
                                            <input type="hidden" name="icon" id="selectedIcon" value="fas fa-briefcase">
                                            <div class="d-flex align-items-center mb-2">
                                                <div id="iconPreview" class="icon-preview-box mr-3">
                                                    <i class="fas fa-briefcase fa-lg"></i>
                                                </div>
                                                <span class="text-muted small" id="iconLabel">fas fa-briefcase</span>
                                            </div>
                                            <div class="icon-picker-grid" id="iconPickerGrid">
                                                <?php
                                                $iconList = [
                                                    'fas fa-briefcase'         => 'Koper',
                                                    'fas fa-user-tie'          => 'Eksekutif',
                                                    'fas fa-hard-hat'          => 'Mandor',
                                                    'fas fa-laptop'            => 'IT/Online',
                                                    'fas fa-boxes'             => 'Gudang',
                                                    'fas fa-truck'             => 'Driver',
                                                    'fas fa-store'             => 'Toko',
                                                    'fas fa-cash-register'     => 'Kasir',
                                                    'fas fa-hands-helping'     => 'Helper',
                                                    'fas fa-chart-line'        => 'Manajer',
                                                    'fas fa-tools'             => 'Teknisi',
                                                    'fas fa-headset'           => 'CS',
                                                    'fas fa-shield-alt'        => 'Security',
                                                    'fas fa-broom'             => 'Kebersihan',
                                                    'fas fa-stethoscope'       => 'Medis',
                                                    'fas fa-utensils'          => 'Dapur',
                                                    'fas fa-dollar-sign'       => 'Keuangan',
                                                    'fas fa-chalkboard-teacher'=> 'Trainer',
                                                    'fas fa-paint-brush'       => 'Desain',
                                                    'fas fa-cogs'              => 'Operasional',
                                                    'fas fa-box'               => 'Logistik',
                                                    'fas fa-shipping-fast'     => 'Ekspedisi',
                                                    'fas fa-user-md'           => 'Medis',
                                                    'fas fa-flask'             => 'Lab',
                                                ];
                                                foreach ($iconList as $cls => $label) {
                                                    echo '<button type="button" class="icon-pick-btn" data-icon="'.htmlspecialchars($cls).'" title="'.htmlspecialchars($label).'"><i class="'.htmlspecialchars($cls).'"></i></button>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <button type="submit" name="simpan" class="btn btn-primary btn-block font-weight-bold mt-3">
                                            <i class="fas fa-save mr-1"></i> Simpan Jabatan
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- TABEL DAFTAR JABATAN -->
                            <div class="col-md-7">
                                <div class="card p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="font-weight-bold text-dark mb-0">Daftar Jabatan</h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Jabatan</th>
                                                    <th class="text-center">Icon</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $sql = "SELECT * FROM tb_jabatan ORDER BY id ASC";
                                                $query = mysqli_query($koneksi, $sql);
                                                $no = 1;
                                                while ($row = mysqli_fetch_assoc($query)) {
                                                    $icon_class = !empty($row['icon']) ? $row['icon'] : 'fas fa-briefcase';
                                                ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td>
                                                        <span class="badge-modern badge-jabatan font-weight-bold" style="font-size: 0.88rem;">
                                                            <i class="<?= htmlspecialchars($icon_class) ?> mr-1"></i> <?= htmlspecialchars($row['jabatan']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="openIconModal(<?= (int)$row['id'] ?>, '<?= htmlspecialchars(addslashes($row['jabatan'])) ?>', '<?= htmlspecialchars($icon_class) ?>')">
                                                            <i class="<?= htmlspecialchars($icon_class) ?> mr-1"></i> Ganti
                                                        </button>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="hapus_jabatan.php?id=<?= urlencode($row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus jabatan ini?');">
                                                            <i class="fas fa-trash mr-1"></i> Hapus
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
        </div>
    </div>

    <!-- MODAL GANTI ICON -->
    <div class="modal fade" id="iconModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-palette mr-2 text-primary"></i>Ganti Icon Jabatan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="jabatan_sv.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="update_jabatan" value="1">
                        <input type="hidden" name="id" id="modalJabatanId">
                        <input type="hidden" name="jabatan" id="modalJabatanNama">
                        <input type="hidden" name="icon" id="modalSelectedIcon" value="fas fa-briefcase">
                        
                        <div class="d-flex align-items-center mb-3 p-3 rounded" style="background: #f8f9ff; border: 1px solid #e0e4ff;">
                            <div class="icon-preview-box mr-3" id="modalIconPreviewBox">
                                <i id="modalIconPreviewI" class="fas fa-briefcase fa-lg"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold text-dark" id="modalJabatanLabel"></div>
                                <small class="text-muted" id="modalIconClass">fas fa-briefcase</small>
                            </div>
                        </div>

                        <label class="font-weight-bold text-muted small mb-2">PILIH ICON:</label>
                        <div class="icon-picker-grid" id="modalIconGrid">
                            <?php
                            foreach ($iconList as $cls => $label) {
                                echo '<button type="button" class="icon-pick-btn modal-icon-btn" data-icon="'.htmlspecialchars($cls).'" title="'.htmlspecialchars($label).'"><i class="'.htmlspecialchars($cls).'"></i><br><small>'.htmlspecialchars($label).'</small></button>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Icon</button>
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
    <script>
    // === FORM TAMBAH: Icon Picker ===
    document.querySelectorAll('#iconPickerGrid .icon-pick-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var icon = this.dataset.icon;
            document.getElementById('selectedIcon').value = icon;
            document.getElementById('iconLabel').textContent = icon;
            document.getElementById('iconPreview').innerHTML = '<i class="' + icon + ' fa-lg"></i>';
            document.querySelectorAll('#iconPickerGrid .icon-pick-btn').forEach(function(b) { b.classList.remove('selected'); });
            this.classList.add('selected');
        });
    });

    // === MODAL: Ganti Icon ===
    function openIconModal(id, nama, currentIcon) {
        document.getElementById('modalJabatanId').value = id;
        document.getElementById('modalJabatanNama').value = nama;
        document.getElementById('modalJabatanLabel').textContent = nama;
        document.getElementById('modalSelectedIcon').value = currentIcon;
        document.getElementById('modalIconPreviewI').className = currentIcon + ' fa-lg';
        document.getElementById('modalIconClass').textContent = currentIcon;
        
        document.querySelectorAll('#modalIconGrid .modal-icon-btn').forEach(function(b) {
            b.classList.toggle('selected', b.dataset.icon === currentIcon);
        });
        
        $('#iconModal').modal('show');
    }

    document.querySelectorAll('#modalIconGrid .modal-icon-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var icon = this.dataset.icon;
            document.getElementById('modalSelectedIcon').value = icon;
            document.getElementById('modalIconPreviewI').className = icon + ' fa-lg';
            document.getElementById('modalIconClass').textContent = icon;
            document.querySelectorAll('#modalIconGrid .modal-icon-btn').forEach(function(b) { b.classList.remove('selected'); });
            this.classList.add('selected');
        });
    });
    </script>
</body>
</html>
