-- MIGRASI SELEKTIF UNTUK DATABASE LAMA db5a.nilaimhsw.
-- Buat backup database dari phpMyAdmin sebelum mengubah struktur.
USE `db5a`;

SHOW COLUMNS FROM `nilaimhsw`;
SHOW INDEX FROM `nilaimhsw`;

-- Jalankan hanya jika kolom foto belum ada:
-- ALTER TABLE `nilaimhsw` ADD COLUMN `foto` VARCHAR(255) NULL DEFAULT NULL AFTER `predikat`;

-- Jalankan hanya jika tanggal belum ada:
-- ALTER TABLE `nilaimhsw` ADD COLUMN `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
-- ALTER TABLE `nilaimhsw` ADD COLUMN `modified_date` DATETIME NULL DEFAULT NULL;

-- Jika database lama memiliki panjang kolom yang tidak sesuai, sesuaikan setelah mengecek data:
-- ALTER TABLE `nilaimhsw` MODIFY `nim` VARCHAR(30) NOT NULL;
-- ALTER TABLE `nilaimhsw` MODIFY `namamhsw` VARCHAR(150) NOT NULL;
-- ALTER TABLE `nilaimhsw` MODIFY `nilaiakhir` DECIMAL(5,2) NOT NULL;
-- ALTER TABLE `nilaimhsw` MODIFY `nilaihuruf` CHAR(1) NOT NULL;
-- ALTER TABLE `nilaimhsw` MODIFY `predikat` VARCHAR(50) NOT NULL;
-- ALTER TABLE `nilaimhsw` MODIFY `modified_date` DATETIME NULL DEFAULT NULL;

-- Cek NIM duplikat sebelum menambahkan indeks unik:
SELECT `nim`, COUNT(*) AS `jumlah`
FROM `nilaimhsw`
GROUP BY `nim`
HAVING COUNT(*) > 1;

-- HANYA bila query duplikat kosong dan belum ada primary/unique index untuk nim:
-- ALTER TABLE `nilaimhsw` ADD UNIQUE KEY `uq_nilaimhsw_nim` (`nim`);

-- Indeks opsional untuk query besar; jangan buat indeks duplikat:
-- CREATE INDEX `idx_nilaimhsw_nilaiakhir` ON `nilaimhsw` (`nilaiakhir`);
-- CREATE INDEX `idx_nilaimhsw_predikat` ON `nilaimhsw` (`predikat`);
-- CREATE INDEX `idx_nilaimhsw_namamhsw` ON `nilaimhsw` (`namamhsw`);

-- Setelah struktur siap, import database_dummy_data.sql untuk 30 record demo.
