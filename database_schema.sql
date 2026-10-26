-- Skema database proyek UTS AdminLTE Nilai Mahasiswa.
-- Untuk instalasi baru. Jika db5a.nilaimhsw sudah ada, gunakan migrasi yang disediakan.
CREATE DATABASE IF NOT EXISTS `db5a` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db5a`;

CREATE TABLE IF NOT EXISTS `nilaimhsw` (
  `nim` VARCHAR(30) NOT NULL,
  `namamhsw` VARCHAR(150) NOT NULL,
  `nilaiakhir` DECIMAL(5,2) NOT NULL,
  `nilaihuruf` CHAR(1) NOT NULL,
  `predikat` VARCHAR(50) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_date` DATETIME DEFAULT NULL,
  PRIMARY KEY (`nim`),
  KEY `idx_nilaimhsw_nilaiakhir` (`nilaiakhir`),
  KEY `idx_nilaimhsw_predikat` (`predikat`),
  KEY `idx_nilaimhsw_namamhsw` (`namamhsw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
