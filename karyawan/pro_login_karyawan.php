<?php 
session_start();
require_once("../koneksi.php");

$username = trim($_POST['username'] ?? '');
$pass = trim($_POST['password'] ?? '');

if (empty($username) || empty($pass)) {
    header("location: login_karyawan.php?error=" . urlencode("Username dan password wajib diisi."));
    exit;
}

$sql = "SELECT * FROM tb_karyawan WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$login = mysqli_stmt_get_result($stmt);
$b = mysqli_fetch_assoc($login);
mysqli_stmt_close($stmt);

if ($b && password_verify($pass, $b['password'])) {
    $_SESSION['idsi']       = $b['id_karyawan'];
    $_SESSION['usersi']     = $b['username'];
    $_SESSION['namasi']     = $b['nama'];
    $_SESSION['ttlsi']      = $b['tmp_tgl_lahir'];
    $_SESSION['jenkelsi']   = $b['jenkel'];
    $_SESSION['agamasi']    = $b['agama'];
    $_SESSION['alamatsi']   = $b['alamat'];
    $_SESSION['teleponsi']  = $b['no_tel'];
    $_SESSION['jabatansi']  = $b['jabatan'];
    
    header("location: index.php?m=awal");
    exit;
} else {
    header("location: login_karyawan.php?error=" . urlencode("Username atau Password salah."));
    exit;
}
