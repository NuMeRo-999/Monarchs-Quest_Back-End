-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-06-2024 a las 00:43:47
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `monarchs_quest`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `effect`
--

CREATE TABLE `effect` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `damage` int(11) NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enemy`
--

CREATE TABLE `enemy` (
  `id` int(11) NOT NULL,
  `stage_id` int(11) DEFAULT NULL,
  `health_points` int(11) NOT NULL,
  `attack_power` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `critical_strike_chance` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `state` smallint(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `max_health_points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `enemy`
--

INSERT INTO `enemy` (`id`, `stage_id`, `health_points`, `attack_power`, `defense`, `critical_strike_chance`, `level`, `state`, `name`, `image_filename`, `max_health_points`) VALUES
(2, NULL, 100, 10, 0, 0, 1, 1, 'Orco', 'Orc-Sheet-664036aba9a76.png', 0),
(3, NULL, 110, 15, 5, 0, 1, 1, 'Orco Guerrero', 'orc-warrior-sheet-6640370929866.png', 0),
(4, NULL, 80, 15, 0, 10, 1, 1, 'Orco Pícaro', 'Orc-Rogue-Sheet-6640374415ff1.png', 0),
(5, NULL, 80, 20, 0, 10, 1, 1, 'Orco Chamán', 'Orc-Shaman-Sheet-66403772270dd.png', 0),
(6, NULL, 100, 10, 0, 0, 1, 1, 'Esqueleto', 'skeleton-Sheet-664037b54866d.png', 0),
(7, NULL, 110, 15, 5, 0, 1, 1, 'Esqueleto Guerrero', 'Skeleton-Warrior-Sheet-6640389b3fa66.png', 0),
(8, NULL, 80, 15, 0, 10, 1, 1, 'Esqueleto Pícaro', 'Skeleton-Rogue-Sheet-664037e727edb.png', 0),
(9, NULL, 80, 20, 0, 10, 1, 1, 'Esqueleto Mago', 'Skeleton-Mage-Sheet-664038fe2fef4.png', 0),


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game`
--

CREATE TABLE `game` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `heroe`
--

CREATE TABLE `heroe` (
  `id` int(11) NOT NULL,
  `health_points` int(11) NOT NULL,
  `attack_power` int(11) NOT NULL,
  `critical_strike_chance` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `experience` double NOT NULL,
  `level` int(11) NOT NULL,
  `state` smallint(6) NOT NULL,
  `max_health_points` int(11) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `heroe`
--

INSERT INTO `heroe` (`id`, `health_points`, `attack_power`, `critical_strike_chance`, `defense`, `experience`, `level`, `state`, `max_health_points`, `image_filename`, `name`) VALUES
(1, 100, 20, 0, 15, 0, 1, 1, 100, 'Knight-Sheet-66402d4b4e098.png', 'Knight'),
(2, 100, 20, 10, 10, 0, 1, 1, 100, 'Rogue-Sheet-66402d78ed349.png', 'Rogue'),
(3, 100, 25, 0, 10, 0, 1, 1, 100, 'Wizard-Sheet-66402d9d49e1c.png', 'Wizard'),


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `heroe_item`
--

CREATE TABLE `heroe_item` (
  `heroe_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `heroe_skill`
--

