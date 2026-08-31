<?php
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: ../../login_karyawan.php');
    exit;
}
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $id_karyawan = $_SESSION['idsi'];
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama']);
    $tmp_tgl_lahir = trim($_POST['tmp_tgl_lahir']);
    $jenkel = trim($_POST['jenkel']);
    $agama = trim($_POST['agama']);
    $alamat = trim($_POST['alamat']);
    $no_tel = trim($_POST['no_tel']);
    $jabatan = trim($_POST['jabatan']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql_d = "UPDATE tb_karyawan SET username=?, password=?, nama=?, tmp_tgl_lahir=?, jenkel=?, agama=?, alamat=?, no_tel=?, jabatan=? WHERE id_karyawan=?";
        $stmt_upd = mysqli_prepare($koneksi, $sql_d);
        mysqli_stmt_bind_param($stmt_upd, "ssssssssss", $username, $password_hashed, $nama, $tmp_tgl_lahir, $jenkel, $agama, $alamat, $no_tel, $jabatan, $id_karyawan);
    } else {
        $sql_d = "UPDATE tb_karyawan SET username=?, nama=?, tmp_tgl_lahir=?, jenkel=?, agama=?, alamat=?, no_tel=?, jabatan=? WHERE id_karyawan=?";
        $stmt_upd = mysqli_prepare($koneksi, $sql_d);
        mysqli_stmt_bind_param($stmt_upd, "sssssssss", $username, $nama, $tmp_tgl_lahir, $jenkel, $agama, $alamat, $no_tel, $jabatan, $id_karyawan);
    }

    $data = mysqli_stmt_execute($stmt_upd);
    mysqli_stmt_close($stmt_upd);

    if ($data) {
        // Perbarui session agar data di header langsung berubah
        $_SESSION['usersi'] = $username;
        $_SESSION['namasi'] = $nama;
        $_SESSION['ttlsi'] = $tmp_tgl_lahir;
        $_SESSION['jenkelsi'] = $jenkel;
        $_SESSION['agamasi'] = $agama;
        $_SESSION['alamatsi'] = $alamat;
        $_SESSION['teleponsi'] = $no_tel;
        $_SESSION['jabatansi'] = $jabatan;

        echo "<script>alert('Data Profil berhasil diperbarui');</script>";
        echo "<script>window.location.href = 'index.php?m=index';</script>";
        exit;
    } else {
        echo "<script>alert('Maaf, Terjadi kesalahan saat menyimpan data.'); window.history.back();</script>";
        exit;
    }
}
?>
