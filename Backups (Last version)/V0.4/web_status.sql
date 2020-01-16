-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2016 a las 14:57:57
-- Versión del servidor: 10.1.16-MariaDB
-- Versión de PHP: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `onlol_concept`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `web_status`
--

CREATE TABLE `web_status` (
  `name` varchar(100) NOT NULL,
  `status` enum('disabled','updating','enabled','') NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `web_status`
--

INSERT INTO `web_status` (`name`, `status`, `reason`) VALUES
('champion_images', 'enabled', '6.19.1'),
('item_images', 'enabled', '6.19.1'),
('stats', 'enabled', ''),
('summoner_images', 'enabled', 'gg'),
('toplists', 'enabled', ''),
('toplists_champmastery', 'enabled', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `web_status`
--
ALTER TABLE `web_status`
  ADD UNIQUE KEY `name` (`name`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
