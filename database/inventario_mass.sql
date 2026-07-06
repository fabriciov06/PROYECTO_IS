-- ========================================================
-- 1. CREACIÓN DE LA BASE DE DATOS
-- ========================================================
CREATE DATABASE IF NOT EXISTS inventario_mass;
USE inventario_mass;

-- Limpieza de tablas (Orden estricto por llaves foráneas)
DROP TABLE IF EXISTS detalle_informe;
DROP TABLE IF EXISTS informe_recepcion;
DROP TABLE IF EXISTS guia_productos;
DROP TABLE IF EXISTS detalle_solicitud;
DROP TABLE IF EXISTS solicitudes_compra;
DROP TABLE IF EXISTS alertas;
DROP TABLE IF EXISTS historial_precios;
DROP TABLE IF EXISTS movimientos_stock;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS usuarios;

-- ========================================================
-- 2. CREACIÓN DE TABLAS
-- ========================================================

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasenia VARCHAR(255) NOT NULL,
    rol VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

-- Tabla de Productos (Catálogo)
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 10,
    estado VARCHAR(20) DEFAULT 'Normal'
) ENGINE=InnoDB;

-- Tabla de Movimientos de Stock
CREATE TABLE movimientos_stock (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_movimiento ENUM('Entrada', 'Salida', 'Ajuste') NOT NULL,
    cantidad INT NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responsable VARCHAR(100) NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de Historial de Precios
CREATE TABLE historial_precios (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    precio_anterior DECIMAL(10, 2) NOT NULL,
    precio_nuevo DECIMAL(10, 2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de Alertas Automáticas
CREATE TABLE alertas (
    id_alerta INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_alerta VARCHAR(50) NOT NULL, 
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado_alerta VARCHAR(20) DEFAULT 'Pendiente',
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de Solicitudes de Compra (CU-10)
CREATE TABLE solicitudes_compra (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    codigo_solicitud VARCHAR(20) NOT NULL UNIQUE,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    supervisor VARCHAR(100) NOT NULL,
    justificacion TEXT NOT NULL,
    estado ENUM('Pendiente', 'Aprobada', 'Rechazada') DEFAULT 'Pendiente',
    fecha_evaluacion DATETIME NULL,
    motivo_rechazo TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE detalle_solicitud (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes_compra(id_solicitud) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de Guías de Productos (CU-08)
CREATE TABLE guia_productos (
    id_guia INT AUTO_INCREMENT PRIMARY KEY,
    codigo_guia VARCHAR(20) NOT NULL UNIQUE,
    proveedor VARCHAR(100) NOT NULL,
    fecha_emision DATE NOT NULL
) ENGINE=InnoDB;

-- Tabla de Informes de Recepción (CU-08)
CREATE TABLE informe_recepcion (
    id_informe INT AUTO_INCREMENT PRIMARY KEY,
    codigo_informe VARCHAR(20) NOT NULL UNIQUE,
    id_guia INT NOT NULL,
    fecha_recepcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    operador VARCHAR(100) NOT NULL,
    observaciones TEXT,
    estado ENUM('Pendiente', 'Conforme', 'Con Incidencias') DEFAULT 'Pendiente',
    FOREIGN KEY (id_guia) REFERENCES guia_productos(id_guia) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de Detalles del Informe (CU-08)
CREATE TABLE detalle_informe (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_informe INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad_recibida INT NOT NULL,
    estado_producto ENUM('Buen estado', 'Dañado', 'Faltante') DEFAULT 'Buen estado',
    FOREIGN KEY (id_informe) REFERENCES informe_recepcion(id_informe) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;


-- ========================================================
-- 3. INSERCIÓN DE DATOS REALES (MOCK DATA)
-- ========================================================

INSERT INTO usuarios (nombre, usuario, contrasenia, rol) VALUES 
('Administrador Sistema', 'admin', 'admin123', 'Administrador');

INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, estado) VALUES
('P001', 'Leche Gloria Evaporada Entera 400g', 'Lácteos', 4.20, 150, 50, 'Normal'),
('P002', 'Leche Gloria Evaporada Entera 170g', 'Lácteos', 2.50, 20, 30, 'Stock Bajo'),
('P003', 'Mayonesa Alacena Doypack 500g', 'Abarrotes', 8.90, 60, 20, 'Normal'),
('P004', 'Mayonesa Alacena Doypack 100g', 'Abarrotes', 2.20, 140, 50, 'Normal'),
('P005', 'Mayonesa Laive Receta Casera 750g', 'Abarrotes', 12.50, 5, 15, 'Stock Bajo'),
('P006', 'Mayonesa Laive Receta Casera 500g', 'Abarrotes', 8.50, 0, 20, 'Agotado'),
('P007', 'Arroz Costeño Extra 5kg', 'Abarrotes', 23.50, 45, 15, 'Normal'),
('P008', 'Arroz Costeño Extra 1kg', 'Abarrotes', 4.80, 10, 15, 'Stock Bajo'),
('P009', 'Aceite Primor Premium 1L', 'Abarrotes', 9.50, 120, 40, 'Normal'),
('P010', 'Detergente Bolívar Matic 2.6kg', 'Limpieza', 28.90, 40, 15, 'Normal'),
('P011', 'Detergente Bolívar Matic 800g', 'Limpieza', 9.50, 0, 30, 'Agotado'),
('P012', 'Inca Kola Descartable 3L', 'Bebidas', 11.50, 85, 25, 'Normal');

INSERT INTO movimientos_stock (id_producto, tipo_movimiento, cantidad, fecha_hora, responsable, motivo) VALUES 
(1, 'Entrada', 100, '2026-07-06 08:30:00', 'Operador Almacén', 'Guía G-1045 (Recepción)'),
(2, 'Entrada', 50, '2026-07-06 08:35:00', 'Operador Almacén', 'Guía G-1045 (Recepción)'),
(3, 'Salida', 15, '2026-07-06 14:15:00', 'Sistema POS', 'Ventas del Turno Mañana'),
(7, 'Salida', 5, '2026-07-06 14:15:00', 'Sistema POS', 'Ventas del Turno Mañana'),
(6, 'Salida', 10, '2026-07-05 19:45:00', 'Sistema POS', 'Ventas del Turno Tarde (Agotado)'),
(11, 'Ajuste', 2, '2026-07-05 11:00:00', 'Administrador (FV)', 'Bolsas rotas en góndola'),
(12, 'Entrada', 150, '2026-07-04 09:45:00', 'Operador Almacén', 'Guía G-1042 (Arca Continental)'),
(5, 'Ajuste', 1, '2026-07-03 16:20:00', 'Administrador (FV)', 'Mayonesa vencida retirada');

INSERT INTO solicitudes_compra (codigo_solicitud, supervisor, justificacion, estado) VALUES
('REQ-1001', 'Supervisor Turno Mañana', 'Reposición urgente de lácteos por fin de semana largo', 'Pendiente'),
('REQ-1002', 'Supervisor Turno Tarde', 'Faltante de mayonesas en góndola principal', 'Pendiente'),
('REQ-1003', 'Supervisor Turno Mañana', 'Pedido quincenal de abarrotes y limpieza', 'Aprobada'),
('REQ-1004', 'Supervisor Turno Tarde', 'Doble pedido por error de conteo', 'Rechazada');

INSERT INTO detalle_solicitud (id_solicitud, id_producto, cantidad) VALUES
(1, 2, 50),
(2, 6, 20),
(2, 5, 10),
(3, 8, 30),
(3, 11, 40);

INSERT INTO guia_productos (codigo_guia, proveedor, fecha_emision) VALUES
('G-1045', 'Gloria S.A.', '2026-07-06'),
('G-1046', 'Alicorp S.A.A.', '2026-07-07'),
('G-1047', 'Costeño S.A.C.', '2026-07-08'),
('G-1048', 'Arca Continental', '2026-07-08'),
('G-1049', 'Procter & Gamble', '2026-07-09');

INSERT INTO informe_recepcion (codigo_informe, id_guia, fecha_recepcion, operador, observaciones, estado) VALUES
('INF-2001', 1, '2026-07-06 09:00:00', 'Operador Almacén (Turno Mañana)', 'Recepción sin novedades. Todo conforme a la guía.', 'Pendiente'),
('INF-2002', 2, '2026-07-07 10:30:00', 'Operador Almacén (Turno Tarde)', 'Cajas de mayonesa aplastadas en el transporte y faltaron botellas de aceite.', 'Pendiente'),
('INF-2003', 3, '2026-07-08 08:15:00', 'Operador Almacén (Turno Mañana)', 'Llegó el pedido de arroz completo y en buen estado.', 'Conforme'),
('INF-2004', 4, '2026-07-08 14:20:00', 'Operador Almacén (Turno Tarde)', 'Varias botellas de Inca Kola reventadas en el fondo del camión.', 'Con Incidencias'),
('INF-2005', 5, '2026-07-09 09:45:00', 'Operador Almacén (Turno Mañana)', 'Faltaron bolsas de detergente según la guía.', 'Pendiente');

INSERT INTO detalle_informe (id_informe, id_producto, cantidad_recibida, estado_producto) VALUES
(1, 1, 100, 'Buen estado'),
(1, 2, 50, 'Buen estado'),
(2, 3, 50, 'Buen estado'),
(2, 4, 130, 'Faltante'), 
(2, 9, 118, 'Dañado'),
(3, 7, 45, 'Buen estado'), 
(3, 8, 100, 'Buen estado'),
(4, 12, 80, 'Buen estado'), 
(4, 12, 5, 'Dañado'),
(5, 10, 40, 'Buen estado'), 
(5, 11, 20, 'Faltante');    