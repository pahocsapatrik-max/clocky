-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Jan 28. 13:27
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
  `FK_roleID` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

--
-- A tábla adatainak kiíratása `emp`
--

INSERT INTO `emp` (`empID`, `name`, `dob`, `tn`, `FK_roleID`, `email`, `active`) VALUES
(1, 'lázár János', '1976-05-12', 8, 1, 'lazarinfo@gmail.com', 0),
(2, 'Deák Dániel', '1945-05-10', 10, 3, 'danideak@gmail.com', 0),
(3, 'Rogán Antal', '1976-03-07', 8, 4, 'propaganda.roganantal@gmail.com', 0),
(4, 'janika', '2000-03-12', 8, 2, 'janika@gmail.com', 0),
(5, 'Sulyok Tamás', '1956-03-24', 12, 8, 'fingszatyor@gmail.com', 0),
(6, 'Mészáros Lőrinc', '1966-02-24', 10, 10, 'lollopok@gmail.com', 0),
(7, 'varga judit', '1980-09-10', 12, 9, 'igazsagugyiszar@gnmail.com', 0),
(8, 'Gáspár Győző', '1976-03-05', 8, 7, 'gyozike@gmail.com', 0),
(9, 'jack Sparrow', '1982-04-06', 8, 6, 'nemengetlegkixd@gmail.com', 0),
(10, 'jakab klára', '1977-05-04', 24, 3, 'klarajakab@gmail.com', 0),
(11, 'Szijjártó petya', '1980-06-06', 16, 3, 'petyamarlenka@gmail.com', 0),
(12, 'Semlyén zsolt', '1962-08-08', 10, 11, 'zsoltibacsi@gmail.com', 0),
(13, 'toroczkai lászló', '1976-05-02', 10, 12, 'laszlotorocz@gmail.com', 0),
(14, 'Dúró Dóra', '1988-03-04', 10, 13, 'torotorta@gmail.com', 0),
(15, 'kaleta Gábor', '1962-03-03', 12, 11, 'gaborkaletaperu@gmail.com', 0),
(16, 'Szalay-Bobrovniczky Kristóf', '1977-04-04', 10, 16, 'hadsereg@gmail.com', 0),
(17, 'Müller cecília', '1953-04-04', 12, 15, 'ceci@gmail.com', 0),
(18, 'kis Grófo', '1988-03-03', 0, 4, 'grofokisssss@gmail.com', 0),
(19, 'józsef József', '1969-04-04', 12, 20, 'jozsijozsi@gmail.com', 0),
(20, 'XD Gábor', '1967-04-04', 10, 19, 'gabixdd@gmail.com', 0),
(21, 'Muhihihi Ignác', '1888-04-04', 10, 18, 'muhihihi@gmail.com', 0),
(22, 'tóalmási-zay-második jároszláv', '1977-04-04', 15, 17, 'eztfixnemiromlemegint@gmail.com', 0);

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
(1, 'janitor', 1200),
(2, 'BOSS', 5000),
(3, 'anyaszomorító', 10),
(4, 'vajda', 1550),
(5, 'séf', 4500),
(6, 'portás', 1345),
(7, 'Wc tisztító', 1650),
(8, 'Autonóm Fing', 2200),
(9, 'államtitkár', 2350),
(10, 'Mészáros és Mészáros KFT Stróm', 7865),
(11, 'inkább hadjuk', 3400),
(12, 'Szésőséges', 2340),
(13, 'könyvdaráló', 1500),
(15, 'operatív törzstag', 2200),
(16, 'Honvédségi Hó takaríto', 3200),
(17, 'vonatkerék pumpáló', 1555),
(18, 'Rézhajlító', 2400),
(19, 'szappan takarító', 560),
(20, 'Tükör tesztelő', 780);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `username` varchar(8) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `jogosultsag` tinyint(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `name`, `jogosultsag`) VALUES
(1, 'ppatrik', 'patrik30', 'patrik', 1),
(2, 'kamupatr', 'kamupatrik', 'kamupatrik', 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `worktime`
--

CREATE TABLE `worktime` (
  `wt_ID` int(11) NOT NULL,
  `FK_empID` int(11) NOT NULL,
  `FK_roleID` int(11) NOT NULL,
  `start_datetime` datetime NOT NULL DEFAULT current_timestamp(),
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
  MODIFY `empID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT a táblához `role`
--
ALTER TABLE `role`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT a táblához `worktime`
--
ALTER TABLE `worktime`
  MODIFY `wt_ID` int(11) NOT NULL AUTO_INCREMENT;

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
