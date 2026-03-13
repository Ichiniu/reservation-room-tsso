ALTER TABLE `user` DROP COLUMN IF EXISTS `erification_token`;
ALTER TABLE `user` DROP COLUMN IF EXISTS `oken_expires_at`;
ALTER TABLE `user` ADD COLUMN `verification_token` VARCHAR(100) DEFAULT NULL AFTER `is_verified`;
ALTER TABLE `user` ADD COLUMN `token_expires_at` DATETIME DEFAULT NULL AFTER `verification_token`;
