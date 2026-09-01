<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';
$owner_nama = $_SESSION['owner_nama'] ?? 'Owner Executive';

// Proses Approval / Rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id     = intval($_GET['id']);

    if ($action === 'approve') {
        $status = 'Disetujui';
    } elseif ($action === 'reject') {
        $status = 'Ditolak';
    } else {
        $status = 'Proses';
    }

    // Update status
    $stmt = mysqli_prepare($koneksi, "UPDATE tb_keterangan SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        // Ambil detail pengajuan + nomor HP karyawan
        $stmt_info = mysqli_prepare($koneksi,
            "SELECT k.id_karyawan, k.nama, k.keterangan, k.tgl_mulai, k.tgl_selesai, ka.no_tel
             FROM tb_keterangan k
             LEFT JOIN tb_karyawan ka ON ka.id_karyawan = k.id_karyawan
             WHERE k.id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt_info, 'i', $id);
        mysqli_stmt_execute($stmt_info);
        $res_info = mysqli_stmt_get_result($stmt_info);
        $info     = mysqli_fetch_assoc($res_info);
        mysqli_stmt_close($stmt_info);

        if ($info && $status !== 'Proses') {
            $id_karyawan = $info['id_karyawan'];
            $nama_kar    = $info['nama'];
            $jenis       = $info['keterangan'] ?? 'Cuti/Izin';
            $no_hp       = $info['no_tel']    ?? '';
            $tgl_mulai   = $info['tgl_mulai'] ?? '';
            $tgl_selesai = $info['tgl_selesai'] ?? '';

            $periode_str = '';
            if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
                $periode_str = date('d/m/Y', strtotime($tgl_mulai)) . ' s/d ' . date('d/m/Y', strtotime($tgl_selesai));
            }

            // Simpan ke tb_notifikasi (bell icon karyawan)
            $emoji       = $status === 'Disetujui' ? '✅' : '❌';
            $info_per    = !empty($periode_str) ? " untuk periode $periode_str" : "";
            $pesan_notif = "$emoji Pengajuan $jenis kamu$info_per telah *$status* oleh Owner pada " . date('d/m/Y H:i') . ".";
            $tipe_notif  = $status === 'Disetujui' ? 'approval' : 'penolakan';
            $stmt_notif  = mysqli_prepare($koneksi,
                "INSERT INTO tb_notifikasi (id_karyawan, nama, pesan, tipe) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_notif, 'ssss', $id_karyawan, $nama_kar, $pesan_notif, $tipe_notif);
            mysqli_stmt_execute($stmt_notif);
            mysqli_stmt_close($stmt_notif);
        }

        $_SESSION['msg'] = "Status pengajuan berhasil diubah menjadi: $status";
    } else {
        $_SESSION['msg_error'] = "Gagal mengubah status pengajuan.";
    }

    header("Location: approval.php");
    exit;
}

// Pagination setup
$batas = 10;
$halaman = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
if ($halaman <= 0) $halaman = 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$total_data = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM tb_keterangan"));
$total_halaman = ceil($total_data / $batas);
if ($total_halaman == 0) $total_halaman = 1;

$previous = $halaman - 1;
$next = $halaman + 1;

