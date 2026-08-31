<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';

if (isset($_POST['simpan'])) {
	$username = $_POST['username'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
}

$save = "INSERT INTO tb_daftar (username, password) VALUES (?, ?)";
$stmt = mysqli_prepare($koneksi, $save);
mysqli_stmt_bind_param($stmt, "ss", $username, $password);
$result = mysqli_stmt_execute($stmt);

if ($result) {
	header("location: datauser.php");
}else{
	echo "gagal disimpan";
}
 ?>