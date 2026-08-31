<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';

$id = $_GET['id'];
if (!filter_var($id, FILTER_VALIDATE_INT)) {
    echo "ID tidak valid";
    exit;
}

$sql_h = "DELETE FROM tb_jabatan WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $sql_h);
mysqli_stmt_bind_param($stmt, "i", $id);
$hapus = mysqli_stmt_execute($stmt);

if ($hapus) {
         header("location: datajabatan.php");
} else {
  echo "Gagal Dihapus";
}
 ?>
