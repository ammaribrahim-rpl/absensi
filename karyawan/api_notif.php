<?php
/**
 * karyawan/api_notif.php — Polling API Notifikasi (JSON)
 * Dipanggil via AJAX setiap 30 detik dari awal.php
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store');

if (empty($_SESSION['idsi'])) {
    echo json_encode(['count' => 0, 'items' => []]);
    exit;
}

include '../koneksi.php';
$id_karyawan = $_SESSION['idsi'];

// Tandai dibaca jika action=read_all
if (isset($_GET['action']) && $_GET['action'] === 'read_all') {
    $stmt = mysqli_prepare($koneksi, "UPDATE tb_notifikasi SET dibaca=1 WHERE id_karyawan=?");
    mysqli_stmt_bind_param($stmt, 's', $id_karyawan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true]);
    exit;
}

// Ambil notifikasi belum dibaca
$stmt = mysqli_prepare($koneksi,
    "SELECT id, pesan, tipe, created_at FROM tb_notifikasi
     WHERE id_karyawan = ? AND dibaca = 0
     ORDER BY created_at DESC LIMIT 10");
mysqli_stmt_bind_param($stmt, 's', $id_karyawan);
mysqli_stmt_execute($stmt);
$res   = mysqli_stmt_get_result($stmt);
$items = [];
while ($row = mysqli_fetch_assoc($res)) {
    $items[] = [
        'id'         => (int) $row['id'],
        'pesan'      => $row['pesan'],
        'tipe'       => $row['tipe'],
        'created_at' => $row['created_at'],
    ];
}
mysqli_stmt_close($stmt);

echo json_encode(['count' => count($items), 'items' => $items]);
