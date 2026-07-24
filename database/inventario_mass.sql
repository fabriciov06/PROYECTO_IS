-- ========================================================
-- 1. CREACIÓN DE LA BASE DE DATOS
-- ========================================================
CREATE DATABASE IF NOT EXISTS inventario_mass;
USE inventario_mass;

-- Limpieza de tablas (Orden estricto por Foreign Keys)
DROP TABLE IF EXISTS auditoria;
DROP TABLE IF EXISTS reclamos;
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
-- 2. CREACIÓN DE TABLAS (ALCANCE ACTUAL)
-- ========================================================

-- Tabla de Auditoría (RNF-18)
CREATE TABLE auditoria (
    idAuditoria INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(100) NOT NULL,
    fecha DATETIME NOT NULL,
    tipo ENUM('crear','modificar','desactivar') NOT NULL,
    entidad VARCHAR(50) NOT NULL,
    idRegistro INT NOT NULL,
    detalle TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Usuarios (Módulo Login)
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasenia VARCHAR(255) NOT NULL,
    rol VARCHAR(30) NOT NULL,
    estado VARCHAR(20) DEFAULT 'Activo',
    intentos_fallidos INT DEFAULT 0,
    session_id VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Productos (Catálogo)
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    unidad_medida VARCHAR(20) NOT NULL DEFAULT 'unidad',
    categoria VARCHAR(50) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 10,
    estado VARCHAR(20) DEFAULT 'Normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 3. INSERCIÓN DE DATOS INICIALES (MOCK DATA)
-- ========================================================

-- Credenciales Administrativas de acceso
INSERT INTO usuarios (nombre, usuario, contrasenia, rol, estado) VALUES 
('Administrador Sistema', 'admin', 'admin123', 'Administrador', 'Activo');

-- Catálogo representativo de Tiendas Mass Perú
INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, estado) VALUES
('P0000001', 'Leche Gloria Evaporada Entera 400g', 'Lácteos y Huevos', 4.20, 150, 50, 'Normal'),
('P0000002', 'Leche Gloria Evaporada Entera 170g', 'Lácteos y Huevos', 2.50, 20, 30, 'Stock Bajo'),
('P0000003', 'Mayonesa Alacena Doypack 500g', 'Abarrotes', 8.90, 60, 20, 'Normal'),
('P0000004', 'Mayonesa Alacena Doypack 100g', 'Abarrotes', 2.20, 140, 50, 'Normal'),
('P0000005', 'Mayonesa Laive Receta Casera 750g', 'Abarrotes', 12.50, 5, 15, 'Stock Bajo'),
('P0000006', 'Mayonesa Laive Receta Casera 500g', 'Abarrotes', 8.50, 0, 20, 'Agotado'),
('P0000007', 'Arroz Costeño Extra 5kg', 'Abarrotes', 23.50, 45, 15, 'Normal'),
('P0000008', 'Arroz Costeño Extra 1kg', 'Abarrotes', 4.80, 10, 15, 'Stock Bajo'),
('P0000009', 'Aceite Primor Premium 1L', 'Abarrotes', 9.50, 120, 40, 'Normal'),
('P0000010', 'Detergente Bolívar Matic 2.6kg', 'Limpieza y Hogar', 28.90, 40, 15, 'Normal'),
('P0000011', 'Detergente Bolívar Matic 800g', 'Limpieza y Hogar', 9.50, 0, 30, 'Agotado'),
('P0000012', 'Inca Kola Descartable 3L', 'Bebidas', 11.50, 85, 25, 'Normal');