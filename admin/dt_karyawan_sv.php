<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
    exit;
}
include '../koneksi.php';

if (isset($_POST['simpan'])) {
    $tgl_masuk   = trim($_POST['id_karyawan']); // nilai asli tanggal masuk yang diinput
    $username    = trim($_POST['username']);
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama        = trim($_POST['nama']);
    $tmp_tgl_lahir = '';
    $jenkel      = trim($_POST['jenkel'] ?? 'Laki-laki');
    $agama       = '';
    $alamat      = '';
    $no_tel      = trim($_POST['no_tel'] ?? '');
    $jabatan     = trim($_POST['jabatan'] ?? '');
    $foto        = '';

    // Generate id_karyawan unik berbasis tanggal masuk + suffix random
    // sehingga beberapa karyawan bisa punya tanggal masuk yang sama
    $id_karyawan = $tgl_masuk;
    $cek_stmt = mysqli_prepare($koneksi, "SELECT id_karyawan FROM tb_karyawan WHERE id_karyawan = ? LIMIT 1");
    $suffix = 0;
    while (true) {
        $id_cek = ($suffix === 0) ? $id_karyawan : $id_karyawan . '-' . $suffix;
        mysqli_stmt_bind_param($cek_stmt, "s", $id_cek);
        mysqli_stmt_execute($cek_stmt);
        $res = mysqli_stmt_get_result($cek_stmt);
        if (!mysqli_fetch_row($res)) {
            // ID ini belum terpakai, gunakan
            $id_karyawan = $id_cek;
            break;
        }
        $suffix++;
    }
    mysqli_stmt_close($cek_stmt);

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