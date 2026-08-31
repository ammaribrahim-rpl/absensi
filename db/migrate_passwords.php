<?php
/**
 * Script Migrasi Database Absensi
 * ================================
 * Jalankan script ini SEKALI setelah semua file PHP sudah di-update.
 * 
 * Yang dilakukan script ini:
 * 1. ALTER tb_karyawan.password ke VARCHAR(255) (untuk menampung hash password_hash)
 * 2. ALTER tb_daftar.password ke VARCHAR(255) 
 * 3. Hash ulang semua password admin yang masih plaintext ke password_hash()
 * 4. Set password default untuk karyawan yang masih pakai MD5 (karena MD5 tidak bisa di-reverse)
 * 
 * PENTING: Setelah migrasi, password karyawan lama TIDAK BISA dipakai lagi.
 *          Karyawan perlu reset password atau admin set password baru.
 * 
 * Cara pakai: Buka di browser http://localhost/absenbos/db/migrate_passwords.php
 * HAPUS FILE INI SETELAH MIGRASI SELESAI!
 */

// Proteksi: hanya bisa dijalankan dari localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die("Script ini hanya bisa dijalankan dari localhost.");
}

include '../koneksi.php';

echo "<h1>Absensi — Migrasi Password</h1>";
echo "<pre>";

// ==========================================
// STEP 1: ALTER TABLE tb_karyawan
// ==========================================
echo "\n[STEP 1] ALTER tb_karyawan.password ke VARCHAR(255)...\n";
$alter1 = mysqli_query($koneksi, "ALTER TABLE tb_karyawan MODIFY password VARCHAR(255) NOT NULL");
if ($alter1) {
    echo "  ✅ tb_karyawan.password berhasil diubah ke VARCHAR(255)\n";
} else {
    echo "  ❌ Gagal: " . mysqli_error($koneksi) . "\n";
}

// ==========================================
// STEP 2: ALTER TABLE tb_daftar
// ==========================================
echo "\n[STEP 2] ALTER tb_daftar.password ke VARCHAR(255)...\n";
$alter2 = mysqli_query($koneksi, "ALTER TABLE tb_daftar MODIFY password VARCHAR(255) NOT NULL");
if ($alter2) {
    echo "  ✅ tb_daftar.password berhasil diubah ke VARCHAR(255)\n";
} else {
    echo "  ❌ Gagal: " . mysqli_error($koneksi) . "\n";
}

// ==========================================
// STEP 3: Hash ulang password admin (tb_daftar)
// ==========================================
echo "\n[STEP 3] Migrasi password admin (tb_daftar)...\n";
$admins = mysqli_query($koneksi, "SELECT id, username, password FROM tb_daftar");
$migrated_admin = 0;
$skipped_admin = 0;

while ($row = mysqli_fetch_assoc($admins)) {
    // Cek apakah sudah di-hash dengan password_hash (prefix $2y$ atau $2b$)
    if (strpos($row['password'], '$2y$') === 0 || strpos($row['password'], '$2b$') === 0) {
        echo "  ⏩ Admin '{$row['username']}' sudah menggunakan password_hash, skip.\n";
        $skipped_admin++;
        continue;
    }
    
    // Password masih plaintext, hash ulang
    $hashed = password_hash($row['password'], PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($koneksi, "UPDATE tb_daftar SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $hashed, $row['id']);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "  ✅ Admin '{$row['username']}' — password berhasil di-hash (password lama masih bisa dipakai)\n";
        $migrated_admin++;
    } else {
        echo "  ❌ Admin '{$row['username']}' — gagal: " . mysqli_error($koneksi) . "\n";
    }
    mysqli_stmt_close($stmt);
}
echo "  📊 Total: $migrated_admin di-migrasi, $skipped_admin sudah ter-hash\n";

// ==========================================
// STEP 4: Info tentang password karyawan
// ==========================================
echo "\n[STEP 4] Status password karyawan (tb_karyawan)...\n";
$karyawans = mysqli_query($koneksi, "SELECT id_karyawan, username, password FROM tb_karyawan");
$md5_count = 0;
$hashed_count = 0;

while ($row = mysqli_fetch_assoc($karyawans)) {
    if (strpos($row['password'], '$2y$') === 0 || strpos($row['password'], '$2b$') === 0) {
        $hashed_count++;
        echo "  ✅ Karyawan '{$row['username']}' (NIP: {$row['id_karyawan']}) — sudah menggunakan password_hash\n";
    } else {
        $md5_count++;
        echo "  ⚠️  Karyawan '{$row['username']}' (NIP: {$row['id_karyawan']}) — masih pakai hash MD5\n";
        echo "     → Password MD5 tidak bisa di-reverse. Karyawan ini perlu RESET PASSWORD.\n";
        echo "     → Admin bisa set password baru lewat menu Edit Karyawan.\n";
    }
}
echo "  📊 Total: $hashed_count sudah ter-hash, $md5_count masih MD5 (perlu reset)\n";

// ==========================================
// SUMMARY
// ==========================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "MIGRASI SELESAI!\n";
echo str_repeat("=", 50) . "\n";
echo "\nLangkah selanjutnya:\n";
echo "1. Password admin yang sudah di-migrasi bisa langsung dipakai (password lama tetap berlaku)\n";
echo "2. Untuk karyawan yang masih MD5, admin perlu set password baru via Edit Karyawan\n";
echo "3. ⚠️  HAPUS FILE INI (migrate_passwords.php) setelah migrasi selesai!\n";

echo "</pre>";
?>
