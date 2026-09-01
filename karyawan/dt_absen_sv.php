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
// ─── Kirim Notifikasi jika Telat ──────────────────────────────────────────────
if ($is_telat) {
    $jam_str = date('H:i') . ' WIB';

    if ($tipe === 'masuk') {
        $pesan_notif = "Kamu tercatat TERLAMBAT masuk pada $jam_str. Harap tepat waktu di hari berikutnya.";
        $tipe_notif  = 'telat_masuk';
    } else {
        $pesan_notif = "Kamu melebihi batas istirahat 1 jam ($durasi_istirahat menit). Tercatat terlambat kembali.";
        $tipe_notif  = 'telat_istirahat';
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