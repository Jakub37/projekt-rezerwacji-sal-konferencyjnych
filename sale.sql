-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Wrz 30, 2025 at 11:07 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `modernforms_system`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sale`
--

CREATE TABLE `sale` (
  `id` int(11) NOT NULL,
  `nr_sali` int(11) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `od_godziny` time DEFAULT NULL,
  `rezerwacja` varchar(100) DEFAULT NULL,
  `do_godziny` time DEFAULT NULL,
  `id_uzytkownika` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`id`, `nr_sali`, `data`, `od_godziny`, `rezerwacja`, `do_godziny`, `id_uzytkownika`) VALUES
(24, 1, '2025-09-24', '13:00:00', 'jakub kowal', '15:00:00', 156),
(26, 1, '2025-09-24', '10:00:00', 'jakub kowal', '13:00:00', 156),
(28, 2, '2025-09-24', '17:00:00', 'jakub kowal', '18:54:00', 161),
(29, 2, '2025-09-06', '12:32:00', 'jakub kowal', '15:13:00', 156),
(30, 1, '2025-09-11', '03:12:00', 'jakub kowal', '03:02:00', 156),
(35, 1, '2025-10-04', '08:00:00', 'jakub kowal', '09:00:00', 156),
(36, 1, '2025-10-06', '12:00:00', 'jakub kowal', '14:00:00', 156),
(37, 2, '2025-09-29', '13:00:00', 'jakub kowal', '14:00:00', 156),
(38, 1, '2025-09-01', '10:00:00', 'jakub kowal', '16:00:00', 156),
(40, 1, '2025-09-29', '14:00:00', 'jakub kowal', '15:00:00', 156),
(42, 2, '2025-10-04', '12:32:00', 'jakub kowal', '13:23:00', 156),
(43, 2, '0000-00-00', '13:13:00', 'jakub kowal', '13:42:00', 161);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
