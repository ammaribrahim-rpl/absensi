<?php 
session_start();
if (empty($_SESSION['idsi'])) {
    header('location: ../../login_karyawan.php');
    exit;
}
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $id_karyawan = trim($_POST['id_karyawan'] ?? '');
    $nama        = trim($_POST['nama'] ?? '');
    $keterangan  = trim($_POST['keterangan'] ?? 'Izin');
    $tgl_mulai   = trim($_POST['tgl_mulai'] ?? date('Y-m-d'));
    $tgl_selesai = trim($_POST['tgl_selesai'] ?? date('Y-m-d'));
    $alasan      = trim($_POST['alasan'] ?? '-');
    $waktu       = trim($_POST['waktu'] ?? date('l, d-m-Y H:i:s'));
    $bukti       = '-';
    $status      = 'Proses';

    if (empty($id_karyawan) || empty($nama) || empty($alasan)) {
        echo "<script>alert('Harap lengkapi semua kolom.'); window.history.back();</script>";
        exit;
    }

    $query = "INSERT INTO tb_keterangan (id_karyawan, nama, keterangan, tgl_mulai, tgl_selesai, alasan, waktu, bukti, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt  = mysqli_prepare($koneksi, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssssss", $id_karyawan, $nama, $keterangan, $tgl_mulai, $tgl_selesai, $alasan, $waktu, $bukti, $status);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($result) {
            echo "<script>alert('Pengajuan " . htmlspecialchars($keterangan) . " Anda (" . htmlspecialchars($tgl_mulai) . " s/d " . htmlspecialchars($tgl_selesai) . ") berhasil dikirim.');</script>";
            echo '<script>window.location.href = "../../index.php?m=awal";</script>';
            exit;
        }
    }

    echo "<script>alert('Gagal mengirim pengajuan. Silakan coba lagi.'); window.history.back();</script>";
    exit;
}
?>