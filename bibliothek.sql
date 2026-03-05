-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 09:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bibliothek`
--

-- --------------------------------------------------------

--
-- Table structure for table `ausleihe`
--

CREATE TABLE `ausleihe` (
  `id` int(11) NOT NULL,
  `inventarnummer` int(11) NOT NULL,
  `leseausweisnummer` int(11) NOT NULL,
  `rueckgabe` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ausleiher`
--

CREATE TABLE `ausleiher` (
  `leseausweisnummer` int(11) NOT NULL,
  `vorname` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `plz` varchar(20) NOT NULL,
  `ort` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buecher`
--

CREATE TABLE `buecher` (
  `inventarnummer` int(11) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `verfasser` varchar(255) NOT NULL,
  `gruppe` varchar(100) NOT NULL,
  `standort` varchar(100) NOT NULL,
  `anzahl_ausleihen` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ausleihe`
--
ALTER TABLE `ausleihe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventarnummer` (`inventarnummer`);

--
-- Indexes for table `ausleiher`
--
ALTER TABLE `ausleiher`
  ADD PRIMARY KEY (`leseausweisnummer`);

--
-- Indexes for table `buecher`
--
ALTER TABLE `buecher`
  ADD PRIMARY KEY (`inventarnummer`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ausleihe`
--
ALTER TABLE `ausleihe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ausleiher`
--
ALTER TABLE `ausleiher`
  MODIFY `leseausweisnummer` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buecher`
--
ALTER TABLE `buecher`
  MODIFY `inventarnummer` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ausleihe`
--
ALTER TABLE `ausleihe`
  ADD CONSTRAINT `ausleihe_ibfk_1` FOREIGN KEY (`inventarnummer`) REFERENCES `buecher` (`inventarnummer`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
