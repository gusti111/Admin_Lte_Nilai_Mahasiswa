-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Okt 2026 pada 02.12
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db5a`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilaimhsw`
--

CREATE TABLE `nilaimhsw` (
  `nim` varchar(10) NOT NULL,
  `namamhsw` varchar(30) NOT NULL,
  `nilaiakhir` float NOT NULL,
  `nilaihuruf` varchar(5) DEFAULT NULL,
  `predikat` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Menyimpan waktu saat data pertama kali dibuat.',
  `modified_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Otomatis memperbarui waktu tiap kali data diubah.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `nilaimhsw`
--

INSERT INTO `nilaimhsw` (`nim`, `namamhsw`, `nilaiakhir`, `nilaihuruf`, `predikat`, `foto`, `created_date`, `modified_date`) VALUES
('0987654321', 'Gusty Faqikh', 99.99, 'A', 'Sangat Memuaskan', NULL, '2026-10-12 03:43:53', '2026-10-12 03:43:53'),
('1010101010', 'Jarwo', 55, 'B', 'Memuaskan', NULL, '2026-10-12 05:33:54', '2026-10-12 05:33:54'),
('1234567890', 'Randa Wijaya', 100, 'A', 'Sangat Memuaskan', NULL, '2026-10-09 03:24:43', '2026-10-09 03:24:43'),
('2020202020', 'Habil', 99, 'A', 'Sangat Memuaskan', NULL, '2026-10-12 05:31:49', '2026-10-12 05:31:49'),
('3030303030', 'Sopo', 30, 'D', 'Kurang', NULL, '2026-10-12 05:35:30', '2026-10-12 05:35:30'),
('3202216543', 'Jolo', 22, 'D', 'Kurang', NULL, '2026-10-12 07:12:26', '2026-10-12 07:12:26'),
('3202416035', 'Muhammad Nabil Fauzal Abrar', 9, 'E', 'Tidak Lulus', NULL, '2026-10-12 06:24:04', '2026-10-12 06:24:04'),
('5050505050', 'Bang Upa', 98, 'A', 'Sangat Memuaskan', NULL, '2026-10-09 03:33:28', '2026-10-09 03:33:28'),
('5555555555', 'akbar', 45, 'C', 'Cukup', NULL, '2026-10-12 05:44:50', '2026-10-12 05:44:50'),
('7171717171', 'Character', 99.99, 'A', 'Sangat Memuaskan', 'f3ac4b58f7dbddbaafdb4d4e5660f1f4.jpg', '2026-10-25 16:13:55', '2026-10-25 16:13:55'),
('8080808080', 'Patrizio', 90, 'A', 'Sangat Memuaskan', NULL, '2026-10-16 02:18:30', '2026-10-16 02:19:48'),
('9090909090', 'Wang Lin', 100, 'A', 'Sangat Memuaskan', NULL, '2026-10-12 05:38:57', '2026-10-12 05:38:57');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `nilaimhsw`
--
ALTER TABLE `nilaimhsw`
  ADD PRIMARY KEY (`nim`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
