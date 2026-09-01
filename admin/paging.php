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
if ($total_halaman < 1) $total_halaman = 1;

$stmt_karyawan = mysqli_prepare($koneksi, "SELECT * FROM tb_karyawan ORDER BY id_karyawan ASC LIMIT ?, ?");
mysqli_stmt_bind_param($stmt_karyawan, "ii", $halaman_awal, $batas);
mysqli_stmt_execute($stmt_karyawan);
$data_karyawan = mysqli_stmt_get_result($stmt_karyawan);

$nomor = $halaman_awal + 1;

// Avatar color palette (same as owner)
$avatar_colors = [
    ['bg' => '#eef2ff', 'color' => '#4f46e5'],
    ['bg' => '#ede9fe', 'color' => '#6d28d9'],
    ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
    ['bg' => '#dcfce7', 'color' => '#166534'],
    ['bg' => '#fef3c7', 'color' => '#92400e'],
    ['bg' => '#e0f2fe', 'color' => '#075985'],
    ['bg' => '#f3e8ff', 'color' => '#7e22ce'],
];

while ($row = mysqli_fetch_assoc($data_karyawan)) {
    $masa_kerja = hitungMasaKerja($row);
    $tgl_masuk_fmt = getFormattedTglMasuk($row);
    $initial = strtoupper(substr($row['nama'], 0, 1));
    $av = $avatar_colors[($nomor - 1) % count($avatar_colors)];
?>
<tr>
    <td class="text-center font-weight-bold text-muted" style="font-size:0.75rem;"><?= $nomor ?></td>
    <td>
        <div class="d-flex align-items-center">
            <div class="avatar-initial-compact mr-2" style="background:<?= $av['bg'] ?>; color:<?= $av['color'] ?>;"><?= $initial ?></div>
            <div>
                <div class="font-weight-bold" style="font-size:0.82rem; color:#1e2228;"><?= htmlspecialchars($row['nama']) ?></div>
                <div class="text-muted" style="font-size:0.71rem;">@<?= htmlspecialchars($row['username']) ?></div>
            </div>
        </div>
    </td>
    <td>
        <span class="badge-modern badge-jabatan">
            <i class="<?= htmlspecialchars(getJabatanIcon($row['jabatan'])) ?> mr-1" style="font-size:0.72rem;"></i>
            <?= htmlspecialchars($row['jabatan']) ?>
        </span>
    </td>
    <td class="text-center">
        <span class="badge-modern" style="background:<?= ($row['jenkel'] == 'Perempuan') ? '#fce7f3' : '#dbeafe' ?>; color:<?= ($row['jenkel'] == 'Perempuan') ? '#9d174d' : '#1d4ed8' ?>; border:1px solid <?= ($row['jenkel'] == 'Perempuan') ? '#fbcfe8' : '#bfdbfe' ?>;">
            <i class="fas <?= ($row['jenkel'] == 'Perempuan') ? 'fa-venus' : 'fa-mars' ?> mr-1"></i><?= htmlspecialchars($row['jenkel']) ?>
        </span>
    </td>
    <td style="font-size:0.79rem; color:#374151;"><?= htmlspecialchars($row['no_tel'] ?: '—') ?></td>
    <td>
        <span style="font-size:0.78rem; color:#374151;">
            <i class="fas fa-calendar-alt text-muted mr-1" style="font-size:0.7rem;"></i><?= $tgl_masuk_fmt ?>
        </span>
    </td>
    <td>
        <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; font-size:0.72rem; padding:3px 8px; border-radius:6px;">
            <i class="fas fa-business-time mr-1"></i><?= $masa_kerja ?>
        </span>
    </td>
    <td class="text-center">
        <a href="karyawan_edit.php?id_karyawan=<?= urlencode($row['id_karyawan']) ?>" class="btn btn-warning btn-action-compact mr-1" title="Edit">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="hapus.php?id_karyawan=<?= urlencode($row['id_karyawan']) ?>" class="btn btn-danger btn-action-compact" onclick="return confirm('Yakin ingin menghapus data <?= htmlspecialchars(addslashes($row['nama'])) ?>?');" title="Hapus">
            <i class="fas fa-trash"></i>
        </a>
    </td>
</tr>
<?php
    $nomor++;
}
?>
