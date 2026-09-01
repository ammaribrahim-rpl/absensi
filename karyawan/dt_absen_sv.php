<?php
/**
 * karyawan/dt_absen_sv.php — Proses Simpan Absensi
 * Menangani tipe: masuk, istirahat_mulai, istirahat_selesai, pulang, reset_test
 */
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: login_karyawan.php');
    exit;
}

date_default_timezone_set('Asia/Jakarta');
include '../koneksi.php';

if (empty($_POST['tipe_absen'])) {
    header('location: index.php?m=awal');
    exit;
}

$id_karyawan = trim($_POST['id_karyawan'] ?? '');
$nama        = trim($_POST['nama'] ?? '');
$tipe        = trim($_POST['tipe_absen'] ?? 'masuk'); // masuk|istirahat_mulai|istirahat_selesai|pulang|reset_test

$waktu_now    = date('l, d-m-Y H:i:s');
$jam_now      = (int) date('H');
$menit_now    = (int) date('i');
$total_menit  = $jam_now * 60 + $menit_now;
$tanggal_hari = date('d-m-Y');

// ─── Ambil data jabatan karyawan ─────────────────────────────────────────────
$jabatan_karyawan = '';
$stmt_jab = mysqli_prepare($koneksi, "SELECT jabatan FROM tb_karyawan WHERE id_karyawan = ? LIMIT 1");
if ($stmt_jab) {
    mysqli_stmt_bind_param($stmt_jab, 's', $id_karyawan);
    mysqli_stmt_execute($stmt_jab);
    $res_jab = mysqli_stmt_get_result($stmt_jab);
    if ($row_j = mysqli_fetch_assoc($res_jab)) {
        $jabatan_karyawan = $row_j['jabatan'] ?? '';
    }
    mysqli_stmt_close($stmt_jab);
}

$is_k1       = (strtoupper(trim($jabatan_karyawan)) === 'K1');
$is_operator = (strtoupper(trim($jabatan_karyawan)) === 'OPERATOR');

// ─── FITUR KHUSUS OPERATOR (Reset Testing) ──────────────────────────────────
if ($tipe === 'reset_test' && $is_operator) {
    $stmt_del = mysqli_prepare($koneksi, "DELETE FROM tb_absen WHERE id_karyawan = ? AND waktu LIKE ?");
    if ($stmt_del) {
        $like_tgl = "%$tanggal_hari%";
        mysqli_stmt_bind_param($stmt_del, 'ss', $id_karyawan, $like_tgl);
        mysqli_stmt_execute($stmt_del);
        mysqli_stmt_close($stmt_del);
    }
    $_SESSION['flash_absen'] = [
        'success' => true,
        'label'   => 'Reset Presensi Testing',
        'waktu'   => date('H:i:s'),
        'is_telat'=> 0,
        'message' => 'Data presensi hari ini berhasil di-reset untuk testing.'
    ];
    header('location: index.php?m=awal');
    exit;
}

// ─── Deteksi Keterlambatan ────────────────────────────────────────────────────
$is_telat         = 0;
$durasi_istirahat = 0;

if ($tipe === 'masuk') {
    // Telat jika absen setelah 08:00 (Kecuali Mode Testing Operator)
    $batas_masuk = 8 * 60; // 08:00 = 480 menit
    if ($total_menit > $batas_masuk && !$is_operator) {
        $is_telat = 1;
    }
} elseif ($tipe === 'istirahat_selesai') {
    // Hitung durasi istirahat dari waktu mulai istirahat hari ini
    $stmt_ist = mysqli_prepare($koneksi,
        "SELECT waktu FROM tb_absen
         WHERE id_karyawan = ? AND tipe_absen = 'istirahat_mulai'
           AND waktu LIKE ?
         ORDER BY id DESC LIMIT 1");
    if ($stmt_ist) {
        $like_tgl = "%$tanggal_hari%";
        mysqli_stmt_bind_param($stmt_ist, 'ss', $id_karyawan, $like_tgl);
        mysqli_stmt_execute($stmt_ist);
        $res_ist = mysqli_stmt_get_result($stmt_ist);
        if ($row_ist = mysqli_fetch_assoc($res_ist)) {
            $ts_mulai = parseWaktuToTimestamp($row_ist['waktu']);
            $ts_now   = time();
            $durasi_istirahat = (int) round(($ts_now - $ts_mulai) / 60);
            
            // Batas istirahat: K1 = 90 menit (1 jam 30 min), Jabatan lain = 60 menit (1 jam)
            $max_istirahat = $is_k1 ? 90 : 60;
            if ($durasi_istirahat > $max_istirahat && !$is_operator) {
                $is_telat = 1;
            }
        }
        mysqli_stmt_close($stmt_ist);
    }
}

// ─── Simpan ke Database ────────────────────────────────────────────────────────
$stmt_save = mysqli_prepare($koneksi,
    "INSERT INTO tb_absen (id_karyawan, nama, waktu, tipe_absen, is_telat) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt_save, 'ssssi', $id_karyawan, $nama, $waktu_now, $tipe, $is_telat);
$result = mysqli_stmt_execute($stmt_save);
mysqli_stmt_close($stmt_save);

if (!$result) {
    $_SESSION['flash_absen'] = [
        'success' => false,
        'message' => 'Gagal menyimpan absensi. Silakan coba lagi.'
    ];
    header('location: index.php?m=awal');
    exit;
}

// ─── Simpan Notifikasi Internal jika Telat ─────────────────────────────────────
if ($is_telat) {
    $jam_str = date('H:i') . ' WIB';

    if ($tipe === 'masuk') {
        $pesan_notif = "Kamu tercatat TERLAMBAT masuk pada $jam_str. Harap tepat waktu di hari berikutnya.";
        $tipe_notif  = 'telat_masuk';
    } else {
        $batas_text  = $is_k1 ? '1 jam 30 menit' : '1 jam';
        $pesan_notif = "Kamu melebihi batas istirahat $batas_text ($durasi_istirahat menit). Tercatat terlambat kembali.";
        $tipe_notif  = 'telat_istirahat';
    }

    $stmt_notif = mysqli_prepare($koneksi,
        "INSERT INTO tb_notifikasi (id_karyawan, nama, pesan, tipe) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_notif, 'ssss', $id_karyawan, $nama, $pesan_notif, $tipe_notif);
    mysqli_stmt_execute($stmt_notif);
    mysqli_stmt_close($stmt_notif);
}

// ─── Pesan Feedback ke User ────────────────────────────────────────────────────
$label_map = [
    'masuk'             => 'Absen Masuk',
    'istirahat_mulai'   => 'Mulai Istirahat',
    'istirahat_selesai' => 'Selesai Istirahat',
    'pulang'            => 'Absen Pulang',
];
$label = $label_map[$tipe] ?? 'Absen';
$batas_ist_label = $is_k1 ? '1 jam 30 menit' : '1 jam';
$telat_msg = ($is_telat && $tipe === 'masuk') ? ' (Terlambat)' : (($is_telat && $tipe === 'istirahat_selesai') ? " (Istirahat melebihi $batas_ist_label)" : '');

$_SESSION['flash_absen'] = [
    'success'   => true,
    'label'     => $label,
    'tipe'      => $tipe,
    'is_telat'  => (int) $is_telat,
    'telat_msg' => $telat_msg,
    'waktu'     => date('H:i:s')
];

header('location: index.php?m=awal');
exit;