-- phpMyAdmin SQL Dump - EcoBank Banca Online (ACTUALIZADO)
-- Añade tarjetas de ejemplo

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `banca_online`;
CREATE DATABASE IF NOT EXISTS `banca_online` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `banca_online`;

-- --------------------------------------------------------
-- Tabla: usuarios
-- --------------------------------------------------------
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rol` int(11) NOT NULL DEFAULT 1,
  `provincia` varchar(100) DEFAULT NULL,
  `municipio` varchar(100) DEFAULT NULL,
  `via` varchar(150) DEFAULT NULL,
  `numero_piso` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `telefono` (`telefono`),
  UNIQUE KEY `dni` (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`,`nombre`,`apellidos`,`email`,`telefono`,`password_hash`,`fecha_nacimiento`,`dni`,`activo`,`rol`,`provincia`,`municipio`,`via`,`numero_piso`) VALUES
(2,'Juan','Castro','javiliyo333@gmail.com','654738675','$2y$10$HMLO9Ted2pZReasEELEMCOk/iSIidz89HfYSavHMvcCamoj0WrByG',NULL,'74537265J',1,1,NULL,NULL,NULL,NULL),
(4,'Laura','Azuhaga','laura@gmail.com','657483956','$2y$10$BJW2j46/H2CRXQXpnj6aoO5x5cZ4djpX.ba15Gi1ydu5VF2Bq2I4i',NULL,'65748937S',1,0,'CASTELLO','ARAÑUEL','CL - COLON','Nº17'),
(5,'admin','admin','admin@gmail.com','657483645','$2y$10$Lnxj1PA4DFCwD/jItO5O.u1SgvK8LM8DT/KgDnUlLMDHp4mzG.8qy',NULL,'67543827L',1,2,'A CORUÑA','A BAÑA','LG - AGROMAIOR','Nº1'),
(7,'Luismi','Prieto Pareja','luismiluismpp@gmail.com','643427479','$2y$10$w5S4qrM/2DeGf.SGhwn6xeBMdj3HrTObnHJFpzvH.rLWcYI24Kq5y',NULL,'78107726W',1,1,'GRANADA','BENALUA DE LAS VILLAS',NULL,'Nº8'),
(9,'Alejandro','Granados Malagón','alejandrogranados8506@gmail.com','642172379','$2y$10$8l3wvEF2cn8aPM9cc8S87.p4pxbJxss8LIPh1WhyoHQBf5ikyBe4C','2008-04-10','75013903R',0,1,'CORDOBA','PRIEGO DE CORDOBA',NULL,'9'),
(10,'pepe','aaaaa','pepe@gmail.com','423422564','$2y$10$eD5viKGcWGXVE4aLai2NgeEgff4.s9N6nxgbgsNYFijid/wCuW9xW','2007-08-01','34345355S',1,0,'CORDOBA','PALMA DEL RIO',NULL,'5');

-- --------------------------------------------------------
-- Tabla: cuentas
-- --------------------------------------------------------
CREATE TABLE `cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `saldo` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tipo` enum('corriente','ahorro') NOT NULL DEFAULT 'corriente',
  `moneda` varchar(3) NOT NULL DEFAULT 'EUR',
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `iban` (`iban`),
  KEY `fk_cuenta_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cuentas` (`id`,`usuario_id`,`iban`,`saldo`,`tipo`,`moneda`,`activa`) VALUES
(1,4,'ES7620770024003102575766',1350.50,'corriente','EUR',1),
(3,7,'ES7620770024003102575767',900.00,'corriente','EUR',1),
(5,9,'ES5041624373449052324310',0.00,'corriente','EUR',1),
(6,10,'ES6745532637747822803392',0.00,'corriente','EUR',1);

-- --------------------------------------------------------
-- Tabla: tarjetas
-- --------------------------------------------------------
CREATE TABLE `tarjetas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuenta_id` int(11) NOT NULL,
  `numero_tarjeta` varchar(255) NOT NULL,
  `titular` varchar(255) NOT NULL,
  `fecha_caducidad` date NOT NULL,
  `cvv_hash` varchar(255) NOT NULL,
  `tipo` enum('debito','credito') NOT NULL DEFAULT 'debito',
  `limite_credito` decimal(10,2) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_tarjeta` (`numero_tarjeta`),
  KEY `fk_tarjeta_cuenta` (`cuenta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tarjeta para Laura (cuenta_id=1)
