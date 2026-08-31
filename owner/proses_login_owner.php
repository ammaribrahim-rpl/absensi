<?php
session_start();
require_once("../koneksi.php");

// Otomatis buat tabel tb_owner jika belum ada
$create_table = "CREATE TABLE IF NOT EXISTS tb_owner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL
)";
mysqli_query($koneksi, $create_table);

// Cek apakah data owner default sudah ada, jika belum masukkan akun default
$check = mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_owner");
$count = mysqli_fetch_row($check)[0] ?? 0;
if ($count == 0) {
    $default_user = 'owner';
    $default_pass = password_hash('owner123', PASSWORD_DEFAULT);
    $default_nama = 'Owner Executive';
    $stmt_init = mysqli_prepare($koneksi, "INSERT INTO tb_owner (username, password, nama) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt_init, "sss", $default_user, $default_pass, $default_nama);
    mysqli_stmt_execute($stmt_init);
    mysqli_stmt_close($stmt_init);
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    header("location: login_owner.php?error=" . urlencode("Username dan password wajib diisi."));
    exit;
}

$sql = "SELECT * FROM tb_owner WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("location: login_owner.php?error=" . urlencode("Akun Owner tidak ditemukan."));
    exit;
}

if (!password_verify($password, $data['password'])) {
    header("location: login_owner.php?error=" . urlencode("Password yang Anda masukkan salah."));
    exit;
}

$_SESSION['owner_id'] = $data['id'];
$_SESSION['owner_username'] = $data['username'];
$_SESSION['owner_nama'] = $data['nama'];

header("location: dashboard.php");
exit;
