-- ============================================================
-- RESET DATA TRANSAKSI - BookingSmarts
-- ============================================================
-- Script ini MENGHAPUS semua data transaksi (pemesanan,
-- pembayaran, ulasan, notifikasi) untuk keperluan UJI COBA
-- user experience dari awal.
--
-- DATA YANG DIPERTAHANKAN (TIDAK DIHAPUS):
--   ✅ user         - Data akun pengguna
--   ✅ catering     - Data list catering
--   ✅ gedung       - Data gedung/ruangan
--   ✅ app_settings - Konfigurasi bank & setting lainnya
--
-- DATA YANG DIHAPUS:
--   ❌ ulasan        - Ulasan/review dari user
--   ❌ notifications - Notifikasi sistem
--   ❌ pembayaran    - Data pembayaran
--   ❌ pemesanan     - Data pemesanan ruangan
--
-- ⚠️  PERINGATAN: Jalankan hanya di environment DEVELOPMENT!
--     Backup database terlebih dahulu sebelum menjalankan ini.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Hapus ulasan terlebih dahulu (ada FK ke pemesanan jika ada kolom ID_PEMESANAN)
TRUNCATE TABLE `ulasan`;

-- 2. Hapus notifikasi sistem
TRUNCATE TABLE `notifications`;

-- 3. Hapus data pembayaran
TRUNCATE TABLE `pembayaran`;

-- 4. Hapus data pemesanan (terakhir karena di-referensi tabel lain)
TRUNCATE TABLE `pemesanan`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Verifikasi: Cek jumlah data setelah reset
-- ============================================================
SELECT 'pemesanan'    AS tabel, COUNT(*) AS jumlah_data FROM `pemesanan`    UNION ALL
SELECT 'pembayaran'   AS tabel, COUNT(*) AS jumlah_data FROM `pembayaran`   UNION ALL
SELECT 'ulasan'       AS tabel, COUNT(*) AS jumlah_data FROM `ulasan`       UNION ALL
SELECT 'notifications'AS tabel, COUNT(*) AS jumlah_data FROM `notifications`UNION ALL
SELECT '--- MASTER ---' AS tabel, NULL AS jumlah_data UNION ALL
SELECT 'user'         AS tabel, COUNT(*) AS jumlah_data FROM `user`         UNION ALL
SELECT 'catering'     AS tabel, COUNT(*) AS jumlah_data FROM `catering`     UNION ALL
SELECT 'gedung'       AS tabel, COUNT(*) AS jumlah_data FROM `gedung`       UNION ALL
SELECT 'app_settings' AS tabel, COUNT(*) AS jumlah_data FROM `app_settings`;