INSERT INTO `tarjetas` (`id`,`cuenta_id`,`numero_tarjeta`,`titular`,`fecha_caducidad`,`cvv_hash`,`tipo`,`activa`) VALUES
(1,1,'4532015112830366','LAURA AZUHAGA','2029-04-01','$2y$10$xxx.example.hash','debito',1);

-- Tarjeta para Luismi (cuenta_id=3)
INSERT INTO `tarjetas` (`id`,`cuenta_id`,`numero_tarjeta`,`titular`,`fecha_caducidad`,`cvv_hash`,`tipo`,`activa`) VALUES
(2,3,'4916338506082832','LUISMI PRIETO PAREJA','2030-01-01','$2y$10$yyy.example.hash','debito',1);

-- --------------------------------------------------------
-- Tabla: transacciones
-- --------------------------------------------------------
CREATE TABLE `transacciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuenta_origen_id` int(11) NOT NULL,
  `cuenta_destino_id` int(11) DEFAULT NULL,
  `importe` decimal(15,2) NOT NULL,
  `tipo` enum('bizum','transferencia') NOT NULL,
  `estado` enum('pendiente','completada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `concepto` varchar(255) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `referencia` (`referencia`),
  KEY `idx_transacciones_origen` (`cuenta_origen_id`),
  KEY `idx_transacciones_destino` (`cuenta_destino_id`),
  KEY `idx_transacciones_fecha` (`fecha`),
  KEY `idx_transacciones_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transacciones` (`id`,`cuenta_origen_id`,`cuenta_destino_id`,`importe`,`tipo`,`estado`,`concepto`,`referencia`,`fecha`) VALUES
(1,1,NULL,50.00,'bizum','completada','Cena con amigos','BIZUM_001','2026-03-10 12:00:00'),
(2,1,NULL,120.00,'transferencia','completada','Alquiler','TRANS_001','2026-03-11 09:00:00'),
(3,1,NULL,15.75,'bizum','completada','Café','BIZUM_002','2026-03-12 10:30:00'),
(4,1,NULL,300.00,'transferencia','pendiente','Compra portátil','TRANS_002','2026-03-20 14:00:00'),
(5,1,NULL,80.00,'bizum','completada','Regalo cumpleaños','BIZUM_003','2026-04-01 16:00:00'),
(6,3,1,20.00,'bizum','completada','cena','BIZUM_69dbc1ba1925d','2026-04-12 16:00:58'),
(7,3,1,70.00,'bizum','completada','Amigos','BIZUM_69dbc25c5bc27','2026-04-12 16:03:40'),
(8,3,1,10.00,'bizum','completada','cena','BIZUM_69dbcd01400ed','2026-04-12 16:49:05'),
-- Transacción de nómina recibida para Luismi
(9,1,3,1200.00,'transferencia','completada','Nómina abril','TRANS_NOMINA_04','2026-04-11 08:00:00'),
-- Bizum recibido
(10,1,3,35.00,'bizum','completada','Bizum a María','BIZUM_MARIA_01','2026-04-18 10:24:00');

-- --------------------------------------------------------
-- Tabla: bizum
-- --------------------------------------------------------
CREATE TABLE `bizum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaccion_id` int(11) NOT NULL,
  `telefono_destino` varchar(20) NOT NULL,
  `nombre_destino` varchar(255) DEFAULT NULL,
  `mensaje` varchar(140) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaccion_id` (`transaccion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bizum` (`id`,`transaccion_id`,`telefono_destino`,`nombre_destino`,`mensaje`) VALUES
(1,6,'632562238','Laura Azuhaga','cena'),
(2,7,'632562238','Laura Azuhaga','Amigos'),
(3,8,'657483956','Laura Azuhaga','cena'),
(4,10,'657483956','María García','Bizum a María');

-- --------------------------------------------------------
-- Tabla: transferencias
-- --------------------------------------------------------
CREATE TABLE `transferencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaccion_id` int(11) NOT NULL,
  `iban_destino` varchar(34) NOT NULL,
  `titular_destino` varchar(255) NOT NULL,
  `banco_destino` varchar(255) DEFAULT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  `requiere_verificacion` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaccion_id` (`transaccion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transferencias` (`id`,`transaccion_id`,`iban_destino`,`titular_destino`,`concepto`,`requiere_verificacion`) VALUES
(1,2,'ES1234567890123456789012','Propietario Inmobiliaria','Alquiler',1),
(2,4,'ES9876543210987654321098','MediaMarkt S.A.','Compra portátil',1),
(3,9,'ES7620770024003102575767','Luismi Prieto Pareja','Nómina abril',1);

-- --------------------------------------------------------
-- Tabla: codigos_verificacion
-- --------------------------------------------------------
CREATE TABLE `codigos_verificacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `transaccion_id` int(11) DEFAULT NULL,
  `codigo` varchar(10) NOT NULL,
  `expira_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_codigo_transaccion` (`transaccion_id`),
  KEY `idx_codigos_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla: notificaciones
-- --------------------------------------------------------
CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('transferencia','bizum','seguridad','info') NOT NULL,
  `contenido` text NOT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notificaciones_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notificaciones` (`usuario_id`,`tipo`,`contenido`,`leida`) VALUES
(7,'bizum','Has enviado un Bizum de €20,00 a Laura Azuhaga.',0),
(7,'transferencia','Has recibido una transferencia de €1.200,00 (Nómina abril).',0),
(7,'bizum','Has enviado un Bizum de €70,00 a Laura Azuhaga.',1),
(7,'info','Bienvenido a EcoBank. Tu cuenta ha sido creada correctamente.',1);

-- --------------------------------------------------------
-- Tabla: sesiones
-- --------------------------------------------------------
CREATE TABLE `sesiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(512) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `dispositivo` varchar(255) DEFAULT NULL,
  `inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_sesiones_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla: mensajes_soporte
-- --------------------------------------------------------
CREATE TABLE `mensajes_soporte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('abierto','en_proceso','cerrado') NOT NULL DEFAULT 'abierto',
  `respuesta` text DEFAULT NULL,
  `respondido_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_soporte_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════ FOREIGN KEYS ════
ALTER TABLE `bizum`
  ADD CONSTRAINT `fk_bizum_transaccion` FOREIGN KEY (`transaccion_id`) REFERENCES `transacciones` (`id`) ON DELETE CASCADE;

ALTER TABLE `codigos_verificacion`
  ADD CONSTRAINT `fk_codigo_transaccion` FOREIGN KEY (`transaccion_id`) REFERENCES `transacciones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_codigo_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `cuentas`
  ADD CONSTRAINT `fk_cuenta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `mensajes_soporte`
  ADD CONSTRAINT `fk_soporte_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `notificaciones`
  ADD CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `sesiones`
  ADD CONSTRAINT `fk_sesion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `tarjetas`
  ADD CONSTRAINT `fk_tarjeta_cuenta` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas` (`id`);

ALTER TABLE `transacciones`
  ADD CONSTRAINT `fk_trans_destino` FOREIGN KEY (`cuenta_destino_id`) REFERENCES `cuentas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_trans_origen` FOREIGN KEY (`cuenta_origen_id`) REFERENCES `cuentas` (`id`);

ALTER TABLE `transferencias`
  ADD CONSTRAINT `fk_transfer_transaccion` FOREIGN KEY (`transaccion_id`) REFERENCES `transacciones` (`id`) ON DELETE CASCADE;

COMMIT;

-- --------------------------------------------------------
-- Tabla: simulaciones (ingresos y gastos periódicos)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `simulaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('gasto','ingreso') NOT NULL,
  `importe` decimal(15,2) NOT NULL,
  `intervalo_dias` int(11) NOT NULL DEFAULT 1,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `ultima_ejecucion` timestamp NULL DEFAULT NULL,
  `proxima_ejecucion` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sim_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `simulaciones`
  ADD CONSTRAINT `fk_sim_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
