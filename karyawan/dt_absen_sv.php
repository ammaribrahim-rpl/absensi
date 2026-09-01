<?php
/**
 * karyawan/dt_absen_sv.php — Proses Simpan Absensi
 * Menangani 4 tipe: masuk, istirahat_mulai, istirahat_selesai, pulang
 */
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: login_karyawan.php');
    exit;
}

date_default_timezone_set('Asia/Jakarta');
include '../koneksi.php';
include '../includes/whatsapp.php';

if (empty($_POST['tipe_absen'])) {
    header('location: index.php?m=awal');
    exit;
}

$id_karyawan = trim($_POST['id_karyawan'] ?? '');
$nama        = trim($_POST['nama'] ?? '');
$tipe        = trim($_POST['tipe_absen'] ?? 'masuk'); // masuk|istirahat_mulai|istirahat_selesai|pulang

$waktu_now   = date('l, d-m-Y H:i:s');
$jam_now     = (int) date('H');
$menit_now   = (int) date('i');
$total_menit = $jam_now * 60 + $menit_now;
$tanggal_hari = date('d-m-Y');

// Ambil nomor HP karyawan untuk WA
$no_hp = '';
$stmt_hp = mysqli_prepare($koneksi, "SELECT no_tel FROM tb_karyawan WHERE id_karyawan = ? LIMIT 1");
if ($stmt_hp) {
    mysqli_stmt_bind_param($stmt_hp, 's', $id_karyawan);
    mysqli_stmt_execute($stmt_hp);
    $res_hp = mysqli_stmt_get_result($stmt_hp);
    if ($row_hp = mysqli_fetch_assoc($res_hp)) {
        $no_hp = $row_hp['no_tel'] ?? '';
    }
    mysqli_stmt_close($stmt_hp);
}

// ─── Deteksi Keterlambatan ────────────────────────────────────────────────────
$is_telat    = 0;
$durasi_istirahat = 0;

if ($tipe === 'masuk') {
    // Telat jika absen setelah 08:00
    $batas_masuk = 8 * 60; // 08:00 = 480 menit
    if ($total_menit > $batas_masuk) {
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
            if ($durasi_istirahat > 60) {
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

// ─── Kirim Notifikasi jika Telat ──────────────────────────────────────────────
if ($is_telat && !empty($no_hp)) {
    $jam_str = date('H:i') . ' WIB';

    if ($tipe === 'masuk') {
        notifTelatMasukWA($no_hp, $nama, $jam_str);
        $pesan_notif = "Kamu tercatat TERLAMBAT masuk pada $jam_str. Harap tepat waktu di hari berikutnya.";
        $tipe_notif  = 'telat_masuk';
    } else {
        $pesan_notif = "Kamu melebihi batas istirahat 1 jam ($durasi_istirahat menit). Tercatat terlambat kembali.";
        $tipe_notif  = 'telat_istirahat';
        notifTelatIstirahatWA($no_hp, $nama, $durasi_istirahat);
    }

    // Simpan ke tb_notifikasi
    $stmt_notif = mysqli_prepare($koneksi,
        "INSERT INTO tb_notifikasi (id_karyawan, nama, pesan, tipe) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_notif, 'ssss', $id_karyawan, $nama, $pesan_notif, $tipe_notif);
    mysqli_stmt_execute($stmt_notif);
    mysqli_stmt_close($stmt_notif);
}

// ─── Pesan Feedback ke User ────────────────────────────────────────────────────
$label_map = [
    'masuk'               => 'Absen Masuk',
    'istirahat_mulai'     => 'Mulai Istirahat',
    'istirahat_selesai'   => 'Selesai Istirahat',
    'pulang'              => 'Absen Pulang',
];
$label = $label_map[$tipe] ?? 'Absen';
$telat_msg = ($is_telat && $tipe === 'masuk') ? ' (Terlambat)' : (($is_telat && $tipe === 'istirahat_selesai') ? ' (Istirahat melebihi 1 jam)' : '');

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