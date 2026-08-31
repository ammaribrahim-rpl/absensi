<?php
include __DIR__ . '/../koneksi.php';

$res = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_keterangan LIKE 'status'");
if (mysqli_num_rows($res) == 0) {
    $alter = mysqli_query($koneksi, "ALTER TABLE tb_keterangan ADD COLUMN status VARCHAR(50) DEFAULT 'Proses'");
    if ($alter) {
        echo "Column 'status' added successfully to tb_keterangan.\n";
    } else {
        echo "Failed to add column 'status': " . mysqli_error($koneksi) . "\n";
    }
} else {
    echo "Column 'status' already exists in tb_keterangan.\n";
}
?>
