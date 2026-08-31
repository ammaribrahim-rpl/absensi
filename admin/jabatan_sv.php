<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';

if (isset($_POST['simpan'])) {
    $jabatan = trim($_POST['jabatan'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fas fa-briefcase');
    if (empty($icon)) $icon = 'fas fa-briefcase';

    if (!empty($jabatan)) {
        $save = "INSERT INTO tb_jabatan (jabatan, icon) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $save);
        mysqli_stmt_bind_param($stmt, "ss", $jabatan, $icon);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("location: datajabatan.php");
    exit;
}

if (isset($_POST['update_jabatan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $jabatan = trim($_POST['jabatan'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fas fa-briefcase');
    if (empty($icon)) $icon = 'fas fa-briefcase';

    if (!empty($jabatan) && $id > 0) {
        $update = "UPDATE tb_jabatan SET jabatan = ?, icon = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $update);
        mysqli_stmt_bind_param($stmt, "ssi", $jabatan, $icon, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("location: datajabatan.php");
    exit;
}

header("location: datajabatan.php");
exit;
?>