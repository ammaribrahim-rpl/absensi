<?php 
include '../koneksi.php';

$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$previous = $halaman - 1;
$next = $halaman + 1;

$data = mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_karyawan");
$jumlah_data = mysqli_fetch_row($data)[0] ?? 0;
$total_halaman = ceil($jumlah_data / $batas);

$stmt_karyawan = mysqli_prepare($koneksi, "SELECT * FROM tb_karyawan ORDER BY id_karyawan ASC LIMIT ?, ?");
mysqli_stmt_bind_param($stmt_karyawan, "ii", $halaman_awal, $batas);
mysqli_stmt_execute($stmt_karyawan);
$data_karyawan = mysqli_stmt_get_result($stmt_karyawan);

$nomor = $halaman_awal + 1;

while ($row = mysqli_fetch_assoc($data_karyawan)) {
    $masa_kerja = hitungMasaKerja($row['id_karyawan']);
?>
<tr>
    <td class="font-weight-bold" style="color: #2563eb;"><?= htmlspecialchars($row['id_karyawan']) ?></td>
    <td><span class="badge badge-info" style="font-size: 0.85rem; padding: 5px 9px;"><?= $masa_kerja ?></span></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>
    <td><span class="badge-modern bg-light text-dark border"><?= htmlspecialchars($row['jenkel']) ?></span></td>
    <td><?= htmlspecialchars($row['no_tel']) ?></td>
    <td><span class="badge-modern badge-jabatan"><i class="<?= htmlspecialchars(getJabatanIcon($row['jabatan'])) ?> mr-1"></i><?= htmlspecialchars($row['jabatan']) ?></span></td>
    <td>
        <a href="karyawan_edit.php?id_karyawan=<?= urlencode($row['id_karyawan']) ?>" class="btn btn-primary btn-sm">
            <i class="fa fa-edit"></i> Ubah
        </a>
        <a href="hapus.php?id_karyawan=<?= urlencode($row['id_karyawan']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data <?= htmlspecialchars(addslashes($row['nama'])) ?>?');">
            <i class="fa fa-trash"></i> Hapus
        </a>
    </td>
</tr>
<?php } ?>
