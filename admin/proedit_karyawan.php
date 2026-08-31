<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';

if (isset($_POST['ubahdata'])) {
    $id_karyawan = trim($_POST['id_karyawan']);
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama']);
    $tmp_tgl_lahir = trim($_POST['tmp_tgl_lahir']);
    $jenkel = trim($_POST['jenkel']);
    $agama = trim($_POST['agama']);
    $alamat = trim($_POST['alamat']);
    $no_tel = trim($_POST['no_tel']);
    $jabatan = trim($_POST['jabatan']);

    $has_password = !empty($_POST['password']);

    if ($has_password) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE tb_karyawan SET username=?, password=?, nama=?, tmp_tgl_lahir=?, jenkel=?, agama=?, alamat=?, no_tel=?, jabatan=? WHERE id_karyawan=?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssss", $username, $password, $nama, $tmp_tgl_lahir, $jenkel, $agama, $alamat, $no_tel, $jabatan, $id_karyawan);
    } else {
        $sql = "UPDATE tb_karyawan SET username=?, nama=?, tmp_tgl_lahir=?, jenkel=?, agama=?, alamat=?, no_tel=?, jabatan=? WHERE id_karyawan=?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssss", $username, $nama, $tmp_tgl_lahir, $jenkel, $agama, $alamat, $no_tel, $jabatan, $id_karyawan);
    }

    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        echo "<script>alert('Ubah Data Dengan ID Karyawan = ".htmlspecialchars($id_karyawan)." Berhasil'); window.location.href = 'datakaryawan.php';</script>";
        exit;
    } else {
        echo "<script>alert('Maaf, Terjadi kesalahan saat mencoba untuk menyimpan data ke database.'); window.location.href = 'karyawan_edit.php?id_karyawan=".urlencode($id_karyawan)."';</script>";
        exit;
    }
}
?>
