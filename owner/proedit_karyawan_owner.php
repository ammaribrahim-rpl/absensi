<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header('location: login_owner.php');
    exit;
}
include '../koneksi.php';

if (isset($_POST['ubahdata'])) {
    $id_karyawan = trim($_POST['id_karyawan']);
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama']);
    $jenkel = trim($_POST['jenkel'] ?? 'Laki-laki');
    $no_tel = trim($_POST['no_tel'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');

    $has_password = !empty($_POST['password']);

    if ($has_password) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE tb_karyawan SET username=?, password=?, nama=?, jenkel=?, no_tel=?, jabatan=? WHERE id_karyawan=?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "sssssss", $username, $password, $nama, $jenkel, $no_tel, $jabatan, $id_karyawan);
    } else {
        $sql = "UPDATE tb_karyawan SET username=?, nama=?, jenkel=?, no_tel=?, jabatan=? WHERE id_karyawan=?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $username, $nama, $jenkel, $no_tel, $jabatan, $id_karyawan);
    }

    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        header('location: karyawan.php?pesan=sukses_edit');
        exit;
    } else {
        header('location: karyawan_edit.php?id_karyawan=' . urlencode($id_karyawan) . '&error=1');
        exit;
    }
} else {
    header('location: karyawan.php');
    exit;
}
?>
