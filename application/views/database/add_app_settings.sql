-- Migration: Create app_settings table
-- Run this SQL in phpMyAdmin:

CREATE TABLE IF NOT EXISTS `app_settings` (
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert default catering phone number
INSERT INTO `app_settings` (`setting_key`, `setting_value`)
VALUES ('catering_phone', '089649261851')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
