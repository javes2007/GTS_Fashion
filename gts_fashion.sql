-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-11-2026 a las 13:20:30
-- Versión del servidor: 10.11.16-MariaDB
-- Versión de PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gts_fashion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `usuario`, `contrasena`, `fecha_registro`) VALUES
(1, 'mati2205', '$2y$10$/7dZEaS.hZirTeIKJ1NQGu80Lkk2Af/UcXrYXmQNj9ebEtoRbNnD.', '2026-10-25 05:52:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(5, 'Accesorios'),
(4, 'Bolsos'),
(2, 'Faldas'),
(3, 'Jeans'),
(6, 'Otros'),
(1, 'Vestidos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL DEFAULT '',
  `documento` varchar(30) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `apellidos`, `documento`, `correo`, `telefono`, `direccion`, `contrasena`, `fecha_registro`) VALUES
(9, 'Grey', 'Estrella', '123456', 'estrellamati2205@gmail.com', '123456', 'La floresta', '$2y$10$9WCqpc.QHcf720CQNeX.he5e3l92WgCOYa.nMq4NDLYlxo373XHiq', '2026-10-29 19:15:58'),
(10, 'Tatiana', 'Galarcio', '12345', 'tati123@gmail.com', '12345', 'Pelayo', '$2y$10$VsPHtN40y9DUeHrjidSx7Oznl8MzRmVf2d85rAlA1NbVrM32YRft2', '2026-10-31 16:59:40'),
(11, 'Sofia', 'Toro', '1234', 'sofia123@gmail.com', '1234', 'Puerto Escondido', '$2y$10$85kQx/AGPJ4zJFe1WL9n9eaHSorTUkZBmdVNTbZWKGFqjPLpp1nEu', '2026-10-31 17:01:42'),
(12, 'Juana', 'Lopez', '123', 'juana@gmail.com', '123', 'San Antero', '$2y$10$36yDDgSZoe0OS/I./WxXP.lkJAcaa2LqPiHFMrVja2mOtEH8yDG3S', '2026-10-31 17:03:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(30) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `colores` varchar(255) DEFAULT NULL,
  `tallas` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `stock`, `colores`, `tallas`, `categoria_id`) VALUES
(1, 'Vestido amarillo', 'Un look femenino, alegre y moderno.', 85000.00, 'imagenes/vestido amarillo.jpg', 10, NULL, NULL, 1),
(2, 'Vestido azul', 'Elegancia para cualquier ocasión.', 95000.00, 'imagenes/vestido azul.jpg', 10, NULL, NULL, 1),
(3, 'Vestido blanco', 'Un diseño delicado y versátil.', 80000.00, 'imagenes/vestido blanco.jpg', 10, NULL, NULL, 1),
(4, 'Vestido corto', 'Un estilo juvenil y moderno.', 75000.00, 'imagenes/vestido corto.jpg', 10, NULL, NULL, 1),
(5, 'Vestido corto elegante', 'Una opción para destacar tu estilo.', 85000.00, 'imagenes/vestido cortoo.jpg', 10, NULL, NULL, 1),
(6, 'Vestido elegante', 'Ideal para ocasiones especiales.', 120000.00, 'imagenes/vestido elegante.jpg', 10, NULL, NULL, 1),
(7, 'Vestido largo', 'Elegancia y comodidad.', 135000.00, 'imagenes/vestido largo.jpg', 10, NULL, NULL, 1),
(8, 'Vestido de noche', 'Un diseño moderno y sofisticado.', 110000.00, 'imagenes/vestidoo.jpg', 10, NULL, NULL, 1),
(9, 'Mini falda blanca', 'Combínala con tus accesorios favoritos.', 45000.00, 'imagenes/mini falda blanca.jpg', 10, NULL, NULL, 2),
(10, 'Mini falda de jean', 'Una prenda casual y fácil de combinar.', 55000.00, 'imagenes/mini falda.jpg', 10, NULL, NULL, 2),
(11, 'Jean negro', 'Un básico imprescindible en tu armario.', 90000.00, 'imagenes/jean negro.jpg', 10, NULL, NULL, 3),
(12, 'Jean azul', 'Comodidad para crear diferentes looks.', 95000.00, 'imagenes/jean.jpg', 10, NULL, NULL, 3),
(13, 'Jean de moda', 'Un estilo moderno para todos los días.', 100000.00, 'imagenes/jeannn.jpg', 10, NULL, NULL, 3),
(14, 'Bolso mini blanco', 'El accesorio ideal para completar tu look.', 45000.00, 'imagenes/bolso mini blanco.jpg', 10, NULL, NULL, 4),
(15, 'Bolso mini negro', 'Un accesorio clásico y elegante.', 50000.00, 'imagenes/bolso mini negro.jpg', 10, NULL, NULL, 4),
(16, 'Bolso mini elegante', 'Un toque especial para tus conjuntos.', 65000.00, 'imagenes/bolso mini.jpg', 10, NULL, NULL, 4),
(17, 'Pareos', 'top manga larga playa', 25000.00, 'prenda_ef996c0150c9939510e9ea803d1d55c8.jpg', 10, 'blanco,negro,rosa', 'talla unica', 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categorias_nombre` (`nombre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_productos_categoria` (`categoria_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categorias` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
