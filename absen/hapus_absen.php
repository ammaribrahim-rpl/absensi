<?php
/**
 * absen/hapus_absen.php — Hapus data absensi
 * Dapat dipanggil oleh Admin (session username) ATAU Owner (session owner_username)
 * Sekarang hapus sudah ditangani langsung di owner/laporan.php via GET params,
 * file ini tetap dipertahankan untuk kompatibilitas admin panel.
 */
session_start();

$is_admin = isset($_SESSION['username']);
$is_owner = isset($_SESSION['owner_username']);

if (!$is_admin && !$is_owner) {
    header('location: ../index.php');
    exit;
}

include '../koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('location: ' . ($is_owner ? '../owner/laporan.php' : '../admin/data_absen.php'));
    exit;
}

$stmt = mysqli_prepare($koneksi, "DELETE FROM tb_absen WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$hapus = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($hapus) {
    if ($is_owner) {
        $_SESSION['msg'] = "Data absensi berhasil dihapus.";
        header("location: ../owner/laporan.php");
    } else {
        header("location: ../admin/data_absen.php");
    }
} else {
    echo "Gagal menghapus data absen.";
}
