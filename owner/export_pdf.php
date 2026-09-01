<?php
session_start();
if (!isset($_SESSION['owner_username'])) {
    header("location: login_owner.php");
    exit;
}
include '../koneksi.php';

$type = $_GET['type'] ?? 'karyawan';
$date_now = date('d F Y, H:i') . ' WIB';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Dokumen Laporan — Portal Owner</title>
    <link rel="icon" href="../img/Fevicon.png" type="image/png">
    <link href="../vendors/fontawesome/css/all.min.css" rel="stylesheet">
    <style>
        @page { size: A4; margin: 15mm 15mm 15mm 15mm; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1f2937;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header-doc {
            border-bottom: 2px solid #374151;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .header-doc h2 {
            margin: 0 0 4px 0;
            color: #111827;
            font-size: 18pt;
            letter-spacing: -0.5px;
        }
        .header-doc p {
            margin: 0;
            color: #6b7280;
            font-size: 9.5pt;
        }
        .meta-box {
            text-align: right;
            font-size: 9pt;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }
        th {
            background-color: #f3f4f6;
            color: #1f2937;
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            font-weight: 700;
            text-align: left;
            font-size: 9pt;
            text-transform: uppercase;
        }
        td {
            border: 1px solid #e5e7eb;
            padding: 7px 10px;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
        }
        .badge-hadir { background: #dcfce7; color: #166534; }
        .badge-izin { background: #fef3c7; color: #92400e; }
        .badge-cuti { background: #ffedd5; color: #c2410c; }
        .footer-doc {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #6b7280;
        }
        .sig-block {
            margin-top: 25px;
            text-align: right;
            font-size: 10pt;
        }
        .sig-space {
            height: 55px;
        }
        .no-print {
            background: #4f46e5;
            color: #fff;
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #fff;
            color: #4f46e5;
            border: none;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <strong>Mode Pratinjau Cetak / PDF</strong>
            <div style="font-size: 9pt; opacity: 0.9;">Gunakan menu browser (Save as PDF) untuk menyimpan dokumen resmi ini.</div>
        </div>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <?php if ($type === 'karyawan'): ?>
        <?php $query = mysqli_query($koneksi, "SELECT * FROM tb_karyawan ORDER BY nama ASC"); ?>
        <div class="header-doc">
            <div>
                <h2>DAFTAR DATA KARYAWAN</h2>
                <p>Sistem Informasi Absensi & Manajemen Karyawan</p>
            </div>
            <div class="meta-box">
                <div>Tanggal Cetak: <strong><?= $date_now ?></strong></div>
                <div>Oleh: <strong><?= htmlspecialchars($_SESSION['owner_nama'] ?? 'Owner') ?></strong></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">No</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th style="text-align: center;">L/P</th>
                    <th>No. Telepon</th>
                    <th style="text-align: center;">Tgl Masuk</th>
                    <th style="text-align: center;">Masa Kerja</th>
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
                    <td><strong><?= htmlspecialchars($r['nama']) ?></strong></td>
                    <td><?= htmlspecialchars($r['jabatan']) ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($r['jenkel']) ?></td>
                    <td><?= htmlspecialchars($r['no_tel']) ?></td>
                    <td style="text-align: center;"><?= $tgl_format ?></td>
                    <td style="text-align: center; font-weight: 700; color: #059669;"><?= $masa_kerja ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    <?php elseif ($type === 'admin'): ?>
        <?php $query = mysqli_query($koneksi, "SELECT * FROM tb_daftar ORDER BY id ASC"); ?>
        <div class="header-doc">
            <div>
                <h2>DAFTAR AKUN ADMINISTRATOR</h2>
                <p>Sistem Informasi Absensi — Hak Akses Operasional</p>
            </div>
            <div class="meta-box">
                <div>Tanggal Cetak: <strong><?= $date_now ?></strong></div>
                <div>Oleh: <strong><?= htmlspecialchars($_SESSION['owner_nama'] ?? 'Owner') ?></strong></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th style="width: 80px; text-align: center;">ID</th>
                    <th>Username Administrator</th>
                    <th style="text-align: center;">Level Hak Akses</th>
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
                    <td><strong><?= htmlspecialchars($r['username']) ?></strong></td>
                    <td style="text-align: center; color: #059669; font-weight: bold;">Administrator Terdaftar</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    <?php elseif ($type === 'rekap'): ?>
        <?php
        $filter_kat = $_GET['kategori'] ?? 'semua';
        $filter_kar = $_GET['id_karyawan'] ?? 'semua';
        $filter_per = $_GET['periode'] ?? 'semua';

        $now = time();
        $cutoff = 0;
        $label_periode = 'Semua Waktu';
        if ($filter_per === '1pekan') {
            $cutoff = $now - (7 * 86400);
            $label_periode = '1 Pekan Terakhir';
        } elseif ($filter_per === '1bulan') {
            $cutoff = $now - (30 * 86400);
            $label_periode = '1 Bulan Terakhir';
        } elseif ($filter_per === '6bulan') {
            $cutoff = $now - (180 * 86400);
            $label_periode = '6 Bulan Terakhir';
        } elseif ($filter_per === '1tahun') {
            $cutoff = $now - (365 * 86400);
            $label_periode = '1 Tahun Terakhir';
        }

        $records = [];
        if ($filter_kat === 'semua' || $filter_kat === 'absen') {
            $q_absen = mysqli_query($koneksi, "SELECT * FROM tb_absen ORDER BY id DESC");
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

        if ($filter_kat === 'semua' || $filter_kat === 'izin' || $filter_kat === 'cuti') {
            $q_ket = mysqli_query($koneksi, "SELECT * FROM tb_keterangan ORDER BY id DESC");
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

        usort($records, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });
        ?>
        <div class="header-doc">
            <div>
                <h2>REKAP LAPORAN KEHADIRAN</h2>
                <p>Periode: <strong><?= $label_periode ?></strong> | Kategori: <strong><?= strtoupper($filter_kat) ?></strong></p>
            </div>
            <div class="meta-box">
                <div>Tanggal Cetak: <strong><?= $date_now ?></strong></div>
                <div>Oleh: <strong><?= htmlspecialchars($_SESSION['owner_nama'] ?? 'Owner') ?></strong></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">No</th>
                    <th>ID Karyawan</th>
                    <th>Nama Karyawan</th>
                    <th style="text-align: center;">Status</th>
                    <th>Alasan / Keterangan</th>
                    <th>Waktu Tercatat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($records)):
                ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #9ca3af; padding: 20px;">Tidak ada data pada periode ini.</td>
                </tr>
                <?php
                endif;
                $no = 1;
                foreach ($records as $row) {
                    $badge_cls = ($row['kategori'] === 'Hadir') ? 'badge-hadir' : (($row['kategori'] === 'Cuti') ? 'badge-cuti' : 'badge-izin');
                ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['id_karyawan']) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td style="text-align: center;"><span class="badge <?= $badge_cls ?>"><?= htmlspecialchars($row['kategori']) ?></span></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td><?= htmlspecialchars($row['waktu']) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="sig-block">
        <div>Mengetahui,</div>
        <div style="font-weight: bold; margin-top: 2px;">Owner / Pimpinan Perusahaan</div>
        <div class="sig-space"></div>
        <div style="font-weight: bold; text-decoration: underline;"><?= htmlspecialchars($_SESSION['owner_nama'] ?? 'Owner Executive') ?></div>
    </div>

    <div class="footer-doc">
        <div>Dokumen ini dicetak otomatis dari Sistem Presensi AbsenBos.</div>
        <div>Halaman 1 / 1</div>
    </div>

</body>
</html>
