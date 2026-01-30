-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Gép: localhost:3306
-- Létrehozás ideje: 2025. Dec 12. 14:26
-- Kiszolgáló verziója: 8.0.44-0ubuntu0.24.04.1
-- PHP verzió: 8.3.6

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
  `empID` int NOT NULL,
  `name` varchar(50) COLLATE utf16_hungarian_ci NOT NULL,
  `dob` date NOT NULL,
  `tn` int NOT NULL,
  `FK_roleID` int NOT NULL,
  `email` varchar(50) COLLATE utf16_hungarian_ci NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `role`
--

CREATE TABLE `role` (
  `roleID` int NOT NULL,
  `role_name` varchar(30) COLLATE utf16_hungarian_ci NOT NULL,
  `pph_HUF` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `ID` int NOT NULL,
  `username` varchar(8) COLLATE utf16_hungarian_ci NOT NULL,
  `password` varchar(50) COLLATE utf16_hungarian_ci NOT NULL,
  `name` varchar(50) COLLATE utf16_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `worktime`
--

CREATE TABLE `worktime` (
  `wt_ID` int NOT NULL,
  `FK_empID` int NOT NULL,
  `FK_roleID` int NOT NULL,
  `start_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `emp`
--
ALTER TABLE `emp`
  ADD PRIMARY KEY (`empID`),
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
  MODIFY `empID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `role`
--
ALTER TABLE `role`
  MODIFY `roleID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `worktime`
--
ALTER TABLE `worktime`
  MODIFY `wt_ID` int NOT NULL AUTO_INCREMENT;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `emp`
--
ALTER TABLE `emp`
  ADD CONSTRAINT `emp_ibfk_1` FOREIGN KEY (`FK_roleID`) REFERENCES `role` (`roleID`);

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
