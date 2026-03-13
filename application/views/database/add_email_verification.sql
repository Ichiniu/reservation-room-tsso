-- ============================================================
-- ADD EMAIL VERIFICATION COLUMNS - BookingSmarts
-- ============================================================
-- Jalankan SQL ini di phpMyAdmin / MySQL CLI sebelum deploy
-- ============================================================

ALTER TABLE `user`
  ADD COLUMN `is_verified`        TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '0=belum verifikasi, 1=sudah verifikasi' AFTER `FOTO_PROFIL`,
  ADD COLUMN `verification_token` VARCHAR(100) DEFAULT NULL COMMENT 'Token untuk verifikasi email' AFTER `is_verified`,
  ADD COLUMN `token_expires_at`   DATETIME     DEFAULT NULL COMMENT 'Waktu kadaluarsa token' AFTER `verification_token`;

-- Set semua user yang SUDAH ADA menjadi verified (karena mereka sudah terdaftar sebelumnya)
UPDATE `user` SET `is_verified` = 1 WHERE `is_verified` = 0;
