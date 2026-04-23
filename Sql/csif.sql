-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2026 a las 04:17:51
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

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`contCita`, `fechaCita`, `horaInicioCita`, `horaFinalCita`, `lugarCita`, `motivoCita`, `estadoCita`, `contDoctor`, `contPaciente`) VALUES
(1, '2026-04-22', '02:00:00', '04:00:00', 'Piso 3 consultorio 2', NULL, 'completada', 6, NULL),
(2, '2026-04-23', '06:00:00', '07:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(3, '2026-04-23', '07:00:00', '08:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(4, '2026-04-23', '08:00:00', '09:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(5, '2026-04-23', '09:00:00', '10:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(6, '2026-04-23', '10:00:00', '11:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(7, '2026-04-23', '11:00:00', '12:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(8, '2026-04-23', '13:00:00', '14:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(9, '2026-04-23', '14:00:00', '15:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(10, '2026-04-23', '15:00:00', '16:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(11, '2026-04-23', '16:00:00', '17:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(12, '2026-04-24', '06:00:00', '07:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(13, '2026-04-24', '07:00:00', '08:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(14, '2026-04-24', '08:00:00', '09:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(15, '2026-04-24', '09:00:00', '10:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(16, '2026-04-24', '10:00:00', '11:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(17, '2026-04-24', '11:00:00', '12:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(18, '2026-04-24', '13:00:00', '14:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(19, '2026-04-24', '14:00:00', '15:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(20, '2026-04-24', '15:00:00', '16:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(21, '2026-04-24', '16:00:00', '17:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(22, '2026-04-25', '06:00:00', '07:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(23, '2026-04-25', '07:00:00', '08:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(24, '2026-04-25', '08:00:00', '09:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(25, '2026-04-25', '09:00:00', '10:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(26, '2026-04-25', '10:00:00', '11:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(27, '2026-04-25', '11:00:00', '12:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(28, '2026-04-25', '13:00:00', '14:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(29, '2026-04-25', '14:00:00', '15:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(30, '2026-04-25', '15:00:00', '16:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(31, '2026-04-25', '16:00:00', '17:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(32, '2026-04-27', '06:00:00', '07:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(33, '2026-04-27', '07:00:00', '08:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(34, '2026-04-27', '08:00:00', '09:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(35, '2026-04-27', '09:00:00', '10:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(36, '2026-04-27', '10:00:00', '11:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(37, '2026-04-27', '11:00:00', '12:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(38, '2026-04-27', '13:00:00', '14:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(39, '2026-04-27', '14:00:00', '15:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(40, '2026-04-27', '15:00:00', '16:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(41, '2026-04-27', '16:00:00', '17:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(42, '2026-04-28', '06:00:00', '07:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', 'pendiente', 6, NULL),
(43, '2026-04-28', '07:00:00', '08:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(44, '2026-04-28', '08:00:00', '09:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(45, '2026-04-28', '09:00:00', '10:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(46, '2026-04-28', '10:00:00', '11:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(47, '2026-04-28', '11:00:00', '12:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(48, '2026-04-28', '13:00:00', '14:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(49, '2026-04-28', '14:00:00', '15:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(50, '2026-04-28', '15:00:00', '16:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL),
(51, '2026-04-28', '16:00:00', '17:00:00', 'Consultorio principal', 'Bloque automático — consultar disponibilidad', '', 6, NULL);

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
('admin', 'admincito', 'cc', 1234, '2026-04-01', 'o', 'admin@admin.com', '$2y$10$RmKFgpviPAgzG9zwYlcLkO3krW3aPodGjIPF8tNxNEaqc8t0GJowO', '3158884328', 'admin', 4),
('recepsionista', 'ista', 'cc', 2, '2026-04-06', 'f', 'rece@rece.com', '$2y$10$hURuFnlZ8Inn8q9ABK9Z6uQqenHYvwvTEVKI/EilWEPibpqPlOhCa', '634563', 'recep', 5),
('doctor', 'doctorado', 'cc', 3, '2026-04-24', 'm', 'doc@doc.com', '$2y$10$vGdbgOIeRNmfnn4uWHndo.tWRFzefsmCpuc7//pF.AotrcjZ4mhnu', '3241413', 'doctor', 6),
('paciente', 'pacientin', 'cc', 4, '2026-04-09', 'm', 'pac@pac.com', '$2y$10$vl.APZHQ3JccjAixqVOBj.CfdtQgUakDd2nPVylWHA2ffG1yHJIfe', '12431324', 'paciente', 7);

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
  MODIFY `contCita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `historias_medicas`
--
ALTER TABLE `historias_medicas`
  MODIFY `contHistoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `cont` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