CREATE TABLE `heroe_skill` (
  `heroe_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `heroe_skill`
--

INSERT INTO `heroe_skill` (`heroe_id`, `skill_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(2, 1),
(2, 2),
(3, 1),
(3, 2),

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `item`
--

CREATE TABLE `item` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `critical_strike_chance` int(11) NOT NULL,
  `attack_power` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `rarity` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `health_points` int(11) NOT NULL,
  `max_health_points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `item`
--

INSERT INTO `item` (`id`, `name`, `description`, `critical_strike_chance`, `attack_power`, `defense`, `quantity`, `rarity`, `type`, `image_filename`, `state`, `health_points`, `max_health_points`) VALUES
(1, 'Poción mediana', 'Te restaura 20 puntos de vida', 0, 0, 0, 1, 'común', 'consumible', 'potion-02a-663dedaf2ed53.png', 0, 20, 0),
(2, 'Poción grande', 'Te restaura 30 puntos de vida', 0, 0, 0, 1, 'raro', 'consumible', 'potion-03a-663deded87d2b.png', 0, 30, 0),
(3, 'Poción pequeña', 'Te restaura 10 puntos de vida', 0, 0, 0, 1, 'común', 'consumible', 'potion_01f.png', 0, 10, 0),
(4, 'Espada de madera', 'Espada de madera, pesada y eficaz', 0, 10, 0, 1, 'común', 'arma', 'wood-sword-66401b73d81c4.png', 0, 0, 0),
(9, 'Escudo básico de madera', 'Un escudo de madera débil y pobre pero útil', 0, 0, 5, 1, 'común', 'arma', 'wood-bad-shield-66401dbfc673c.png', 0, 0, 0),
(10, 'Escudo mediocre de madera', 'Escudo resistente hecho de madera', 0, 0, 10, 1, 'común', 'arma', 'wood-medium-shield-66401e0cac4bb.png', 0, 0, 0),
(11, 'Escudo de madera noble', 'Escudo de gran resistencia fabricado con madera noble', 0, 0, 15, 1, 'raro', 'arma', 'wood-good-shield-66401e53f0d45.png', 0, 0, 0),
(12, 'Espada de hueso', 'Espada potente y punzante fabricada con hueso', 20, 30, 0, 1, 'raro', 'arma', 'bone-sword-66401e9fdbc9b.png', 0, 0, 0),
(13, 'Martillo de hueso', 'Gran martillo fabricado con huesos de gigante', 10, 40, 0, 1, 'raro', 'arma', 'bone-hammer-66401ee8f06f8.png', 0, 0, 0),
(14, 'Daga de hueso', 'Peligrosa daga punzante y veloz fabricada con huesos', 40, 25, 0, 1, 'raro', 'arma', 'bone-dagger-66401f4ec7674.png', 0, 0, 0),
(15, 'Hacha de hueso', 'Gran hacha fabricada con huesos', 15, 30, 0, 1, 'raro', 'arma', 'bone-axe-66401f96bd9a1.png', 0, 0, 0),
(16, 'Lanza de hueso', 'Lanza robusta y veloz fabricada con huesos', 30, 30, 0, 1, 'raro', 'arma', 'bone-spear-664020028a5fe.png', 0, 0, 0),
(17, 'Escudo básico de hueso', 'Escudo resistente hecho de hueso', 0, 0, 10, 1, 'común', 'arma', 'bone-bad-shield-664020590f91e.png', 0, 0, 0),
(18, 'Escudo resistente de hueso', 'Escudo resistente hecho de hueso', 0, 0, 20, 1, 'raro', 'arma', 'bone-medium-shield-66402090d417c.png', 0, 0, 0),
(19, 'Escudo de gran resistencia de hueso', 'Escudo robusto y muy resistente hecho con hueso', 0, 0, 30, 1, 'raro', 'arma', 'bone-good-shield-664020d017d1d.png', 0, 0, 0),
(20, 'Colgante de vida', 'Te proporciona un pequeño aumento de vida máxima', 0, 0, 0, 1, 'común', 'amuleto', 'necklace-02b-664026db03b15.png', 0, 0, 10),
(21, 'Anillo de poder', 'Anillo que te aumenta la fuerza de tus ataques', 10, 10, 0, 1, 'raro', 'amuleto', 'ring-01e-6640274ccf52d.png', 0, 0, 0),

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `save_slot`
--

CREATE TABLE `save_slot` (
  `id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `money` int(11) NOT NULL,
  `kills` int(11) NOT NULL,
  `state` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `save_slot_item`
--

CREATE TABLE `save_slot_item` (
  `save_slot_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `skill`
--

CREATE TABLE `skill` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `attack_damage` int(11) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `health_points` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `critical_strike_chance` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `skill`
--

INSERT INTO `skill` (`id`, `name`, `description`, `type`, `attack_damage`, `image_filename`, `health_points`, `defense`, `critical_strike_chance`) VALUES
(1, 'Ataque con espada', 'Asesta un tajo con tu espada', 'Attack', 10, 'Icon23-663de46ac14ba.png', 0, 0, 0),
(2, 'Protección', 'Aumenta tu defensa permanentemente', 'Buff', 0, 'Icon24-663de8196a6e3.png', 0, 2, 0),
(3, 'Aumento de fuerza', 'Aumenta tu fuerza permanentemente', 'Buff', 2, 'icon6.png', 0, 0, 0),
(4, 'Revitalización menor', 'Recibes una pequeña cantidad de curación', 'Buff', 0, 'icon2.png', 10, 0, 0),
(5, 'Aumento de crítico', 'Aumenta tu crítico permanentemente', 'Buff', 0, 'icon22.png', 0, 0, 2),
(6, 'Aumento de experiencia', 'Ganas una pequeña cantidad de experiencia', 'Buff', 0, 'icon20.png', 0, 0, 0),
(7, 'Ingresos pasivos', 'Obtienes una buena cantidad de dinero', 'Buff', 0, 'icon7.png', 0, 0, 0),
(8, 'Martillazo', 'Das un martillazo de gran potencia', 'Attack', 20, 'icon10.png', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `skill_effect`
--

CREATE TABLE `skill_effect` (
  `skill_id` int(11) NOT NULL,
  `effect_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stage`
--

CREATE TABLE `stage` (
  `id` int(11) NOT NULL,
  `save_slot_id` int(11) NOT NULL,
  `stage` int(11) NOT NULL,
  `state` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stage_heroe`
--

CREATE TABLE `stage_heroe` (
  `stage_id` int(11) NOT NULL,
  `heroe_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `username` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `effect`
--
ALTER TABLE `effect`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `enemy`
--
ALTER TABLE `enemy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FB9F5AA92298D193` (`stage_id`);

--
-- Indices de la tabla `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `heroe`
--
ALTER TABLE `heroe`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `heroe_item`
--
ALTER TABLE `heroe_item`
  ADD PRIMARY KEY (`heroe_id`,`item_id`),
  ADD KEY `IDX_78E07E0977D25060` (`heroe_id`),
  ADD KEY `IDX_78E07E09126F525E` (`item_id`);

--
-- Indices de la tabla `heroe_skill`
--
ALTER TABLE `heroe_skill`
  ADD PRIMARY KEY (`heroe_id`,`skill_id`),
  ADD KEY `IDX_DD899AEB77D25060` (`heroe_id`),
  ADD KEY `IDX_DD899AEB5585C142` (`skill_id`);

--
-- Indices de la tabla `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indices de la tabla `save_slot`
--
ALTER TABLE `save_slot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D3EE9702E48FD905` (`game_id`);

--
-- Indices de la tabla `save_slot_item`
--
ALTER TABLE `save_slot_item`
  ADD PRIMARY KEY (`save_slot_id`,`item_id`),
  ADD KEY `IDX_1100B40E1C77C22F` (`save_slot_id`),
  ADD KEY `IDX_1100B40E126F525E` (`item_id`);

--
-- Indices de la tabla `skill`
--
ALTER TABLE `skill`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `skill_effect`
--
ALTER TABLE `skill_effect`
  ADD PRIMARY KEY (`skill_id`,`effect_id`),
  ADD KEY `IDX_992AC5E65585C142` (`skill_id`),
  ADD KEY `IDX_992AC5E6F5E9B83B` (`effect_id`);

--
-- Indices de la tabla `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C27C93691C77C22F` (`save_slot_id`);

--
-- Indices de la tabla `stage_heroe`
--
ALTER TABLE `stage_heroe`
  ADD PRIMARY KEY (`stage_id`,`heroe_id`),
  ADD KEY `IDX_6BF720E52298D193` (`stage_id`),
  ADD KEY `IDX_6BF720E577D25060` (`heroe_id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_USERNAME` (`username`),
  ADD KEY `IDX_8D93D649E48FD905` (`game_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `effect`
--
ALTER TABLE `effect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `enemy`
--
ALTER TABLE `enemy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1198;

--
-- AUTO_INCREMENT de la tabla `game`
--
ALTER TABLE `game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `heroe`
--
ALTER TABLE `heroe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT de la tabla `item`
--
ALTER TABLE `item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4883;

--
-- AUTO_INCREMENT de la tabla `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `save_slot`
--
ALTER TABLE `save_slot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT de la tabla `skill`
--
ALTER TABLE `skill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `stage`
--
ALTER TABLE `stage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `enemy`
--
ALTER TABLE `enemy`
  ADD CONSTRAINT `FK_FB9F5AA92298D193` FOREIGN KEY (`stage_id`) REFERENCES `stage` (`id`);

--
-- Filtros para la tabla `heroe_item`
--
ALTER TABLE `heroe_item`
  ADD CONSTRAINT `FK_78E07E09126F525E` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_78E07E0977D25060` FOREIGN KEY (`heroe_id`) REFERENCES `heroe` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `heroe_skill`
--
ALTER TABLE `heroe_skill`
  ADD CONSTRAINT `FK_DD899AEB5585C142` FOREIGN KEY (`skill_id`) REFERENCES `skill` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_DD899AEB77D25060` FOREIGN KEY (`heroe_id`) REFERENCES `heroe` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `save_slot`
--
ALTER TABLE `save_slot`
  ADD CONSTRAINT `FK_D3EE9702E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`);

--
-- Filtros para la tabla `save_slot_item`
--
ALTER TABLE `save_slot_item`
  ADD CONSTRAINT `FK_1100B40E126F525E` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_1100B40E1C77C22F` FOREIGN KEY (`save_slot_id`) REFERENCES `save_slot` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `skill_effect`
--
ALTER TABLE `skill_effect`
  ADD CONSTRAINT `FK_992AC5E65585C142` FOREIGN KEY (`skill_id`) REFERENCES `skill` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_992AC5E6F5E9B83B` FOREIGN KEY (`effect_id`) REFERENCES `effect` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `FK_C27C93691C77C22F` FOREIGN KEY (`save_slot_id`) REFERENCES `save_slot` (`id`);

--
-- Filtros para la tabla `stage_heroe`
--
ALTER TABLE `stage_heroe`
  ADD CONSTRAINT `FK_6BF720E52298D193` FOREIGN KEY (`stage_id`) REFERENCES `stage` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_6BF720E577D25060` FOREIGN KEY (`heroe_id`) REFERENCES `heroe` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
