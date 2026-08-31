<?php
/**
 * migrate_v2.php — Migrasi Database v2
 * Jalankan SEKALI: http://localhost/absensi/db/migrate_v2.php
 */
include '../koneksi.php';

$errors = [];
$success = [];

// 1. Tambah kolom tipe_absen ke tb_absen
$check = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_absen LIKE 'tipe_absen'");
if (mysqli_num_rows($check) === 0) {
    $sql = "ALTER TABLE tb_absen ADD COLUMN tipe_absen ENUM('masuk','istirahat_mulai','istirahat_selesai','pulang') DEFAULT 'masuk' AFTER waktu";
    if (mysqli_query($koneksi, $sql)) {
        $success[] = "Kolom tipe_absen berhasil ditambahkan ke tb_absen";
    } else {
        $errors[] = "Gagal tambah tipe_absen: " . mysqli_error($koneksi);
    }
} else {
    $success[] = "Kolom tipe_absen sudah ada (dilewati)";
}

// 2. Tambah kolom is_telat ke tb_absen
$check2 = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_absen LIKE 'is_telat'");
if (mysqli_num_rows($check2) === 0) {
    $sql2 = "ALTER TABLE tb_absen ADD COLUMN is_telat TINYINT(1) DEFAULT 0 AFTER tipe_absen";
    if (mysqli_query($koneksi, $sql2)) {
        $success[] = "Kolom is_telat berhasil ditambahkan ke tb_absen";
    } else {
        $errors[] = "Gagal tambah is_telat: " . mysqli_error($koneksi);
    }
} else {
    $success[] = "Kolom is_telat sudah ada (dilewati)";
}

// 3. Update existing records: set tipe_absen='masuk' untuk data lama
mysqli_query($koneksi, "UPDATE tb_absen SET tipe_absen='masuk' WHERE tipe_absen IS NULL OR tipe_absen=''");
$success[] = "Data absen lama diupdate ke tipe_absen='masuk'";

// 4. Buat tabel tb_notifikasi
$check3 = mysqli_query($koneksi, "SHOW TABLES LIKE 'tb_notifikasi'");
if (mysqli_num_rows($check3) === 0) {
    $sql3 = "CREATE TABLE tb_notifikasi (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_karyawan VARCHAR(50) NOT NULL,
        nama VARCHAR(255) NOT NULL,
        pesan TEXT NOT NULL,
        tipe VARCHAR(50) DEFAULT 'info',
        dibaca TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_karyawan (id_karyawan),
        INDEX idx_dibaca (dibaca)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    if (mysqli_query($koneksi, $sql3)) {
        $success[] = "Tabel tb_notifikasi berhasil dibuat";
    } else {
        $errors[] = "Gagal buat tabel tb_notifikasi: " . mysqli_error($koneksi);
    }
} else {
    $success[] = "Tabel tb_notifikasi sudah ada (dilewati)";
}

// 5. Tambah kolom tgl_mulai dan tgl_selesai ke tb_keterangan
$check4 = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_keterangan LIKE 'tgl_mulai'");
if (mysqli_num_rows($check4) === 0) {
    if (mysqli_query($koneksi, "ALTER TABLE tb_keterangan ADD COLUMN tgl_mulai VARCHAR(50) NULL AFTER keterangan")) {
        $success[] = "Kolom tgl_mulai berhasil ditambahkan ke tb_keterangan";
    } else {
        $errors[] = "Gagal tambah tgl_mulai: " . mysqli_error($koneksi);
    }
} else {
    $success[] = "Kolom tgl_mulai sudah ada (dilewati)";
}

$check5 = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_keterangan LIKE 'tgl_selesai'");
if (mysqli_num_rows($check5) === 0) {
    if (mysqli_query($koneksi, "ALTER TABLE tb_keterangan ADD COLUMN tgl_selesai VARCHAR(50) NULL AFTER tgl_mulai")) {
        $success[] = "Kolom tgl_selesai berhasil ditambahkan ke tb_keterangan";
    } else {
        $errors[] = "Gagal tambah tgl_selesai: " . mysqli_error($koneksi);
    }
} else {
    $success[] = "Kolom tgl_selesai sudah ada (dilewati)";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Migrasi DB v2</title>
<style>
body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 20px; }
.ok { color: #16a34a; background: #dcfce7; padding: 8px 14px; border-radius: 6px; margin-bottom: 6px; }
.err { color: #b91c1c; background: #fee2e2; padding: 8px 14px; border-radius: 6px; margin-bottom: 6px; }
h2 { color: #1e2228; }
a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4f46e5; color: #fff; border-radius: 6px; text-decoration: none; }
</style>
</head>
<body>
<h2>🗄️ Migrasi Database v2</h2>
<?php foreach ($success as $s): ?>
    <div class="ok">✅ <?= htmlspecialchars($s) ?></div>
<?php endforeach; ?>
<?php foreach ($errors as $e): ?>
    <div class="err">❌ <?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<?php if (empty($errors)): ?>
    <p><strong>✅ Semua migrasi berhasil!</strong> Sistem siap digunakan.</p>
<?php else: ?>
    <p><strong>⚠️ Ada error pada migrasi. Periksa pesan di atas.</strong></p>
<?php endif; ?>
<a href="../karyawan/awal.php">← Kembali ke Portal Karyawan</a>
</body>
</html>
