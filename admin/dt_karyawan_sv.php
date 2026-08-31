<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';

if (isset($_POST['simpan'])) {
    $id_karyawan = trim($_POST['id_karyawan']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = trim($_POST['nama']);
    $tmp_tgl_lahir = '';
    $jenkel = trim($_POST['jenkel'] ?? 'Laki-laki');
    $agama = '';
    $alamat = '';
    $no_tel = trim($_POST['no_tel'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $foto = '';

    // Cek duplikasi Tanggal Masuk / ID
    $sql = "SELECT id_karyawan FROM tb_karyawan WHERE id_karyawan = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id_karyawan);
    mysqli_stmt_execute($stmt);
    $tambah = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_row($tambah)) {
        echo "<script>alert('Data Dengan Tanggal Masuk = ".htmlspecialchars($id_karyawan)." sudah ada');</script>";
        echo "<script>window.location.href = \"datakaryawan.php\";</script>";
        exit;
    }
    mysqli_stmt_close($stmt);

    // Simpan karyawan baru
    $query = "INSERT INTO tb_karyawan (id_karyawan, username, password, nama, tmp_tgl_lahir, jenkel, agama, alamat, no_tel, jabatan, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt2 = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt2, "sssssssssss", $id_karyawan, $username, $password, $nama, $tmp_tgl_lahir, $jenkel, $agama, $alamat, $no_tel, $jabatan, $foto);
    $success = mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    if ($success) {
        header("location: datakaryawan.php");
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan data karyawan'); window.location.href = 'datakaryawan.php';</script>";
        exit;
    }
}
?>