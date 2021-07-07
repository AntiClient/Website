-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Apr 10, 2019 alle 20:58
-- Versione del server: 10.1.38-MariaDB-0ubuntu0.18.04.1
-- Versione PHP: 7.3.3-1+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `AnticlientTMP`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `blacklist`
--

CREATE TABLE `blacklist` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `pcname` varchar(200) NOT NULL,
  `date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `iplogs`
--

CREATE TABLE `iplogs` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `date` varchar(200) NOT NULL,
  `ip` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `content` longtext,
  `date` int(20) NOT NULL,
  `data` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `partner`
--

CREATE TABLE `partner` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `addedby` varchar(200) NOT NULL,
  `addedon` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `products`
--

CREATE TABLE `products` (
  `id` int(200) NOT NULL,
  `name` varchar(40) NOT NULL,
  `price` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `features` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `token` varchar(20) NOT NULL,
  `payerID` varchar(30) NOT NULL,
  `product` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `pin` varchar(4) NOT NULL,
  `check1` varchar(200) NOT NULL,
  `check2` varchar(200) NOT NULL,
  `check3` varchar(200) NOT NULL,
  `date` varchar(200) NOT NULL,
  `alts` longtext,
  `recyclebin` longtext,
  `startTime` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `pin` varchar(4) NOT NULL,
  `status` varchar(20) NOT NULL,
  `date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `email` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `password` varchar(500) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `banned` int(1) NOT NULL DEFAULT '0',
  `rank` int(1) NOT NULL DEFAULT '0',
  `rank_bought_on` int(15) DEFAULT NULL,
  `firstlogin` int(1) NOT NULL DEFAULT '0',
  `download` varchar(10) DEFAULT NULL,
  `download_generated_on` int(15) DEFAULT NULL,
  `pin` varchar(4) DEFAULT NULL,
  `pin_generated_on` int(15) DEFAULT NULL,
  `verified` int(1) NOT NULL DEFAULT '0',
  `confirmCode` varchar(16) DEFAULT NULL,
  `resetpw` varchar(20) DEFAULT NULL,
  `resetpw_generated_on` int(15) DEFAULT NULL,
  `lastip` varchar(200) DEFAULT NULL,
  `licenses` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `iplogs`
--
ALTER TABLE `iplogs`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indici per le tabelle `partner`
--
ALTER TABLE `partner`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `iplogs`
--
ALTER TABLE `iplogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `partner`
--
ALTER TABLE `partner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `products`
--
ALTER TABLE `products`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
