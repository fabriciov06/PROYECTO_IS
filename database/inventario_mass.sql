-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-07-2026 a las 04:32:21
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventario_mass`
--

CREATE DATABASE IF NOT EXISTS `inventario_mass`;
USE `inventario_mass`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
  `idAuditoria` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo` enum('crear','modificar','desactivar') NOT NULL,
  `entidad` varchar(50) NOT NULL,
  `idRegistro` int(11) NOT NULL,
  `detalle` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`idAuditoria`, `usuario`, `fecha`, `tipo`, `entidad`, `idRegistro`, `detalle`) VALUES
(1, 'Administrador Sistema', '2026-07-24 02:58:31', 'crear', 'Producto', 13, 'Registro de nuevo producto: P0000013 - Gaseosa Coca-Cola 1.5L \\\'Zero\\\' &lt;script&gt;alert(\\\'test\\\')&lt;/script&gt; (Stock: 50, Mín: 15, Precio: S/ 7.2)'),
(2, 'Administrador Sistema', '2026-07-24 03:10:12', 'modificar', 'Producto', 13, 'Modificación de producto P0000013 (ID: 13) - Nombre: Gaseosa Coca-Cola 1.5L &#039;Zero&#039; &amp;lt;script&amp;gt;alert(&#039;test&#039;)&amp;lt;/scrasdasipt&amp;gt;, Categoría: Bebidas, Precio: S/ 7.2, Stock: 50, Stock Mín: 15'),
(3, 'root', '2026-07-24 03:35:13', 'desactivar', 'Producto', 13, 'Desactivación lógica de producto ID: 13'),
(4, 'Administrador Sistema', '2026-07-24 03:36:08', 'crear', 'Producto', 14, 'Registro de nuevo producto: P0000014 - Leche Gloria Evaporada Entera 400g (Stock: 120, Mín: 40, Precio: S/ 4.2)'),
(5, 'Administrador Sistema', '2026-07-24 03:37:05', 'crear', 'Producto', 15, 'Registro de nuevo producto: P0000015 - Huevos Calera Frescos 15 unidades (Stock: 12, Mín: 20, Precio: S/ 9.5)'),
(6, 'Administrador Sistema', '2026-07-24 03:37:33', 'crear', 'Producto', 16, 'Registro de nuevo producto: P0000016 - Yogurt Batido Gloria Fresa 1kg (Stock: 45, Mín: 15, Precio: S/ 6.5)'),
(7, 'Administrador Sistema', '2026-07-24 03:37:58', 'crear', 'Producto', 17, 'Registro de nuevo producto: P0000017 - Arroz Costeño Extra 5kg (Stock: 60, Mín: 20, Precio: S/ 23.9)'),
(8, 'Administrador Sistema', '2026-07-24 03:38:21', 'crear', 'Producto', 18, 'Registro de nuevo producto: P0000018 - Aceite Primor Premium 1L (Stock: 8, Mín: 25, Precio: S/ 9.8)'),
(9, 'Administrador Sistema', '2026-07-24 03:39:13', 'crear', 'Producto', 19, 'Registro de nuevo producto: P0000019 - Mayonesa Alacena Doypack 500g (Stock: 50, Mín: 15, Precio: S/ 9.2)'),
(10, 'Administrador Sistema', '2026-07-24 03:39:37', 'crear', 'Producto', 20, 'Registro de nuevo producto: P0000020 - Atún Campomar Trozos en Aceite 170g (Stock: 0, Mín: 25, Precio: S/ 5.9)'),
(11, 'Administrador Sistema', '2026-07-24 03:49:10', 'modificar', 'Producto', 20, 'Modificación de producto P0000020 (ID: 20) - Nombre: Atún Campomar Trozos en Aceite 170g, Categoría: Abarrotes, Precio: S/ 5.9, Stock: 0, Stock Mín: 25'),
(12, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 21, 'Registro de nuevo producto: P0000021 - Gaseosa Sprite 1.5L (Stock: 70, Mín: 20, Precio: S/ 5.8)'),
(13, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 22, 'Registro de nuevo producto: P0000022 - Gaseosa Fanta Naranja 3L (Stock: 12, Mín: 25, Precio: S/ 10.5)'),
(14, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 23, 'Registro de nuevo producto: P0000023 - Agua Cielo Sin Gas 625ml (Stock: 180, Mín: 50, Precio: S/ 1.2)'),
(15, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 24, 'Registro de nuevo producto: P0000024 - Rehidratante Sporade Fresa 500ml (Stock: 95, Mín: 30, Precio: S/ 2.5)'),
(16, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 25, 'Registro de nuevo producto: P0000025 - Cerveza Cristal Sixpack 355ml (Stock: 35, Mín: 10, Precio: S/ 22.9)'),
(17, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 26, 'Registro de nuevo producto: P0000026 - Jugo Tampico Citrus Punch 1L (Stock: 0, Mín: 20, Precio: S/ 3.5)'),
(18, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 27, 'Registro de nuevo producto: P0000027 - Salchicha Huachana San Fernando 250g (Stock: 22, Mín: 10, Precio: S/ 6.9)'),
(19, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 28, 'Registro de nuevo producto: P0000028 - Chorizo Cocktail Otto Kunz 250g (Stock: 5, Mín: 12, Precio: S/ 11.5)'),
(20, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 29, 'Registro de nuevo producto: P0000029 - Tocino Ahumado Braedt 150g (Stock: 18, Mín: 8, Precio: S/ 13.9)'),
(21, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 30, 'Registro de nuevo producto: P0000030 - Crema Dental Kolynos 75ml (Stock: 105, Mín: 30, Precio: S/ 3.2)'),
(22, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 31, 'Registro de nuevo producto: P0000031 - Jabón Líquido Aval Glicerina 400ml (Stock: 40, Mín: 15, Precio: S/ 7.5)'),
(23, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 32, 'Registro de nuevo producto: P0000032 - Desodorante Rexona Odorono 60g (Stock: 0, Mín: 10, Precio: S/ 8.9)'),
(24, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 33, 'Registro de nuevo producto: P0000033 - Plátano de Seda Seleccionado 1kg (Stock: 65, Mín: 20, Precio: S/ 3.5)'),
(25, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 34, 'Registro de nuevo producto: P0000034 - Papa Amarilla Tumbay 1kg (Stock: 85, Mín: 25, Precio: S/ 4.2)'),
(26, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 35, 'Registro de nuevo producto: P0000035 - Huevos La Calera Pardos 30u (Stock: 28, Mín: 10, Precio: S/ 18.5)'),
(27, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 36, 'Registro de nuevo producto: P0000036 - Mantequilla Laive con Sal 200g (Stock: 9, Mín: 15, Precio: S/ 7.2)'),
(28, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 37, 'Registro de nuevo producto: P0000037 - Queso Fresco Laive 500g (Stock: 30, Mín: 10, Precio: S/ 14.5)'),
(29, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 38, 'Registro de nuevo producto: P0000038 - Limpiador Poett Primavera 900ml (Stock: 75, Mín: 20, Precio: S/ 4.9)'),
(30, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 39, 'Registro de nuevo producto: P0000039 - Lejía Clorox Original 1L (Stock: 120, Mín: 30, Precio: S/ 3.8)'),
(31, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 40, 'Registro de nuevo producto: P0000040 - Panetón D\\\'Onofrio Caja 880g (Stock: 45, Mín: 15, Precio: S/ 26.9)'),
(32, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 41, 'Registro de nuevo producto: P0000041 - Chocolates Sublime Extragrande 100g (Stock: 160, Mín: 40, Precio: S/ 4.5)'),
(33, 'Administrador', '2026-07-24 03:54:13', 'crear', 'Producto', 42, 'Registro de nuevo producto: P0000042 - Papas Pringles Original 124g (Stock: 14, Mín: 20, Precio: S/ 9.9)'),
(34, 'Administrador', '2026-07-24 03:54:30', 'crear', 'Producto', 43, 'Registro de nuevo producto: P0000043 - Detergente Opal Ultra 2.6kg (Stock: 50, Mín: 15, Precio: S/ 24.9)'),
(35, 'Administrador', '2026-07-24 03:54:30', 'crear', 'Producto', 44, 'Registro de nuevo producto: P0000044 - Suavizante Suavitel Fresca Primavera 850ml (Stock: 30, Mín: 10, Precio: S/ 8.5)'),
(36, 'Administrador', '2026-07-24 03:54:30', 'crear', 'Producto', 45, 'Registro de nuevo producto: P0000045 - Té Herbi Manzanilla 25u (Stock: 100, Mín: 25, Precio: S/ 2.8)'),
(37, 'Administrador', '2026-07-24 03:54:30', 'crear', 'Producto', 46, 'Registro de nuevo producto: P0000046 - Café Nescafé Kirma 190g (Stock: 45, Mín: 15, Precio: S/ 14.9)'),
(38, 'Administrador', '2026-07-24 03:54:30', 'crear', 'Producto', 47, 'Registro de nuevo producto: P0000047 - Cereal Angel Flakes 500g (Stock: 60, Mín: 20, Precio: S/ 9.9)'),
(39, 'Administrador', '2026-07-24 03:54:30', 'crear', 'Producto', 48, 'Registro de nuevo producto: P0000048 - Gaseosa Concordia Piña 3L (Stock: 25, Mín: 10, Precio: S/ 7.5)'),
(40, 'Administrador', '2026-07-24 03:54:30', 'crear', 'Producto', 49, 'Registro de nuevo producto: P0000049 - Cerveza Cusqueña Dorada Sixpack 355ml (Stock: 20, Mín: 8, Precio: S/ 27.5)'),
(41, 'Administrador', '2026-07-24 03:54:30', 'crear', 'Producto', 50, 'Registro de nuevo producto: P0000050 - Jamón de Pavita San Fernando 200g (Stock: 15, Mín: 8, Precio: S/ 9.2)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidad_medida` varchar(20) NOT NULL DEFAULT 'unidad',
  `categoria` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 10,
  `estado` varchar(20) DEFAULT 'Normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `codigo`, `nombre`, `descripcion`, `unidad_medida`, `categoria`, `precio`, `stock`, `stock_minimo`, `estado`) VALUES
(1, 'P0000001', 'Leche Gloria Evaporada Entera 400g', NULL, 'unidad', 'Lácteos y Huevos', 4.20, 150, 50, 'Normal'),
(2, 'P0000002', 'Leche Gloria Evaporada Entera 170g', NULL, 'unidad', 'Lácteos y Huevos', 2.50, 20, 30, 'Stock Bajo'),
(3, 'P0000003', 'Mayonesa Alacena Doypack 500g', NULL, 'unidad', 'Abarrotes', 8.90, 60, 20, 'Normal'),
(4, 'P0000004', 'Mayonesa Alacena Doypack 100g', NULL, 'unidad', 'Abarrotes', 2.20, 140, 50, 'Normal'),
(5, 'P0000005', 'Mayonesa Laive Receta Casera 750g', NULL, 'unidad', 'Abarrotes', 12.50, 5, 15, 'Stock Bajo'),
(6, 'P0000006', 'Mayonesa Laive Receta Casera 500g', NULL, 'unidad', 'Abarrotes', 8.50, 0, 20, 'Agotado'),
(7, 'P0000007', 'Arroz Costeño Extra 5kg', NULL, 'unidad', 'Abarrotes', 23.50, 45, 15, 'Normal'),
(8, 'P0000008', 'Arroz Costeño Extra 1kg', NULL, 'unidad', 'Abarrotes', 4.80, 10, 15, 'Stock Bajo'),
(9, 'P0000009', 'Aceite Primor Premium 1L', NULL, 'unidad', 'Abarrotes', 9.50, 120, 40, 'Normal'),
(10, 'P0000010', 'Detergente Bolívar Matic 2.6kg', NULL, 'unidad', 'Limpieza y Hogar', 28.90, 40, 15, 'Normal'),
(11, 'P0000011', 'Detergente Bolívar Matic 800g', NULL, 'unidad', 'Limpieza y Hogar', 9.50, 0, 30, 'Agotado'),
(12, 'P0000012', 'Inca Kola Descartable 3L', NULL, 'unidad', 'Bebidas', 11.50, 85, 25, 'Normal'),
(13, 'P0000013', 'Gaseosa Coca-Cola 1.5L &#039;Zero&#039; &amp;lt;script&amp;gt;alert(&#039;test&#039;)&amp;lt;/scrasd', 'Bebida efervescente sin azúcar', 'unidad', 'Bebidas', 7.20, 50, 15, 'Desactivado'),
(14, 'P0000014', 'Leche Gloria Evaporada Entera 400g', '', 'unidad', 'Lácteos y Huevos', 4.20, 120, 40, 'Activo'),
(15, 'P0000015', 'Huevos Calera Frescos 15 unidades', '', 'paquete', 'Lácteos y Huevos', 9.50, 12, 20, 'Activo'),
(16, 'P0000016', 'Yogurt Batido Gloria Fresa 1kg', '', 'unidad', 'Lácteos y Huevos', 6.50, 45, 15, 'Activo'),
(17, 'P0000017', 'Arroz Costeño Extra 5kg', '', 'bolsa', 'Abarrotes', 23.90, 60, 20, 'Activo'),
(18, 'P0000018', 'Aceite Primor Premium 1L', '', 'unidad', 'Abarrotes', 9.80, 8, 25, 'Activo'),
(19, 'P0000019', 'Mayonesa Alacena Doypack 500g', '', 'unidad', 'Abarrotes', 9.20, 50, 15, 'Activo'),
(20, 'P0000020', 'Atún Campomar Trozos en Aceite 170g', '', 'pack', 'Abarrotes', 5.90, 0, 25, 'Activo'),
(21, 'P0000021', 'Gaseosa Sprite 1.5L', 'Bebida gasificada sabor lima limón.', 'unidad', 'Bebidas', 5.80, 70, 20, 'Activo'),
(22, 'P0000022', 'Gaseosa Fanta Naranja 3L', 'Bebida gasificada sabor naranja.', 'unidad', 'Bebidas', 10.50, 12, 25, 'Activo'),
(23, 'P0000023', 'Agua Cielo Sin Gas 625ml', 'Agua de mesa purificada.', 'unidad', 'Bebidas', 1.20, 180, 50, 'Activo'),
(24, 'P0000024', 'Rehidratante Sporade Fresa 500ml', 'Bebida rehidratante para deportistas.', 'unidad', 'Bebidas', 2.50, 95, 30, 'Activo'),
(25, 'P0000025', 'Cerveza Cristal Sixpack 355ml', 'Pack de 6 latas de cerveza.', 'pack', 'Bebidas', 22.90, 35, 10, 'Activo'),
(26, 'P0000026', 'Jugo Tampico Citrus Punch 1L', 'Bebida refrescante sabor a cítricos.', 'unidad', 'Bebidas', 3.50, 0, 20, 'Activo'),
(27, 'P0000027', 'Salchicha Huachana San Fernando 250g', 'Salchicha tradicional huachana.', 'paquete', 'Carnes y Embutidos', 6.90, 22, 10, 'Activo'),
(28, 'P0000028', 'Chorizo Cocktail Otto Kunz 250g', 'Chorizo fino de cerdo.', 'paquete', 'Carnes y Embutidos', 11.50, 5, 12, 'Activo'),
(29, 'P0000029', 'Tocino Ahumado Braedt 150g', 'Tocino ahumado en tajadas.', 'paquete', 'Carnes y Embutidos', 13.90, 18, 8, 'Activo'),
(30, 'P0000030', 'Crema Dental Kolynos 75ml', 'Crema dental con flúor.', 'unidad', 'Cuidado Personal', 3.20, 105, 30, 'Activo'),
(31, 'P0000031', 'Jabón Líquido Aval Glicerina 400ml', 'Jabón líquido para manos.', 'unidad', 'Cuidado Personal', 7.50, 40, 15, 'Activo'),
(32, 'P0000032', 'Desodorante Rexona Odorono 60g', 'Desodorante antitranspirante.', 'unidad', 'Cuidado Personal', 8.90, 0, 10, 'Activo'),
(33, 'P0000033', 'Plátano de Seda Seleccionado 1kg', 'Fruta fresca de estación.', 'bolsa', 'Frutas y Verduras', 3.50, 65, 20, 'Activo'),
(34, 'P0000034', 'Papa Amarilla Tumbay 1kg', 'Papas nativas seleccionadas.', 'bolsa', 'Frutas y Verduras', 4.20, 85, 25, 'Activo'),
(35, 'P0000035', 'Huevos La Calera Pardos 30u', 'Bandeja familiar de 30 huevos.', 'caja', 'Lácteos y Huevos', 18.50, 28, 10, 'Activo'),
(36, 'P0000036', 'Mantequilla Laive con Sal 200g', 'Mantequilla pasteurizada.', 'unidad', 'Lácteos y Huevos', 7.20, 9, 15, 'Activo'),
(37, 'P0000037', 'Queso Fresco Laive 500g', 'Queso fresco empaquetado.', 'paquete', 'Lácteos y Huevos', 14.50, 30, 10, 'Activo'),
(38, 'P0000038', 'Limpiador Poett Primavera 900ml', 'Limpiador desinfectante aromático.', 'unidad', 'Limpieza y Hogar', 4.90, 75, 20, 'Activo'),
(39, 'P0000039', 'Lejía Clorox Original 1L', 'Desinfectante concentrado.', 'unidad', 'Limpieza y Hogar', 3.80, 120, 30, 'Activo'),
(40, 'P0000040', 'Panetón D\'Onofrio Caja 880g', 'Panetón tradicional con pasas y frutas.', 'caja', 'Panadería y Pastelería', 26.90, 45, 15, 'Activo'),
(41, 'P0000041', 'Chocolates Sublime Extragrande 100g', 'Chocolate con maní.', 'unidad', 'Snacks y Golosinas', 4.50, 160, 40, 'Activo'),
(42, 'P0000042', 'Papas Pringles Original 124g', 'Snack de papa deshidratada en lata.', 'unidad', 'Snacks y Golosinas', 9.90, 14, 20, 'Activo'),
(43, 'P0000043', 'Detergente Opal Ultra 2.6kg', 'Detergente en polvo ultra blanqueador.', 'bolsa', 'Limpieza y Hogar', 24.90, 50, 15, 'Activo'),
(44, 'P0000044', 'Suavizante Suavitel Fresca Primavera 850ml', 'Suavizante para ropa concentrado.', 'unidad', 'Limpieza y Hogar', 8.50, 30, 10, 'Activo'),
(45, 'P0000045', 'Té Herbi Manzanilla 25u', 'Infusión natural de manzanilla.', 'paquete', 'Abarrotes', 2.80, 100, 25, 'Activo'),
(46, 'P0000046', 'Café Nescafé Kirma 190g', 'Café instantáneo en lata.', 'unidad', 'Abarrotes', 14.90, 45, 15, 'Activo'),
(47, 'P0000047', 'Cereal Angel Flakes 500g', 'Cereal de maíz tostado.', 'caja', 'Abarrotes', 9.90, 60, 20, 'Activo'),
(48, 'P0000048', 'Gaseosa Concordia Piña 3L', 'Bebida gasificada sabor piña.', 'unidad', 'Bebidas', 7.50, 25, 10, 'Activo'),
(49, 'P0000049', 'Cerveza Cusqueña Dorada Sixpack 355ml', 'Cerveza premium tipo lager.', 'pack', 'Bebidas', 27.50, 20, 8, 'Activo'),
(50, 'P0000050', 'Jamón de Pavita San Fernando 200g', 'Jamón de pechuga de pavo.', 'paquete', 'Carnes y Embutidos', 9.20, 15, 8, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasenia` varchar(255) NOT NULL,
  `rol` varchar(30) NOT NULL,
  `estado` varchar(20) DEFAULT 'Activo',
  `intentos_fallidos` int(11) DEFAULT 0,
  `session_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `usuario`, `contrasenia`, `rol`, `estado`, `intentos_fallidos`, `session_id`) VALUES
(1, 'Administrador Sistema', 'admin', 'admin123', 'Administrador', 'Activo', 0, 'p89aj44mkr7casc4kgfhsf6eab'),
(2, 'Jose Bocanegra Valverde', 'admin2', 'admin123', 'Administrador', 'Activo', 0, NULL),
(3, 'Sebastian Salazar', 'admin3', 'admin123', 'Administrador', 'Activo', 0, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`idAuditoria`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `idAuditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
