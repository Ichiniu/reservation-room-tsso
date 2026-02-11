-- ========================================
-- FIX: Add TOTAL_PESERTA column to pemesanan table
-- ========================================
-- This SQL script adds the missing TOTAL_PESERTA column
-- which is needed to store the number of participants for external bookings

-- Check if column exists first (MySQL doesn't have IF NOT EXISTS for columns)
-- Run this first to see current structure:
DESCRIBE pemesanan;

-- If TOTAL_PESERTA doesn't exist in the output above, run this:
ALTER TABLE `pemesanan` 
ADD COLUMN `TOTAL_PESERTA` INT(11) NULL DEFAULT NULL 
COMMENT 'Total peserta untuk pricing PER_PESERTA (eksternal user)';

-- Verify the column was added:
DESCRIBE pemesanan;

-- Check existing data:
SELECT ID_PEMESANAN, USERNAME, TOTAL_PESERTA, ID_GEDUNG 
FROM pemesanan 
ORDER BY ID_PEMESANAN DESC 
LIMIT 10;
