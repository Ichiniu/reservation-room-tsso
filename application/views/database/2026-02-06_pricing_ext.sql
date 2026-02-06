-- BookingSmarts: Konsep harga EKSTERNAL (Meeting/Amphitheater per peserta, Podcast per jam)
-- Jalankan di database MySQL/MariaDB kamu.
-- Catatan: Jika kolom sudah ada, abaikan error "Duplicate column".

-- =========================
-- 1) Tambah kolom harga di tabel GEDUNG
-- =========================
ALTER TABLE gedung
  ADD COLUMN PRICING_MODE VARCHAR(20) NULL AFTER HARGA_SEWA,
  ADD COLUMN HARGA_HALF_DAY_PP INT NULL AFTER PRICING_MODE,
  ADD COLUMN HARGA_FULL_DAY_PP INT NULL AFTER HARGA_HALF_DAY_PP,
  ADD COLUMN HARGA_AUDIO_PER_JAM INT NULL AFTER HARGA_FULL_DAY_PP,
  ADD COLUMN HARGA_VIDEO_PER_JAM INT NULL AFTER HARGA_AUDIO_PER_JAM;

-- =========================
-- 2) Tambah kolom input tambahan di tabel PEMESANAN (khusus eksternal)
-- =========================
ALTER TABLE pemesanan
  ADD COLUMN TOTAL_PESERTA INT NULL AFTER JUMLAH_CATERING,
  ADD COLUMN PODCAST_TYPE VARCHAR(10) NULL AFTER TOTAL_PESERTA;

-- =========================
-- 3) (Opsional) Isi default mode & harga kalau kosong
-- =========================
-- Mode:
--   FLAT            : harga gedung biasa (HARGA_SEWA)
--   PER_PESERTA     : half/full day x total peserta
--   PODCAST_PER_JAM : per jam berdasarkan AUDIO/VIDEO

UPDATE gedung
SET PRICING_MODE = 'PODCAST_PER_JAM'
WHERE (PRICING_MODE IS NULL OR TRIM(PRICING_MODE) = '')
  AND UPPER(NAMA_GEDUNG) LIKE '%PODCAST%';

UPDATE gedung
SET PRICING_MODE = 'PER_PESERTA'
WHERE (PRICING_MODE IS NULL OR TRIM(PRICING_MODE) = '')
  AND (
    UPPER(NAMA_GEDUNG) LIKE '%MEETING%'
    OR UPPER(NAMA_GEDUNG) LIKE '%AMPHI%'
    OR UPPER(NAMA_GEDUNG) LIKE '%AMPHITHEATER%'
  );

UPDATE gedung
SET PRICING_MODE = 'FLAT'
WHERE PRICING_MODE IS NULL OR TRIM(PRICING_MODE) = '';

-- Default harga (boleh kamu sesuaikan dari admin nanti)
UPDATE gedung SET HARGA_HALF_DAY_PP = 30000 WHERE HARGA_HALF_DAY_PP IS NULL;
UPDATE gedung SET HARGA_FULL_DAY_PP = 60000 WHERE HARGA_FULL_DAY_PP IS NULL;
UPDATE gedung SET HARGA_AUDIO_PER_JAM = 150000 WHERE HARGA_AUDIO_PER_JAM IS NULL;
UPDATE gedung SET HARGA_VIDEO_PER_JAM = 200000 WHERE HARGA_VIDEO_PER_JAM IS NULL;
