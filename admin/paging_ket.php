<?php 
include '../koneksi.php';

$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$previous = $halaman - 1;
$next = $halaman + 1;

$data = mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_keterangan");
$jumlah_data = mysqli_fetch_row($data)[0] ?? 0;
$total_halaman = ceil($jumlah_data / $batas);

$stmt_karyawan = mysqli_prepare($koneksi, "SELECT * FROM tb_keterangan ORDER BY id DESC LIMIT ?, ?");
mysqli_stmt_bind_param($stmt_karyawan, "ii", $halaman_awal, $batas);
mysqli_stmt_execute($stmt_karyawan);
$data_karyawan = mysqli_stmt_get_result($stmt_karyawan);

$nomor = $halaman_awal + 1;

while ($row = mysqli_fetch_assoc($data_karyawan)) {
    $initial = strtoupper(substr($row['nama'], 0, 1));
    $color_idx = (abs(crc32($row['nama'])) % 7) + 1;
    $avatar_class = "avatar-c" . $color_idx;
    $ket = strtolower($row['keterangan']);
    $badge_class = ($ket === 'sakit') ? 'badge-sakit' : 'badge-izin';
?>
<tr>
    <td class="font-weight-bold text-muted"><?= htmlspecialchars($row['id']) ?></td>
    <td class="font-weight-bold" style="color: #2563eb;"><?= htmlspecialchars($row['id_karyawan']) ?></td>
    <td>
        <div class="d-flex align-items-center">
            <div class="avatar-initial avatar-sm mr-2 <?= $avatar_class ?>"><?= $initial ?></div>
            <span class="font-weight-bold text-dark"><?= htmlspecialchars($row['nama']) ?></span>
        </div>
    </td>
    <td>
        <span class="badge-modern <?= $badge_class ?>">
            <?= htmlspecialchars($row['keterangan']) ?>
        </span>
    </td>
    <td><?= htmlspecialchars($row['alasan']) ?></td>
    <td>
        <i class="far fa-clock text-muted mr-1"></i>
        <?= htmlspecialchars($row['waktu']) ?>
    </td>
    <td class="text-center">
        <a href="absen/hapus_keterangan.php?id=<?= urlencode($row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data keterangan ini?');" title="Hapus">
            <i class="fas fa-trash mr-1"></i> Hapus
        </a>
    </td>
</tr>
<?php } ?>
