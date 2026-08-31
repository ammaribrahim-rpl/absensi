<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}

include '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt_h = mysqli_prepare($koneksi, "DELETE FROM tb_keterangan WHERE id = ?");
mysqli_stmt_bind_param($stmt_h, "i", $id);
$hapus = mysqli_stmt_execute($stmt_h);
mysqli_stmt_close($stmt_h);

if ($hapus) {
    header("location: ../data_keterangan.php");
    exit;
} else {
    echo "<script>alert('Gagal menghapus data keterangan'); window.location.href = '../data_keterangan.php';</script>";
    exit;
}
?>
