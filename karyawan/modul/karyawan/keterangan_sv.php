<?php 
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: ../../login_karyawan.php');
    exit;
}
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $id_karyawan = $_POST['id_karyawan'];
    $nama = $_POST['nama'];
    $keterangan = $_POST['keterangan'];
    $alasan = $_POST['alasan'];
    $waktu = $_POST['waktu'];
    $bukti = '-'; // Upload file ditiadakan untuk kecepatan & kemudahan

    $query = "INSERT INTO tb_keterangan (id_karyawan, nama, keterangan, alasan, waktu, bukti) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ssssss", $id_karyawan, $nama, $keterangan, $alasan, $waktu, $bukti);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($result) {
        echo "<script>alert('Pengajuan ".htmlspecialchars($keterangan)." Anda berhasil dikirim');</script>";
        echo '<script>window.location.href = "../../index.php?m=awal";</script>';
        exit;
    } else {
        echo "<script>alert('Gagal mengirim keterangan'); window.history.back();</script>";
        exit;
    }
}
?>