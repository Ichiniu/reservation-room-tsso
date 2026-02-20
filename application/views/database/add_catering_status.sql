-- Migration: Add IS_ACTIVE column to CATERING table
-- Run this SQL in phpMyAdmin or MySQL CLI:
--   mysql -u root bookingsmarts < application/views/database/add_catering_status.sql

ALTER TABLE `catering`
  ADD COLUMN `IS_ACTIVE` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Aktif, 0=Nonaktif'
  AFTER `MENU_JSON`;
