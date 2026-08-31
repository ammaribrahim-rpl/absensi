<?php
/**
 * includes/whatsapp.php — Fonnte WhatsApp Notification Helper
 *
 * CARA MENDAPATKAN API KEY FONNTE:
 * 1. Daftar di https://fonnte.com (gratis)
 * 2. Tambah device WhatsApp kamu sebagai pengirim
 * 3. Copy API Token dari dashboard → tempel di FONNTE_TOKEN di bawah
 *
 * FORMAT NOMOR: 628xxxxxxx (tanpa + atau 0 di depan)
 */

if (!defined('FONNTE_TOKEN')) {
    define('FONNTE_TOKEN', 'ISI_API_TOKEN_FONNTE_DISINI'); // ← Ganti dengan token kamu
}

/**
 * Format nomor HP ke format internasional (628xxx)
 */
function formatNomorWA(string $nomor): string {
    $nomor = preg_replace('/[^0-9]/', '', $nomor);
    if (empty($nomor)) return '';
    if (str_starts_with($nomor, '0')) {
        $nomor = '62' . substr($nomor, 1);
    } elseif (!str_starts_with($nomor, '62')) {
        $nomor = '62' . $nomor;
    }
    return $nomor;
}

/**
 * Kirim pesan WhatsApp via Fonnte API
 * @return array ['success' => bool, 'response' => string]
 */
function kirimWA(string $nomor, string $pesan): array {
    $nomor = formatNomorWA($nomor);
    if (empty($nomor) || strlen($nomor) < 10) {
        return ['success' => false, 'response' => 'Nomor tidak valid'];
    }
    if (FONNTE_TOKEN === 'ISI_API_TOKEN_FONNTE_DISINI') {
        // Mode simulasi: log ke file jika token belum diisi
        $log = date('Y-m-d H:i:s') . " | WA ke $nomor: $pesan\n";
        file_put_contents(__DIR__ . '/../db/wa_log.txt', $log, FILE_APPEND);
        return ['success' => true, 'response' => 'SIMULATED (token belum diisi)'];
    }

    $ch = curl_init('https://api.fonnte.com/send');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Authorization: ' . FONNTE_TOKEN],
        CURLOPT_POSTFIELDS     => [
            'target'  => $nomor,
            'message' => $pesan,
        ],
        CURLOPT_TIMEOUT => 10,
    ]);
    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['success' => false, 'response' => "cURL error: $err"];
    }
    $decoded = json_decode($response, true);
    $ok = isset($decoded['status']) && $decoded['status'] === true;
    return ['success' => $ok, 'response' => $response];
}

/**
 * Notifikasi WA: Approval / Penolakan Cuti
 */
function notifApprovalWA(string $nomor, string $nama_karyawan, string $jenis, string $status_baru, string $periode = ''): array {
    $emoji  = $status_baru === 'Disetujui' ? '✅' : ($status_baru === 'Ditolak' ? '❌' : '🔄');
    $tgl    = date('d/m/Y H:i');
    $info_periode = !empty($periode) ? "Periode: *$periode*\n" : "";
    $pesan  = "$emoji *NOTIFIKASI ABSENSI*\n\n"
            . "Halo *$nama_karyawan*,\n\n"
            . "Pengajuan *$jenis* kamu telah diperbarui.\n"
            . $info_periode
            . "Status: *$status_baru*\n"
            . "Waktu Pembaruan: $tgl\n\n"
            . "Silakan cek portal absensi untuk detail lebih lanjut.\n"
            . "_— Tim HR & Manajemen_";
    return kirimWA($nomor, $pesan);
}

/**
 * Notifikasi WA: Karyawan Terlambat Masuk
 */
function notifTelatMasukWA(string $nomor, string $nama, string $jam_absen): array {
    $tgl   = date('d/m/Y');
    $pesan = "⏰ *PERINGATAN KETERLAMBATAN*\n\n"
           . "Halo *$nama*,\n\n"
           . "Kamu tercatat *TERLAMBAT* masuk kerja hari ini.\n"
           . "Tanggal: $tgl\n"
           . "Waktu Absen: *$jam_absen*\n"
           . "Batas Masuk: 08:00 WIB\n\n"
           . "Keterlambatan ini akan dicatat dalam rekap kehadiran.\n"
           . "_— Sistem Absensi_";
    return kirimWA($nomor, $pesan);
}

/**
 * Notifikasi WA: Karyawan Terlambat Kembali dari Istirahat
 */
function notifTelatIstirahatWA(string $nomor, string $nama, int $durasi_menit): array {
    $lebih = $durasi_menit - 60;
    $tgl   = date('d/m/Y H:i');
    $pesan = "🍽️ *PERINGATAN ISTIRAHAT MELEBIHI BATAS*\n\n"
           . "Halo *$nama*,\n\n"
           . "Kamu melebihi batas waktu istirahat 1 jam.\n"
           . "Durasi istirahat: *$durasi_menit menit* (lebih $lebih menit)\n"
           . "Waktu kembali: $tgl\n\n"
           . "Keterlambatan ini akan dicatat dalam rekap kehadiran.\n"
           . "_— Sistem Absensi_";
    return kirimWA($nomor, $pesan);
}
