-- ============================================================
-- ADD GOOGLE LOGIN COLUMNS - BookingSmarts
-- ============================================================

ALTER TABLE `user`
  ADD COLUMN `google_id` VARCHAR(100) DEFAULT NULL AFTER `PASSWORD`,
  ADD COLUMN `is_profile_complete` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=belum lengkap, 1=sudah lengkap' AFTER `is_verified`;

-- Untuk user lama, set is_profile_complete = 1 karena mereka sudah lewat form registrasi
UPDATE `user` SET `is_profile_complete` = 1;

-- Ubah beberapa kolom menjadi nullable agar user Google bisa masuk dulu sebelum melengkapi data
ALTER TABLE `user` 
  MODIFY COLUMN `PASSWORD` VARCHAR(255) DEFAULT NULL,
  MODIFY COLUMN `NO_TELEPON` VARCHAR(15) DEFAULT NULL,
  MODIFY COLUMN `ALAMAT` VARCHAR(225) DEFAULT NULL,
  MODIFY COLUMN `TANGGAL_LAHIR` DATE DEFAULT NULL,
  MODIFY COLUMN `perusahaan` ENUM('INTERNAL','EKSTERNAL') DEFAULT NULL;
