<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';

$id = $_GET['id'] ?? '';
if (!filter_var($id, FILTER_VALIDATE_INT)) {
    echo "ID tidak valid";
    exit;
}

$sql_h = "DELETE FROM tb_daftar WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $sql_h);
mysqli_stmt_bind_param($stmt, "i", $id);
$query = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($query) {
    header("location: datauser.php");
} else {
    echo "gagal dihapus";
}
?>
