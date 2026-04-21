-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-04-2026 a las 00:30:19
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `csif`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `contCita` int(11) NOT NULL,
  `fechaCita` date NOT NULL,
  `horaInicioCita` time NOT NULL,
  `horaFinalCita` time NOT NULL,
  `lugarCita` varchar(150) NOT NULL,
  `motivoCita` varchar(255) DEFAULT NULL,
  `estadoCita` enum('pendiente','confirmada','cancelada','completada') NOT NULL DEFAULT 'pendiente',
  `contDoctor` int(11) NOT NULL,
  `contPaciente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historias_medicas`
--

CREATE TABLE `historias_medicas` (
  `contHistoria` int(11) NOT NULL,
  `contDoctor` int(11) NOT NULL,
  `contPaciente` int(11) NOT NULL,
  `contCita` int(11) DEFAULT NULL,
  `fechaExpedicion` date NOT NULL,
  `motivoConsulta` varchar(255) NOT NULL,
  `sintomas` text DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `recetaMedica` text DEFAULT NULL,
  `incapacidadMedica` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `nameUser` varchar(50) NOT NULL,
  `secondNameUser` varchar(50) NOT NULL,
  `tipoID` varchar(10) NOT NULL,
  `idUser` int(12) NOT NULL,
  `fechaNacimientoUsr` date NOT NULL,
  `generoUser` varchar(2) NOT NULL,
  `emailUser` varchar(100) NOT NULL,
  `passwordUser` varchar(255) DEFAULT NULL,
  `telUser` varchar(20) DEFAULT NULL,
  `rolUser` varchar(10) NOT NULL,
  `cont` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`nameUser`, `secondNameUser`, `tipoID`, `idUser`, `fechaNacimientoUsr`, `generoUser`, `emailUser`, `passwordUser`, `telUser`, `rolUser`, `cont`) VALUES
('admin', 'admincito', 'cc', 1234, '2026-04-01', 'o', 'admin@admin.com', '$2y$10$RmKFgpviPAgzG9zwYlcLkO3krW3aPodGjIPF8tNxNEaqc8t0GJowO', '3158884328', 'admin', 4);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`contCita`),
  ADD KEY `fk_cita_doctor` (`contDoctor`),
  ADD KEY `fk_cita_paciente` (`contPaciente`);

--
-- Indices de la tabla `historias_medicas`
--
ALTER TABLE `historias_medicas`
  ADD PRIMARY KEY (`contHistoria`),
  ADD KEY `fk_historia_doctor` (`contDoctor`),
  ADD KEY `fk_historia_paciente` (`contPaciente`),
  ADD KEY `fk_historia_cita` (`contCita`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cont`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `contCita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historias_medicas`
--
ALTER TABLE `historias_medicas`
  MODIFY `contHistoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `cont` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `fk_cita_doctor` FOREIGN KEY (`contDoctor`) REFERENCES `usuario` (`cont`),
  ADD CONSTRAINT `fk_cita_paciente` FOREIGN KEY (`contPaciente`) REFERENCES `usuario` (`cont`);

--
-- Filtros para la tabla `historias_medicas`
--
ALTER TABLE `historias_medicas`
  ADD CONSTRAINT `fk_historia_cita` FOREIGN KEY (`contCita`) REFERENCES `citas` (`contCita`),
  ADD CONSTRAINT `fk_historia_doctor` FOREIGN KEY (`contDoctor`) REFERENCES `usuario` (`cont`),
  ADD CONSTRAINT `fk_historia_paciente` FOREIGN KEY (`contPaciente`) REFERENCES `usuario` (`cont`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
