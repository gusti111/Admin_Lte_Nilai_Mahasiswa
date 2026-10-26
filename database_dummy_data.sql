-- 30 data dummy untuk demonstrasi pencarian, filter predikat, sorting, dan pagination.
-- Foto demo memakai avatar bawaan AdminLTE yang dipetakan oleh users.php/_nilai_helpers.php.
-- INSERT IGNORE menjaga record yang memakai NIM sama tidak ditimpa.
USE `db5a`;
INSERT IGNORE INTO `nilaimhsw`
  (`nim`, `namamhsw`, `nilaiakhir`, `nilaihuruf`, `predikat`, `foto`, `created_date`, `modified_date`)
VALUES
  ('202600001', 'Aditya Pratama', 95.00, 'A', 'Sangat Memuaskan', 'demo-user1.jpg', '2026-10-02 09:07:00', '2026-10-03 10:11:00'),
  ('202600002', 'Aisyah Putri', 88.50, 'A', 'Sangat Memuaskan', 'demo-user2.jpg', '2026-10-03 10:14:00', '2026-10-04 11:22:00'),
  ('202600003', 'Andi Saputra', 82.00, 'A', 'Sangat Memuaskan', 'demo-user3.jpg', '2026-10-04 11:21:00', '2026-10-05 12:33:00'),
  ('202600004', 'Anisa Rahmawati', 80.50, 'B', 'Memuaskan', 'demo-user4.jpg', '2026-10-05 12:28:00', NULL),
  ('202600005', 'Arif Hidayat', 79.75, 'B', 'Memuaskan', 'demo-user5.jpg', '2026-10-06 13:35:00', '2026-10-07 14:55:00'),
  ('202600006', 'Bunga Lestari', 75.25, 'B', 'Memuaskan', 'demo-user6.jpg', '2026-10-07 14:42:00', '2026-10-08 15:06:00'),
  ('202600007', 'Dinda Maharani', 70.00, 'B', 'Memuaskan', 'demo-user7.jpg', '2026-10-08 15:49:00', '2026-10-09 16:17:00'),
  ('202600008', 'Dimas Setiawan', 66.00, 'B', 'Memuaskan', 'demo-user8.jpg', '2026-10-09 16:56:00', NULL),
  ('202600009', 'Eka Wulandari', 65.50, 'C', 'Cukup Memuaskan', 'demo-user1.jpg', '2026-10-10 08:03:00', '2026-10-11 10:39:00'),
  ('202600010', 'Farhan Akbar', 62.25, 'C', 'Cukup Memuaskan', 'demo-user2.jpg', '2026-10-11 09:10:00', '2026-10-12 11:50:00'),
  ('202600011', 'Fajar Nugroho', 58.00, 'C', 'Cukup Memuaskan', 'demo-user3.jpg', '2026-10-12 10:17:00', '2026-10-13 12:01:00'),
  ('202600012', 'Fitri Handayani', 54.50, 'C', 'Cukup Memuaskan', 'demo-user4.jpg', '2026-10-13 11:24:00', NULL),
  ('202600013', 'Gilang Ramadhan', 50.50, 'D', 'Kurang Memuaskan', 'demo-user5.jpg', '2026-10-14 12:31:00', '2026-10-15 14:23:00'),
  ('202600014', 'Intan Permata', 48.00, 'D', 'Kurang Memuaskan', 'demo-user6.jpg', '2026-10-15 13:38:00', '2026-10-16 15:34:00'),
  ('202600015', 'Ilham Saputra', 43.75, 'D', 'Kurang Memuaskan', 'demo-user7.jpg', '2026-10-16 14:45:00', '2026-10-17 16:45:00'),
  ('202600016', 'Kevin Alexander', 36.00, 'D', 'Kurang Memuaskan', 'demo-user8.jpg', '2026-10-17 15:52:00', NULL),
  ('202600017', 'Laila Zahra', 34.50, 'E', 'Tidak Lulus', 'demo-user1.jpg', '2026-10-18 16:59:00', '2026-10-19 10:07:00'),
  ('202600018', 'Maya Sari', 32.00, 'E', 'Tidak Lulus', 'demo-user2.jpg', '2026-10-19 08:06:00', '2026-10-20 11:18:00'),
  ('202600019', 'Muhammad Rizky', 25.50, 'E', 'Tidak Lulus', 'demo-user3.jpg', '2026-10-20 09:13:00', '2026-10-21 12:29:00'),
  ('202600020', 'Nabila Azzahra', 14.50, 'E', 'Tidak Lulus', 'demo-user4.jpg', '2026-10-21 10:20:00', NULL),
  ('202600021', 'Putri Amelia', 5.00, 'E', 'Tidak Lulus', 'demo-user5.jpg', '2026-10-22 11:27:00', '2026-10-23 14:51:00'),
  ('202600022', 'Rafi Kurniawan', 99.00, 'A', 'Sangat Memuaskan', 'demo-user6.jpg', '2026-10-23 12:34:00', '2026-10-24 15:02:00'),
  ('202600023', 'Rina Oktaviani', 85.00, 'A', 'Sangat Memuaskan', 'demo-user7.jpg', '2026-10-24 13:41:00', '2026-10-25 16:13:00'),
  ('202600024', 'Siti Nurhaliza', 81.25, 'A', 'Sangat Memuaskan', 'demo-user8.jpg', '2026-10-01 14:48:00', NULL),
  ('202600025', 'Taufik Hidayat', 68.50, 'B', 'Memuaskan', 'demo-user1.jpg', '2026-10-02 15:55:00', '2026-10-03 10:35:00'),
  ('202600026', 'Upa Pratama', 60.00, 'C', 'Cukup Memuaskan', 'demo-user2.jpg', '2026-10-03 16:02:00', '2026-10-04 11:46:00'),
  ('202600027', 'Vina Anggraini', 49.50, 'D', 'Kurang Memuaskan', 'demo-user3.jpg', '2026-10-04 08:09:00', '2026-10-05 12:57:00'),
  ('202600028', 'Wahyu Firmansyah', 38.50, 'D', 'Kurang Memuaskan', 'demo-user4.jpg', '2026-10-05 09:16:00', NULL),
  ('202600029', 'Yusuf Maulana', 20.00, 'E', 'Tidak Lulus', 'demo-user5.jpg', '2026-10-06 10:23:00', '2026-10-07 14:19:00'),
  ('202600030', 'Zahra Nurfadilah', 64.25, 'C', 'Cukup Memuaskan', 'demo-user6.jpg', '2026-10-07 11:30:00', '2026-10-08 15:30:00');
