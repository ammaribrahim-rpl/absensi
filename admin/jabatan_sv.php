<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';
if (isset($_POST['simpan'])) {
	$jabatan = $_POST['jabatan'];
}

$save = "INSERT INTO tb_jabatan (jabatan) VALUES (?)";
$stmt = mysqli_prepare($koneksi, $save);
mysqli_stmt_bind_param($stmt, "s", $jabatan);
$result = mysqli_stmt_execute($stmt);

if ($result) {
	header("location: datajabatan.php");
}else{
	echo "gagal disimpan";
}
 ?>