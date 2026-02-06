-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Feb 06. 12:41
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `clocky`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `emp`
--

CREATE TABLE `emp` (
  `empID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `tn` int(11) NOT NULL,
  `FK_roleID` int(11) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `FK_userID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

--
-- A tábla adatainak kiíratása `emp`
--

INSERT INTO `emp` (`empID`, `name`, `dob`, `tn`, `FK_roleID`, `email`, `active`, `FK_userID`) VALUES
(36, 'Példa János', '1976-03-03', 11, 2, 'peldajanos@gmail.com', 1, 12),
(37, 'Példa jenő', '1980-04-05', 8, 4, 'peldajeno@gmail.com', 1, 13),
(38, 'igazgató József', '1966-03-04', 10, 32, 'igazgajozsef@gmail.com', 1, 14),
(39, 'példa györgy', '1955-03-04', 10, 33, 'peldagyorgy@gmail.com', 1, 15),
(40, 'példa károly', '2000-03-03', 10, 3, 'peldakaroly@gmail.com', 1, 16),
(41, 'Hektor Bonifác', '1977-03-06', 10, 1, 'hektorbonifac@gmail.com', 1, NULL),
(42, 'Példa istván', '1970-03-06', 10, 3, 'istvanpelda@gmail.com', 1, NULL),
(43, 'Példa istván', '1970-03-06', 10, 3, 'istvanpelda@gmail.com', 1, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `role`
--

CREATE TABLE `role` (
  `roleID` int(11) NOT NULL,
  `role_name` varchar(30) NOT NULL,
  `pph_HUF` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

--
-- A tábla adatainak kiíratása `role`
--

INSERT INTO `role` (`roleID`, `role_name`, `pph_HUF`) VALUES
(1, 'Tanár', 2550),
(2, 'Takarító', 1600),
(3, 'Igazgató', 3200),
(4, 'Portás', 1750),
(5, 'logopédus', 2200),
(32, 'Legfőbb Igazgató', 34500),
(33, 'hangszerész', 1550),
(34, 'helyettes', 2300);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `username` varchar(8) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `jogosultsag` tinyint(10) NOT NULL DEFAULT 0,
  `FK_roleID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `name`, `jogosultsag`, `FK_roleID`) VALUES
(1, 'ppatrik', 'patrik30', 'patrik', 1, 1),
(12, 'peldajan', '1234', 'Példa János', 0, 1),
(13, 'peldajen', '1234', 'Példa jenő', 0, 3),
(14, 'igazgajo', '1234', 'igazgató József', 0, 32),
(15, 'peldagyo', '1234', 'példa györgy', 0, 5),
(16, 'peldakar', '1234', 'példa károly', 0, 0),
(17, 'hektorbo', '1234', 'Hektor Bonifác', 0, 1),
(18, 'istvanpe', '1234', 'Példa istván', 0, 3),
(19, 'istvanpe', '1234', 'Példa istván', 0, 3);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `worktime`
--

CREATE TABLE `worktime` (
  `wt_ID` int(11) NOT NULL,
  `FK_empID` int(11) NOT NULL,
  `FK_roleID` int(11) NOT NULL,
  `start_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `end_datetime` datetime DEFAULT NULL,
  `startbreak_time` time DEFAULT NULL,
  `endbreak_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

--
-- A tábla adatainak kiíratása `worktime`
--

INSERT INTO `worktime` (`wt_ID`, `FK_empID`, `FK_roleID`, `start_datetime`, `end_datetime`, `startbreak_time`, `endbreak_time`) VALUES
(29, 36, 1, '2026-02-04 13:24:57', '2026-02-04 13:25:01', NULL, NULL),
(30, 36, 1, '2026-02-04 13:25:02', '2026-02-04 13:25:05', NULL, NULL),
(31, 37, 4, '2026-02-04 13:32:22', '2026-02-04 13:32:33', NULL, NULL),
(32, 36, 2, '2026-02-04 13:37:14', '2026-02-04 13:37:16', NULL, NULL),
(33, 37, 4, '2026-02-04 13:38:17', '2026-02-04 13:39:08', NULL, NULL),
(34, 38, 32, '2026-02-04 13:42:48', '2026-02-04 13:45:45', NULL, NULL),
(47, 37, 4, '2026-02-04 14:34:09', '2026-02-04 14:34:14', NULL, NULL),
(48, 36, 2, '2026-02-04 14:35:06', '2026-02-04 14:35:09', NULL, NULL),
(49, 39, 33, '2026-02-04 14:36:44', '2026-02-04 14:36:47', NULL, NULL),
(50, 39, 33, '2026-02-04 14:39:42', '2026-02-04 14:39:44', NULL, NULL),
(51, 38, 32, '2026-02-04 14:58:08', '2026-02-04 15:05:24', NULL, NULL),
(52, 37, 4, '2026-02-04 15:17:50', '2026-02-04 15:17:52', NULL, NULL),
(53, 40, 3, '2026-02-05 09:31:44', NULL, NULL, NULL),
(55, 37, 3, '2026-02-05 09:38:50', '2026-02-05 09:38:55', NULL, NULL),
(56, 37, 3, '2026-02-05 09:38:56', '2026-02-05 09:39:01', NULL, NULL),
(58, 38, 32, '2026-02-05 09:44:08', '2026-02-06 12:19:40', NULL, NULL),
(59, 41, 1, '2026-02-05 09:46:07', NULL, NULL, NULL),
(61, 39, 5, '2026-02-05 09:46:47', NULL, NULL, NULL),
(62, 38, 32, '2026-02-06 12:22:18', NULL, '12:40:25', '12:41:04');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `emp`
--
ALTER TABLE `emp`
  ADD PRIMARY KEY (`empID`),
  ADD UNIQUE KEY `FK_userID` (`FK_userID`),
  ADD KEY `FK_roleID` (`FK_roleID`);

--
-- A tábla indexei `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`roleID`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `worktime`
--
ALTER TABLE `worktime`
  ADD PRIMARY KEY (`wt_ID`),
  ADD KEY `FK_empID` (`FK_empID`),
  ADD KEY `FK_roleID` (`FK_roleID`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `emp`
--
ALTER TABLE `emp`
  MODIFY `empID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT a táblához `role`
--
ALTER TABLE `role`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT a táblához `worktime`
--
ALTER TABLE `worktime`
  MODIFY `wt_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `emp`
--
ALTER TABLE `emp`
  ADD CONSTRAINT `emp_ibfk_1` FOREIGN KEY (`FK_roleID`) REFERENCES `role` (`roleID`),
  ADD CONSTRAINT `fk_emp_user` FOREIGN KEY (`FK_userID`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Megkötések a táblához `worktime`
--
ALTER TABLE `worktime`
  ADD CONSTRAINT `worktime_ibfk_1` FOREIGN KEY (`FK_roleID`) REFERENCES `role` (`roleID`),
  ADD CONSTRAINT `worktime_ibfk_2` FOREIGN KEY (`FK_empID`) REFERENCES `emp` (`empID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
