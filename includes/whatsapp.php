<?php
/**
 * includes/whatsapp.php — WhatsApp Notification Helper (Disabled)
 */

if (!defined('FONNTE_TOKEN')) {
    define('FONNTE_TOKEN', '');
}

function formatNomorWA(string $nomor): string {
    return '';
}

function kirimWA(string $nomor, string $pesan): array {
    // WA Notification Disabled
    return ['success' => false, 'response' => 'WA Notification Disabled'];
}

function notifApprovalWA(string $nomor, string $nama_karyawan, string $jenis, string $status_baru, string $periode = ''): array {
    return ['success' => false, 'response' => 'WA Notification Disabled'];
}

function notifTelatMasukWA(string $nomor, string $nama, string $jam_absen): array {
    return ['success' => false, 'response' => 'WA Notification Disabled'];
}

function notifTelatIstirahatWA(string $nomor, string $nama, int $durasi_menit): array {
    return ['success' => false, 'response' => 'WA Notification Disabled'];
}
