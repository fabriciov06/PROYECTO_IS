-- 1. Creación de la Base de Datos
CREATE DATABASE IF NOT EXISTS inventario_mass;
USE inventario_mass;

-- 2. Tabla de Usuarios (para el Login)
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasenia VARCHAR(255) NOT NULL,
    rol VARCHAR(30) NOT NULL -- 'Administrador', 'Supervisor', etc.
) ENGINE=InnoDB;

-- 3. Tabla de Productos (El núcleo del inventario)
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 10,
    estado VARCHAR(20) DEFAULT 'Normal' -- 'Normal', 'Stock Bajo', 'Agotado'
) ENGINE=InnoDB;

-- 4. Tabla de Movimientos de Stock (Entradas y Salidas)
CREATE TABLE IF NOT EXISTS movimientos_stock (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_movimiento ENUM('ENTRADA', 'SALIDA') NOT NULL,
    cantidad INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Tabla de Historial de Precios (Para el control de variaciones)
CREATE TABLE IF NOT EXISTS historial_precios (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    precio_anterior DECIMAL(10, 2) NOT NULL,
    precio_nuevo DECIMAL(10, 2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabla de Alertas Automáticas
CREATE TABLE IF NOT EXISTS alertas (
    id_alerta INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_alerta VARCHAR(50) NOT NULL, -- 'Stock Bajo', 'Agotado', 'Cambio de Precio'
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado_alerta VARCHAR(20) DEFAULT 'Pendiente', -- 'Pendiente', 'Atendida'
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;


-- ========================================================
-- INSERCIÓN DE DATOS DE PRUEBA (Para pruebas iniciales)
-- ========================================================

-- Insertar un usuario de prueba (Contraseña simple para desarrollo)
INSERT INTO usuarios (nombre, usuario, contrasenia, rol) 
VALUES ('Administrador Sistema', 'admin', 'admin123', 'Administrador');

-- Insertar los productos base que coinciden con la interfaz php
INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, estado) VALUES
('P001', 'Leche', 'Lácteos', 4.50, 5, 10, 'Stock Bajo'),
('P002', 'Arroz', 'Abarrotes', 3.00, 20, 10, 'Normal'),
('P003', 'Aceite', 'Abarrotes', 8.50, 0, 10, 'Agotado');