<?php
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: login_karyawan.php');
    exit;
}
require_once __DIR__ . '/modul/karyawan/koneksi.php';
$nama        = $_SESSION['namasi'] ?? 'Karyawan';
$id_karyawan = $_SESSION['idsi']   ?? '';
$initial     = strtoupper(substr($nama, 0, 1));

// Tandai semua notifikasi sebagai dibaca
$stmt_mark = mysqli_prepare($koneksi, "UPDATE tb_notifikasi SET dibaca=1 WHERE id_karyawan=?");
mysqli_stmt_bind_param($stmt_mark, 's', $id_karyawan);
mysqli_stmt_execute($stmt_mark);
mysqli_stmt_close($stmt_mark);

// Ambil semua notifikasi (30 terbaru)
$notifikasi = [];
$stmt_all   = mysqli_prepare($koneksi,
    "SELECT id, pesan, tipe, dibaca, created_at FROM tb_notifikasi
     WHERE id_karyawan = ?
     ORDER BY created_at DESC LIMIT 30");
mysqli_stmt_bind_param($stmt_all, 's', $id_karyawan);
mysqli_stmt_execute($stmt_all);
$res_all = mysqli_stmt_get_result($stmt_all);
while ($row = mysqli_fetch_assoc($res_all)) {
    $notifikasi[] = $row;
}
mysqli_stmt_close($stmt_all);

// Hapus notif jika diminta
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $del_id = (int) $_GET['hapus'];
    $stmt_del = mysqli_prepare($koneksi, "DELETE FROM tb_notifikasi WHERE id=? AND id_karyawan=?");
    mysqli_stmt_bind_param($stmt_del, 'is', $del_id, $id_karyawan);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
    header('location: notifikasi.php');
    exit;
}

function tipeIcon(string $tipe): string {
    return match($tipe) {
        'telat_masuk'     => '<i class="fas fa-clock text-danger"></i>',
        'telat_istirahat' => '<i class="fas fa-utensils text-warning"></i>',
        'approval'        => '<i class="fas fa-check-circle text-success"></i>',
        'penolakan'       => '<i class="fas fa-times-circle text-danger"></i>',
        default           => '<i class="fas fa-bell text-primary"></i>',
    };
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Notifikasi — Portal Absensi</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">
    <link href="../css/font-face.css" rel="stylesheet">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
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
                    <li><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Izin / Cuti</a></li>
                    <li class="active"><a href="notifikasi.php"><i class="fas fa-bell"></i> Notifikasi</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- MENU SIDEBAR -->
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
                    <li><a href="index.php?m=karyawan&s=title"><i class="fas fa-file-medical"></i> Pengajuan Izin / Cuti</a></li>
                    <li class="active"><a href="notifikasi.php"><i class="fas fa-bell"></i> Notifikasi</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- PAGE CONTAINER -->
    <div class="page-container">
        <header class="header-desktop d-none d-lg-block" style="background:#fff;border-bottom:1px solid var(--color-border);box-shadow:var(--shadow-sm);">
            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="header-wrap">
                        <h4 class="font-weight-bold mb-0 text-dark">Notifikasi</h4>
                        <div class="d-flex align-items-center">
                            <div class="avatar-initial avatar-sm mr-2" style="background:linear-gradient(135deg,#10b981,#059669);"><?= $initial ?></div>
                            <span class="font-weight-bold text-dark"><?= htmlspecialchars($nama) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-content">
            <div class="section__content section__content--p30">
                <div class="container-fluid" style="max-width:720px;margin:0 auto;">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-bell mr-2 text-primary"></i>
                            Semua Notifikasi (<?= count($notifikasi) ?>)
                        </h5>
                        <a href="index.php?m=awal" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>

                    <?php if (empty($notifikasi)): ?>
                    <div class="card p-5 text-center">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3" style="opacity:0.3;"></i>
                        <p class="text-muted mb-0">Belum ada notifikasi.</p>
                    </div>
                    <?php else: ?>
                    <div class="card p-0" style="overflow:hidden;">
                        <?php foreach ($notifikasi as $i => $notif):
                            $border = ($i > 0) ? 'border-top:1px solid var(--color-border);' : '';
                        ?>
                        <div style="padding:16px 20px;<?= $border ?>">
                            <div class="d-flex align-items-start">
                                <div class="mr-3 mt-1" style="font-size:1.2rem;min-width:24px;">
                                    <?= tipeIcon($notif['tipe']) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1" style="font-size:0.875rem;line-height:1.5;"><?= nl2br(htmlspecialchars($notif['pesan'])) ?></p>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></small>
                                </div>
                                <a href="notifikasi.php?hapus=<?= $notif['id'] ?>"
                                   onclick="return confirm('Hapus notifikasi ini?')"
                                   class="ml-3 text-muted" style="font-size:0.8rem;" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>



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
<script src="../js/audio_notif.js"></script>
</body>
</html>
