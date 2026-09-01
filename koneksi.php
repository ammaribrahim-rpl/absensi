<?php 
$koneksi = mysqli_connect("127.0.0.1", "root", "", "karyawansi", 8080, "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock");

if (mysqli_connect_errno()) {
	echo "koneksi gagal " . mysqli_connect_error();
}

if (!function_exists('resolveTglMasuk')) {
    function resolveTglMasuk($row_or_tgl, $fallback_id = '') {
        if (is_array($row_or_tgl)) {
            $tgl = trim($row_or_tgl['tgl_masuk'] ?? '');
            if (!empty($tgl) && $tgl !== '0000-00-00' && $tgl !== '-') return $tgl;
            return trim($row_or_tgl['id_karyawan'] ?? '');
        }
        $tgl = trim((string)$row_or_tgl);
        if (!empty($tgl) && $tgl !== '0000-00-00' && $tgl !== '-') return $tgl;
        return trim((string)$fallback_id);
    }
}

if (!function_exists('hitungMasaKerja')) {
    function hitungMasaKerja($tgl_masuk, $fallback_id = '') {
        $tgl = resolveTglMasuk($tgl_masuk, $fallback_id);
        if (empty($tgl)) return '-';
        $tgl_clean = trim($tgl);
        // Ekstrak DD-MM-YYYY dari awal string (abaikan suffix seperti "-1", "-2")
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $tgl_clean, $m)) {
            $tgl_clean = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        try {
            $masuk = new DateTime($tgl_clean);
            $sekarang = new DateTime();
            if ($masuk > $sekarang) {
                return 'Baru Bergabung';
            }
            $diff = $sekarang->diff($masuk);
            $y = $diff->y;
            $m = $diff->m;
            if ($y == 0 && $m == 0) return 'Baru Bergabung';
            if ($y == 0) return $m . " Bulan";
            if ($m == 0) return $y . " Tahun";
            return $y . " Tahun " . $m . " Bulan";
        } catch (Exception $e) {
            return '-';
        }
    }
}

if (!function_exists('getFormattedTglMasuk')) {
    function getFormattedTglMasuk($raw, $fallback_id = '') {
        $val = resolveTglMasuk($raw, $fallback_id);
        if (empty($val)) return '-';
        $val = trim($val);
        // Format DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $val, $m)) {
            return sprintf('%02d-%02d-%04d', $m[1], $m[2], $m[3]);
        }
        $ts = strtotime($val);
        if ($ts) {
            return date('d-m-Y', $ts);
        }
        return $val;
    }
}

if (!function_exists('getJabatanIcon')) {
    function getJabatanIcon($nama_jabatan) {
        global $koneksi;
        static $cache = [];
        if (empty($nama_jabatan)) return 'fas fa-briefcase';
        if (isset($cache[$nama_jabatan])) return $cache[$nama_jabatan];
        
        $stmt = mysqli_prepare($koneksi, "SELECT icon FROM tb_jabatan WHERE jabatan = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $nama_jabatan);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($r = mysqli_fetch_assoc($res)) {
                $cache[$nama_jabatan] = !empty($r['icon']) ? $r['icon'] : 'fas fa-briefcase';
                mysqli_stmt_close($stmt);
                return $cache[$nama_jabatan];
            }
            mysqli_stmt_close($stmt);
        }
        return 'fas fa-briefcase';
    }
}

if (!function_exists('parseWaktuToTimestamp')) {
    function parseWaktuToTimestamp($waktu) {
        if (empty($waktu)) return 0;
        if (preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})(?:\s+(\d{1,2}):(\d{2})(?::(\d{2}))?(?:\s*([ap]m))?)?/i', $waktu, $m)) {
            $day = $m[1]; $mon = $m[2]; $year = $m[3];
            $hour = $m[4] ?? '00'; $min = $m[5] ?? '00'; $sec = $m[6] ?? '00';
            $ampm = strtolower($m[7] ?? '');
            if ($ampm === 'pm' && $hour < 12) $hour += 12;
            if ($ampm === 'am' && $hour == 12) $hour = '00';
            $iso = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $mon, $day, $hour, $min, $sec);
            return strtotime($iso) ?: 0;
        }
        return strtotime($waktu) ?: 0;
    }
}
?>