<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}

include '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = mysqli_prepare($koneksi, "DELETE FROM tb_absen WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$hapus = mysqli_stmt_execute($stmt);

if ($hapus) {
	header("location: ../admin/data_absen.php");
}else{
	echo "Gagal menghapus data absen";
} 
?>
