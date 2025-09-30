-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Wrz 30, 2025 at 11:13 AM
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

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id_uzytkownika`, `nr_uzytkownika`, `imie`, `nazwisko`, `Haslo`, `uprawnienia`, `email`, `telefon`, `stopka`, `aktywny`, `oddzial`, `access_production`, `access_sale`, `access_designer`, `access_manager`, `dzial`, `password_reset_timestamp`, `password_reset_token`, `session_token_timestamp`, `session_token`) VALUES
(156, NULL, 'jakub', 'kowal', '$2y$10$G51v0KDHwZtClHZoiPiaDemBNuygB6cWR7XTMvu7a5M5MDuoiEErG', NULL, 'jakkow@gmail.com', '123456789', NULL, 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(157, NULL, 'kacper', 'skalski', '$2y$10$xtPvVQsOXIB18r4ncUeEFOgrxMm33xOn8n.fMIWDo5QAm54j1QN2G', NULL, 'kac123@gmail.com', '987654321', NULL, 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(158, NULL, 'aneta', 'opel', '$2y$10$UD14YPQvAo6YNGG/u8626OFDv5posNI0t0nqfWpDX.FkLT/CrHdUq', NULL, 'aneta@gmail.com', '321654987', NULL, 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(159, NULL, 'jolanta', 'ziemba', '$2y$10$OtCcjoOJxw.SxdIHncyI2uqJPh7O13EZ1HroP2tHlfIuokic4ewyq', NULL, 'jolka432@gmail.com', '789567234', NULL, 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(160, NULL, 'konrad', 'witowski', '$2y$10$81wqRwEAPCQ1E7lkPcTmgu690KXFJpNRWBv7gJxP1vHjrgL/QVpBi', NULL, 'wiciu69@gmail.com', '423756089', NULL, 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(161, NULL, 'jakub', 'kowal', '$2y$10$Ie2kuUY3PyGVSJ/z6gJfhOvOnJnYohdRrutrSKo5kvXdq3eoYn6Fa', NULL, 'jkk@gmail.com', '867524586', NULL, 1, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
