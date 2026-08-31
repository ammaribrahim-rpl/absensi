<?php
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: login_karyawan.php');
    exit;
}
include '../koneksi.php';

if (isset($_POST['simpan'])) {
    $id_karyawan = $_POST['id_karyawan'];
    $nama = $_POST['nama'];
    $waktu = $_POST['waktu'];

    $save = "INSERT INTO tb_absen (id_karyawan, nama, waktu) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $save);
    mysqli_stmt_bind_param($stmt, "sss", $id_karyawan, $nama, $waktu);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "<script>alert('Anda sudah absen untuk hari ini') </script>";
        echo "<script>window.location.href = \"index.php?m=awal\" </script>";	
    } else {
        echo "Gagal menyimpan data absen";
    }
}
?>