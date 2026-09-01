<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';

$type = $_GET['type'] ?? 'karyawan';
$date_now = date('Y-m-d_His');

if ($type === 'karyawan') {
    $filename = "Data_Karyawan_" . $date_now . ".xls";
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $query = mysqli_query($koneksi, "SELECT * FROM tb_karyawan ORDER BY nama ASC");
    ?>
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px; }
            th { background-color: #4f46e5; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #312e81; padding: 8px; }
            td { border: 1px solid #d1d5db; padding: 6px 8px; }
            .header-title { font-size: 16px; font-weight: bold; color: #1e1b4b; text-align: center; }
            .sub-title { font-size: 11px; color: #6b7280; text-align: center; margin-bottom: 12px; }
            .badge-pos { font-weight: bold; color: #4338ca; }
        </style>
    </head>
    <body>
        <div class="header-title">LAPORAN DATA KARYAWAN</div>
        <div class="sub-title">Diekspor oleh Owner pada: <?= date('d F Y H:i:s') ?> WIB</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th>Jenis Kelamin</th>
                    <th>No. Telepon</th>
                    <th>Tanggal Masuk</th>
                    <th>Masa Kerja</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($r = mysqli_fetch_assoc($query)) {
                    $tgl_format = getFormattedTglMasuk($r);
                    $masa_kerja = hitungMasaKerja($r);
                ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><b><?= htmlspecialchars($r['nama']) ?></b></td>
                    <td class="badge-pos"><?= htmlspecialchars($r['jabatan']) ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($r['jenkel']) ?></td>
                    <td style="mso-number-format:'\@';"><?= htmlspecialchars($r['no_tel']) ?></td>
                    <td style="text-align: center;"><?= $tgl_format ?></td>
                    <td style="text-align: center; font-weight: bold; color: #059669;"><?= $masa_kerja ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}

if ($type === 'admin') {
    $filename = "Data_Admin_" . $date_now . ".xls";
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $query = mysqli_query($koneksi, "SELECT * FROM tb_daftar ORDER BY id ASC");
    ?>
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px; }
            th { background-color: #059669; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #065f46; padding: 8px; }
            td { border: 1px solid #d1d5db; padding: 6px 8px; }
            .header-title { font-size: 16px; font-weight: bold; color: #064e3b; text-align: center; }
            .sub-title { font-size: 11px; color: #6b7280; text-align: center; margin-bottom: 12px; }
        </style>
    </head>
    <body>
        <div class="header-title">LAPORAN DATA AKUN ADMINISTRATOR</div>
        <div class="sub-title">Diekspor oleh Owner pada: <?= date('d F Y H:i:s') ?> WIB</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 80px;">ID User</th>
                    <th>Username Admin</th>
                    <th>Role / Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($r = mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td style="text-align: center;"><?= $r['id'] ?></td>
                    <td><b><?= htmlspecialchars($r['username']) ?></b></td>
                    <td style="text-align: center; color: #059669; font-weight: bold;">Administrator</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}

if ($type === 'rekap') {
    $filename = "Rekap_Kehadiran_" . $date_now . ".xls";
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $filter_kat = $_GET['kategori'] ?? 'semua';
    $filter_kar = $_GET['id_karyawan'] ?? 'semua';
    $filter_per = $_GET['periode'] ?? 'semua';

    $now = time();
    $cutoff = 0;
    if ($filter_per === '1pekan') {
        $cutoff = $now - (7 * 86400);
    } elseif ($filter_per === '1bulan') {
        $cutoff = $now - (30 * 86400);
    } elseif ($filter_per === '6bulan') {
        $cutoff = $now - (180 * 86400);
    } elseif ($filter_per === '1tahun') {
        $cutoff = $now - (365 * 86400);
    }

    $records = [];

    // Ambil Data Absensi (Hadir)
    if ($filter_kat === 'semua' || $filter_kat === 'absen') {
        $sql_absen = "SELECT * FROM tb_absen ORDER BY id DESC";
        $q_absen = mysqli_query($koneksi, $sql_absen);
        while ($ra = mysqli_fetch_assoc($q_absen)) {
            $ts = parseWaktuToTimestamp($ra['waktu']);
            if ($cutoff > 0 && $ts < $cutoff) continue;
            if ($filter_kar !== 'semua' && $ra['id_karyawan'] !== $filter_kar) continue;

            $records[] = [
                'timestamp'   => $ts,
                'id_karyawan' => $ra['id_karyawan'],
                'nama'        => $ra['nama'],
                'kategori'    => 'Hadir',
                'keterangan'  => 'Presensi Harian',
                'waktu'       => $ra['waktu']
            ];
        }
    }

    // Ambil Data Keterangan (Izin & Cuti)
    if ($filter_kat === 'semua' || $filter_kat === 'izin' || $filter_kat === 'cuti') {
        $sql_ket = "SELECT * FROM tb_keterangan ORDER BY id DESC";
        $q_ket = mysqli_query($koneksi, $sql_ket);
        while ($rk = mysqli_fetch_assoc($q_ket)) {
            $ts = parseWaktuToTimestamp($rk['waktu']);
            if ($cutoff > 0 && $ts < $cutoff) continue;
            if ($filter_kar !== 'semua' && $rk['id_karyawan'] !== $filter_kar) continue;

            $kat = ($rk['keterangan'] === 'Cuti' || $rk['keterangan'] === 'Sakit') ? 'Cuti' : 'Izin';
            if ($filter_kat === 'izin' && $kat !== 'Izin') continue;
            if ($filter_kat === 'cuti' && $kat !== 'Cuti') continue;

            $records[] = [
                'timestamp'   => $ts,
                'id_karyawan' => $rk['id_karyawan'],
                'nama'        => $rk['nama'],
                'kategori'    => $kat,
                'keterangan'  => $rk['alasan'] ?? '-',
                'waktu'       => $rk['waktu']
            ];
        }
    }

    // Urutkan berdasarkan timestamp descending
    usort($records, function($a, $b) {
        return $b['timestamp'] <=> $a['timestamp'];
    });
    ?>
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px; }
            th { background-color: #7e22ce; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #581c87; padding: 8px; }
            td { border: 1px solid #d1d5db; padding: 6px 8px; }
            .header-title { font-size: 16px; font-weight: bold; color: #581c87; text-align: center; }
            .sub-title { font-size: 11px; color: #6b7280; text-align: center; margin-bottom: 12px; }
            .kat-hadir { color: #16a34a; font-weight: bold; }
            .kat-izin { color: #d97706; font-weight: bold; }
            .kat-cuti { color: #ea580c; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="header-title">REKAP LAPORAN KEHADIRAN KARYAWAN</div>
        <div class="sub-title">Filter: Kategori [<?= strtoupper($filter_kat) ?>] | Periode [<?= strtoupper($filter_per) ?>] | Diekspor: <?= date('d F Y H:i:s') ?> WIB</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>ID / Tanggal Masuk</th>
                    <th>Nama Karyawan</th>
                    <th>Kategori Status</th>
                    <th>Keterangan / Alasan</th>
                    <th>Waktu Catat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($records as $row) {
                    $cls = ($row['kategori'] === 'Hadir') ? 'kat-hadir' : (($row['kategori'] === 'Cuti') ? 'kat-cuti' : 'kat-izin');
                ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td style="mso-number-format:'\@'; text-align: center;"><?= htmlspecialchars($row['id_karyawan']) ?></td>
                    <td><b><?= htmlspecialchars($row['nama']) ?></b></td>
                    <td class="<?= $cls ?>" style="text-align: center;"><?= htmlspecialchars($row['kategori']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($row['waktu']) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}
