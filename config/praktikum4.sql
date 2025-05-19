-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 19, 2025 at 10:12 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `praktikum4`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_perkara`
--

CREATE TABLE `data_perkara` (
  `id_data` int NOT NULL,
  `id_perkara` int NOT NULL,
  `no_perkara` varchar(255) NOT NULL,
  `nama_klien` varchar(255) NOT NULL,
  `jadwal_sidang` datetime NOT NULL,
  `peradilan` varchar(255) DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `data_perkara`
--

INSERT INTO `data_perkara` (`id_data`, `id_perkara`, `no_perkara`, `nama_klien`, `jadwal_sidang`, `peradilan`, `keterangan`) VALUES
(112, 129, '46/Pdt.P/2024/PN/Smp', 'maula', '2025-05-22 20:14:00', 'sumeneap', '-'),
(119, 129, '46/Pdt.P/2024/PN/Smp', 'zaki', '2025-05-30 15:10:00', '-', '-'),
(120, 129, 'nngg///', 'zaki', '2025-05-28 15:11:00', '-', '-'),
(121, 136, 'mmmmmmmmmm', 'maula', '2025-05-22 15:18:00', 'sumenep', '-');

-- --------------------------------------------------------

--
-- Table structure for table `perkara`
--

CREATE TABLE `perkara` (
  `id_perkara` int NOT NULL,
  `nama_perkara` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `perkara`
--

INSERT INTO `perkara` (`id_perkara`, `nama_perkara`) VALUES
(129, 'Perkara Pidana'),
(136, 'Perkara Perdata');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('admin','user') NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `password`, `status`, `foto`) VALUES
(24, 'zaki anwar', 'zaki', '$2y$10$k6UKl3je9T6es.qhkuscJ.WKm10rdZmj664cLrwgKDiIbrlF9WVae', 'admin', '682afb3d9a3cc.jpg'),
(64, 'alan', 'alan', '$2y$10$AtGnEMzY/k7g1Gi6PTXN1.bPujcjW.ziiwe6zlq7.2YErbMQZ0tym', 'admin', '682afb603e34e.png'),
(67, 'habib', 'habib', '$2y$10$Dm3bzgu.wdxB1D4ep8ocC.LsUkocRZeeHnB8KCszITDFtZOk1vDCK', 'user', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_perkara`
--
ALTER TABLE `data_perkara`
  ADD PRIMARY KEY (`id_data`),
  ADD KEY `id_perkara` (`id_perkara`);

--
-- Indexes for table `perkara`
--
ALTER TABLE `perkara`
  ADD PRIMARY KEY (`id_perkara`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_perkara`
--
ALTER TABLE `data_perkara`
  MODIFY `id_data` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `perkara`
--
ALTER TABLE `perkara`
  MODIFY `id_perkara` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_perkara`
--
ALTER TABLE `data_perkara`
  ADD CONSTRAINT `data_perkara_ibfk_1` FOREIGN KEY (`id_perkara`) REFERENCES `perkara` (`id_perkara`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