// Load data pengajuan
$query_pengajuan = mysqli_query($koneksi, "SELECT * FROM tb_keterangan ORDER BY id DESC LIMIT $halaman_awal, $batas");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Persetujuan Pengajuan Cuti — Owner Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">

    <!-- CSS -->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
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
        .table-responsive {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }
        .badge-proses {
            background-color: #fef3c7 !important;
            color: #d97706 !important;
            border: 1px solid #fde68a;
            font-size: 0.72rem !important;
            padding: 2px 8px !important;
        }
        .badge-disetujui {
            background-color: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0;
            font-size: 0.72rem !important;
            padding: 2px 8px !important;
        }
        .badge-ditolak {
            background-color: #fee2e2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca;
            font-size: 0.72rem !important;
            padding: 2px 8px !important;
        }

        /* ── Compact Table Styling ── */
        .table-compact {
            width: 100%;
            table-layout: auto;
            margin-bottom: 0 !important;
        }
        .table-compact th {
            font-size: 0.7rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.04em !important;
            text-transform: uppercase !important;
            color: #6b7280 !important;
            padding: 10px 10px !important;
            white-space: nowrap !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none !important;
        }
        .table-compact td {
            font-size: 0.8rem !important;
            padding: 9px 10px !important;
            vertical-align: middle !important;
            border-top: 1px solid #f1f5f9 !important;
            color: #1e2228 !important;
        }
        .avatar-initial-compact {
            width: 26px;
            height: 26px;
            min-width: 26px;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 50%;
            background: #eef2ff;
            color: #4f46e5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        .btn-action-compact {
            padding: 4px 8px !important;
            font-size: 0.73rem !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            line-height: 1.2 !important;
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
                        <li><a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a></li>
                        <li><a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a></li>
                        <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a></li>
                        <li class="active"><a href="approval.php"><i class="fas fa-check-double"></i> Approval Cuti</a></li>
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
                        <li>
                            <a href="karyawan.php"><i class="fas fa-users"></i> Data Karyawan</a>
                        </li>
                        <li>
                            <a href="admin_user.php"><i class="fas fa-user-shield"></i> Data Admin</a>
                        </li>
                        <li>
                            <a href="jabatan.php"><i class="fas fa-briefcase"></i> Data Jabatan</a>
                        </li>
                        <li class="active">
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
                                <h4 class="font-weight-bold mb-0 text-dark">Persetujuan Pengajuan Cuti / Izin</h4>
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
                        
                        <?php if (isset($_SESSION['msg'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($_SESSION['msg']) ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php unset($_SESSION['msg']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['msg_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($_SESSION['msg_error']) ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php unset($_SESSION['msg_error']); ?>
                        <?php endif; ?>

                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0">Daftar Pengajuan Cuti / Izin</h5>
                                    <small class="text-muted">Setujui atau tolak izin dan cuti yang diajukan oleh karyawan</small>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-compact">
                                    <thead>
                                        <tr>
                                            <th style="width: 38px; text-align: center;">No</th>
                                            <th style="width: 17%;">Nama Karyawan</th>
                                            <th style="width: 80px; text-align: center;">Kategori</th>
                                            <th style="width: 16%;">Waktu Cuti / Izin</th>
                                            <th style="width: 22%;">Alasan / Keterangan</th>
                                            <th style="width: 14%;">Waktu Pengajuan</th>
                                            <th style="width: 85px; text-align: center;">Status</th>
                                            <th style="width: 135px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = $halaman_awal + 1;
                                        if (mysqli_num_rows($query_pengajuan) == 0):
                                        ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle mr-1"></i> Belum ada pengajuan cuti atau izin saat ini.
                                            </td>
                                        </tr>
                                        <?php
                                        endif;

                                        while ($row = mysqli_fetch_assoc($query_pengajuan)) {
                                            $status = $row['status'] ?? 'Proses';
                                            $badge_class = 'badge-proses';
                                            if ($status === 'Disetujui') {
                                                $badge_class = 'badge-disetujui';
                                            } elseif ($status === 'Ditolak') {
                                                $badge_class = 'badge-ditolak';
                                            }

                                            // Hitung waktu & durasi cuti/izin
                                            $tgl_m = !empty($row['tgl_mulai']) ? date('d/m/Y', strtotime($row['tgl_mulai'])) : '';
                                            $tgl_s = !empty($row['tgl_selesai']) ? date('d/m/Y', strtotime($row['tgl_selesai'])) : '';
                                            $durasi_info = '<span class="text-muted">-</span>';
                                            if (!empty($tgl_m) && !empty($tgl_s)) {
                                                $d1 = new DateTime($row['tgl_mulai']);
                                                $d2 = new DateTime($row['tgl_selesai']);
                                                $diff_hari = $d1->diff($d2)->days + 1;
                                                if ($tgl_m === $tgl_s) {
                                                    $durasi_info = "<span class='font-weight-bold d-block text-dark' style='font-size:0.78rem;'>$tgl_m</span><span class='badge badge-primary font-weight-bold' style='font-size:0.65rem;padding:1px 6px;'>1 Hari</span>";
                                                } else {
                                                    $durasi_info = "<span class='font-weight-bold d-block text-dark' style='font-size:0.75rem;line-height:1.2;'>$tgl_m - $tgl_s</span><span class='badge badge-primary font-weight-bold' style='font-size:0.65rem;padding:1px 6px;'>$diff_hari Hari</span>";
                                                }
                                            }

                                            // Format waktu pengajuan agar ringkas
                                            $waktu_raw = $row['waktu'] ?? '';
                                            $waktu_formatted = htmlspecialchars($waktu_raw);
                                            if (preg_match('/(\d{2}-\d{2}-\d{4})\s+(\d{2}:\d{2})/', $waktu_raw, $m)) {
                                                $waktu_formatted = "<span class='font-weight-bold d-block text-dark' style='font-size:0.78rem;'>{$m[1]}</span><span class='text-muted' style='font-size:0.7rem;'>{$m[2]} WIB</span>";
                                            } elseif (strtotime($waktu_raw)) {
                                                $waktu_formatted = "<span class='font-weight-bold d-block text-dark' style='font-size:0.78rem;'>" . date('d/m/Y', strtotime($waktu_raw)) . "</span><span class='text-muted' style='font-size:0.7rem;'>" . date('H:i', strtotime($waktu_raw)) . " WIB</span>";
                                            }
                                        ?>
                                        <tr>
                                            <td style="text-align: center; color:#6b7280; font-weight:600;"><?= $no++ ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-initial-compact mr-2"><?= strtoupper(substr($row['nama'], 0, 1)) ?></div>
                                                    <span class="font-weight-bold text-dark" style="font-size:0.8rem;"><?= htmlspecialchars($row['nama']) ?></span>
                                                </div>
                                            </td>
                                            <td style="text-align: center;"><span class="badge-modern badge-jabatan" style="font-size:0.7rem; padding:2px 7px;"><?= htmlspecialchars($row['keterangan']) ?></span></td>
                                            <td><?= $durasi_info ?></td>
                                            <td style="font-size: 0.78rem; line-height: 1.35; color: #374151; word-break: break-word;"><?= htmlspecialchars($row['alasan']) ?></td>
                                            <td><?= $waktu_formatted ?></td>
                                            <td style="text-align: center;"><span class="badge-modern <?= $badge_class ?>"><?= $status ?></span></td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <?php if ($status === 'Proses'): ?>
                                                    <a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-action-compact mr-1" onclick="return confirm('Setujui pengajuan ini?');" title="Setujui">
                                                        <i class="fas fa-check"></i> Setujui
                                                    </a>
                                                    <a href="?action=reject&id=<?= $row['id'] ?>" class="btn btn-danger btn-action-compact" onclick="return confirm('Tolak pengajuan ini?');" title="Tolak">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </a>
                                                <?php else: ?>
                                                    <a href="?action=reset&id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-action-compact" title="Reset ke Proses">
                                                        <i class="fas fa-undo"></i> Reset
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PAGINATION -->
                            <div class="mt-3 d-flex justify-content-center">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?halaman=<?= $previous ?>">Prev</a>
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
    </div>

    <!-- Scripts -->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
