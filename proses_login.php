<?php 
session_start();
require_once("koneksi.php");

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    header("location: login.php?error=" . urlencode("Username dan password wajib diisi."));
    exit;
}

$sql = "SELECT * FROM tb_daftar WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$hasil = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$hasil) {
    header("location: login.php?error=" . urlencode("Username belum terdaftar!"));
    exit;
}

if (!password_verify($password, $hasil['password'])) {
    header("location: login.php?error=" . urlencode("Password yang Anda masukkan salah!"));
    exit;
}

$_SESSION['username'] = $hasil['username'];
header('location: admin/admin.php');
exit;