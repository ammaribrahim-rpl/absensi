<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';

$id = trim($_GET['id_karyawan'] ?? '');
if ($id === '') {
    echo "<script>alert('ID / Tanggal Masuk Karyawan tidak valid'); window.location.href = 'datakaryawan.php';</script>";
    exit;
}

$sql_h = "DELETE FROM tb_karyawan WHERE id_karyawan = ?";
$stmt_delete = mysqli_prepare($koneksi, $sql_h);
mysqli_stmt_bind_param($stmt_delete, "s", $id);
$hapus = mysqli_stmt_execute($stmt_delete);
mysqli_stmt_close($stmt_delete);

if ($hapus) {
    header("location: datakaryawan.php");
    exit;
} else {
    echo "<script>alert('Gagal menghapus data'); window.location.href = 'datakaryawan.php';</script>";
    exit;
}
?>
