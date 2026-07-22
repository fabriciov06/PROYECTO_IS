-- ========================================================
-- SISTEMA MASS - SCRIPT COMPLETO DE BASE DE DATOS (UML 100%)
-- Universidad Privada Antenor Orrego (UPAO)
-- ========================================================

CREATE DATABASE IF NOT EXISTS inventario_mass;
USE inventario_mass;

-- Limpieza de tablas (Orden estricto por Foreign Keys)
DROP TABLE IF EXISTS auditoria;
DROP TABLE IF EXISTS reclamos;
DROP TABLE IF EXISTS detalle_informe;
DROP TABLE IF EXISTS informe_recepcion;
DROP TABLE IF EXISTS facturas;
DROP TABLE IF EXISTS guia_productos;
DROP TABLE IF EXISTS detalle_solicitud;
DROP TABLE IF EXISTS solicitudes_compra;
DROP TABLE IF EXISTS alertas;
DROP TABLE IF EXISTS movimientos_inventario;
DROP TABLE IF EXISTS movimientos_stock;
DROP TABLE IF EXISTS lotes;
DROP TABLE IF EXISTS inventario;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS catalogo;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS proveedores;
DROP TABLE IF EXISTS supervisores;
DROP TABLE IF EXISTS operadores;
DROP TABLE IF EXISTS administradores;
DROP TABLE IF EXISTS usuarios;

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

-- ========================================================
-- 1. TABLAS DE USUARIOS Y ROLES (HERENCIA UML)
-- ========================================================

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL DEFAULT '',
    email VARCHAR(150) NOT NULL DEFAULT '',
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasenia VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) DEFAULT '',
    rol ENUM('administrador', 'supervisor', 'operador', 'proveedor') NOT NULL,
    estado TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE administradores (
    id_usuario INT PRIMARY KEY,
    nivel_acceso INT NOT NULL DEFAULT 1,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE operadores (
    id_usuario INT PRIMARY KEY,
    turno VARCHAR(50) NOT NULL DEFAULT 'Mañana',
    sede VARCHAR(100) NOT NULL DEFAULT 'Sede Principal',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE supervisores (
    id_usuario INT PRIMARY KEY,
    sede VARCHAR(100) NOT NULL DEFAULT 'Sede Principal',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE proveedores (
    id_usuario INT PRIMARY KEY,
    ruc VARCHAR(20) NOT NULL UNIQUE,
    razon_social VARCHAR(150) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    contacto VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 2. TABLAS DE CATÁLOGO E INVENTARIO
-- ========================================================

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE catalogo (
    id_catalogo INT AUTO_INCREMENT PRIMARY KEY,
    sede VARCHAR(100) NOT NULL,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    unidad_medida VARCHAR(20) NOT NULL DEFAULT 'unidad',
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 10,
    estado VARCHAR(20) DEFAULT 'Normal',
    categoria VARCHAR(50) NOT NULL,
    id_categoria INT NULL,
    id_catalogo INT NULL,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL,
    FOREIGN KEY (id_catalogo) REFERENCES catalogo(id_catalogo) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inventario (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    sede VARCHAR(100) NOT NULL,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    usuario_responsable VARCHAR(100) NOT NULL,
    motivo VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE lotes (
    id_lote INT AUTO_INCREMENT PRIMARY KEY,
    codigo_lote VARCHAR(50) NOT NULL UNIQUE,
    fecha_ingreso DATE NOT NULL,
    fecha_salida_estimada DATE NOT NULL,
    cantidad INT NOT NULL,
    estado VARCHAR(30) DEFAULT 'Disponible',
    id_producto INT NOT NULL,
    id_inventario INT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
    FOREIGN KEY (id_inventario) REFERENCES inventario(id_inventario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE movimientos_inventario (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_movimiento ENUM('Entrada', 'Salida', 'Ajuste') NOT NULL,
    cantidad INT NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responsable VARCHAR(100) NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    sede VARCHAR(100) DEFAULT 'Sede Central',
    id_inventario INT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
    FOREIGN KEY (id_inventario) REFERENCES inventario(id_inventario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE movimientos_stock LIKE movimientos_inventario;

CREATE TABLE alertas (
    id_alerta INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_alerta VARCHAR(50) NOT NULL,
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leida TINYINT(1) DEFAULT 0,
    destinatario VARCHAR(100) DEFAULT 'Administrador',
    estado_alerta VARCHAR(20) DEFAULT 'Pendiente',
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 3. SOLICITUDES DE COMPRA, GUÍAS Y FACTURAS
-- ========================================================

CREATE TABLE solicitudes_compra (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    codigo_solicitud VARCHAR(20) NOT NULL UNIQUE,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    supervisor VARCHAR(100) NOT NULL,
    justificacion TEXT NOT NULL,
    estado ENUM('Pendiente', 'Aprobada', 'Rechazada') DEFAULT 'Pendiente',
    fecha_evaluacion DATETIME NULL,
    motivo_rechazo TEXT NULL,
    sede VARCHAR(100) DEFAULT 'Sede Central'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE detalle_solicitud (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    id_producto INT NOT NULL,
    producto VARCHAR(150),
    cantidad INT NOT NULL,
    precio_estimado DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes_compra(id_solicitud) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE guia_productos (
    id_guia INT AUTO_INCREMENT PRIMARY KEY,
    codigo_guia VARCHAR(20) NOT NULL UNIQUE,
    proveedor VARCHAR(100) NOT NULL,
    fecha_emision DATE NOT NULL,
    fecha_entrega DATE NULL,
    archivo_adjunto VARCHAR(255) NULL,
    estado VARCHAR(30) DEFAULT 'Registrada',
    id_solicitud INT NULL,
    id_proveedor INT NULL,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes_compra(id_solicitud) ON DELETE SET NULL,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(50) NOT NULL UNIQUE,
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    monto_total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'pagada', 'vencida', 'rechazada') DEFAULT 'pendiente',
    archivo_adjunto VARCHAR(255) NULL,
    id_guia INT NULL,
    id_solicitud INT NULL,
    FOREIGN KEY (id_guia) REFERENCES guia_productos(id_guia) ON DELETE SET NULL,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes_compra(id_solicitud) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 4. RECEPCIÓN DE INFORMES Y RECLAMOS
-- ========================================================

CREATE TABLE informe_recepcion (
    id_informe INT AUTO_INCREMENT PRIMARY KEY,
    codigo_informe VARCHAR(20) NOT NULL UNIQUE,
    id_guia INT NOT NULL,
    fecha_recepcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    operador VARCHAR(100) NOT NULL,
    observaciones TEXT,
    estado ENUM('Pendiente', 'Conforme', 'Con Incidencias') DEFAULT 'Pendiente',
    fotos_adjuntas TEXT NULL,
    FOREIGN KEY (id_guia) REFERENCES guia_productos(id_guia) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE detalle_informe (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_informe INT NOT NULL,
    id_producto INT NOT NULL,
    producto_esperado VARCHAR(150),
    cantidad_esperada INT NOT NULL DEFAULT 0,
    cantidad_recibida INT NOT NULL,
    estado_producto ENUM('Buen estado', 'Dañado', 'Faltante') DEFAULT 'Buen estado',
    observacion TEXT,
    FOREIGN KEY (id_informe) REFERENCES informe_recepcion(id_informe) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reclamos (
    id_reclamo INT AUTO_INCREMENT PRIMARY KEY,
    id_informe INT NOT NULL,
    motivo VARCHAR(150) DEFAULT 'Incidencia en recepción',
    descripcion_incidencia TEXT NOT NULL,
    estado ENUM('Pendiente', 'En atención', 'Cerrado') DEFAULT 'Pendiente',
    solucion_proveedor TEXT NULL,
    archivo_solucion VARCHAR(255) NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATETIME NULL,
    plazo_limite DATE NULL,
    id_factura INT NULL,
    FOREIGN KEY (id_informe) REFERENCES informe_recepcion(id_informe) ON DELETE CASCADE,
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 5. INSERCIÓN DE DATOS DE PRUEBA (MOCK DATA)
-- ========================================================

INSERT INTO usuarios (nombre, apellido, email, usuario, contrasenia, rol, estado) VALUES 
('Fabricio', 'Vargas', 'admin@mass.pe', 'admin', 'admin123', 'administrador', 1),
('Carlos', 'López', 'supervisor1@mass.pe', 'sup_mañana', 'sup123', 'supervisor', 1),
('Juan', 'Pérez', 'operador1@mass.pe', 'op_almacen', 'op123', 'operador', 1),
('Gloria S.A.', 'Distribuciones', 'contacto@gloria.com.pe', 'proveedor_gloria', 'prov123', 'proveedor', 1);

INSERT INTO administradores (id_usuario, nivel_acceso) VALUES (1, 1);
INSERT INTO supervisores (id_usuario, sede) VALUES (2, 'Sede Trujillo Centro');
INSERT INTO operadores (id_usuario, turno, sede) VALUES (3, 'Mañana', 'Sede Trujillo Centro');
INSERT INTO proveedores (id_usuario, ruc, razon_social, direccion, contacto) VALUES 
(4, '20100013345', 'Leche Gloria S.A.', 'Av. República de Panamá 2461, Lima', 'contacto@gloria.com.pe');

INSERT INTO categorias (nombre, descripcion) VALUES
('Lácteos', 'Productos lácteos y derivados'),
('Abarrotes', 'Productos alimenticios envasados no perecibles'),
('Limpieza', 'Productos de aseo del hogar y personal'),
('Bebidas', 'Gaseosas, jugos y aguas');

INSERT INTO catalogo (sede) VALUES ('Sede Trujillo Centro');

INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, estado, id_categoria, id_catalogo) VALUES
('P0000001', 'Leche Gloria Evaporada Entera 400g', 'Lácteos', 4.20, 150, 50, 'Normal', 1, 1),
('P0000002', 'Leche Gloria Evaporada Entera 170g', 'Lácteos', 2.50, 20, 30, 'Stock Bajo', 1, 1),
('P0000003', 'Mayonesa Alacena Doypack 500g', 'Abarrotes', 8.90, 60, 20, 'Normal', 2, 1),
('P0000004', 'Mayonesa Alacena Doypack 100g', 'Abarrotes', 2.20, 140, 50, 'Normal', 2, 1),
('P0000005', 'Mayonesa Laive Receta Casera 750g', 'Abarrotes', 12.50, 5, 15, 'Stock Bajo', 2, 1),
('P0000006', 'Mayonesa Laive Receta Casera 500g', 'Abarrotes', 8.50, 0, 20, 'Agotado', 2, 1),
('P0000007', 'Arroz Costeño Extra 5kg', 'Abarrotes', 23.50, 45, 15, 'Normal', 2, 1),
('P0000008', 'Arroz Costeño Extra 1kg', 'Abarrotes', 4.80, 10, 15, 'Stock Bajo', 2, 1),
('P0000009', 'Aceite Primor Premium 1L', 'Abarrotes', 9.50, 120, 40, 'Normal', 2, 1),
('P0000010', 'Detergente Bolívar Matic 2.6kg', 'Limpieza', 28.90, 40, 15, 'Normal', 3, 1),
('P0000011', 'Detergente Bolívar Matic 800g', 'Limpieza', 9.50, 0, 30, 'Agotado', 3, 1),
('P0000012', 'Inca Kola Descartable 3L', 'Bebidas', 11.50, 85, 25, 'Normal', 4, 1);

INSERT INTO inventario (sede, usuario_responsable, motivo) VALUES 
('Sede Trujillo Centro', 'Administrador Sistema', 'Inventario general mensual');

INSERT INTO movimientos_inventario (id_producto, tipo_movimiento, cantidad, fecha_hora, responsable, motivo) VALUES 
(1, 'Entrada', 100, '2026-07-06 08:30:00', 'Operador Almacén', 'Guía G-1045 (Recepción)'),
(2, 'Entrada', 50, '2026-07-06 08:35:00', 'Operador Almacén', 'Guía G-1045 (Recepción)'),
(3, 'Salida', 15, '2026-07-06 14:15:00', 'Sistema POS', 'Ventas del Turno Mañana'),
(7, 'Salida', 5, '2026-07-06 14:15:00', 'Sistema POS', 'Ventas del Turno Mañana'),
(6, 'Salida', 10, '2026-07-05 19:45:00', 'Sistema POS', 'Ventas del Turno Tarde (Agotado)'),
(11, 'Ajuste', 2, '2026-07-05 11:00:00', 'Administrador (FV)', 'Bolsas rotas en góndola'),
(12, 'Entrada', 150, '2026-07-04 09:45:00', 'Operador Almacén', 'Guía G-1042 (Arca Continental)'),
(5, 'Ajuste', 1, '2026-07-03 16:20:00', 'Administrador (FV)', 'Mayonesa vencida retirada');

INSERT INTO movimientos_stock (id_producto, tipo_movimiento, cantidad, fecha_hora, responsable, motivo) 
SELECT id_producto, tipo_movimiento, cantidad, fecha_hora, responsable, motivo FROM movimientos_inventario;

INSERT INTO solicitudes_compra (codigo_solicitud, supervisor, justificacion, estado) VALUES
('REQ-1001', 'Supervisor Turno Mañana', 'Reposición urgente de lácteos por fin de semana largo', 'Pendiente'),
('REQ-1002', 'Supervisor Turno Tarde', 'Faltante de mayonesas en góndola principal', 'Pendiente'),
('REQ-1003', 'Supervisor Turno Mañana', 'Pedido quincenal de abarrotes y limpieza', 'Aprobada'),
('REQ-1004', 'Supervisor Turno Tarde', 'Doble pedido por error de conteo', 'Rechazada');

INSERT INTO detalle_solicitud (id_solicitud, id_producto, producto, cantidad, precio_estimado) VALUES
(1, 2, 'Leche Gloria Evaporada Entera 170g', 50, 2.50),
(2, 6, 'Mayonesa Laive Receta Casera 500g', 20, 8.50),
(2, 5, 'Mayonesa Laive Receta Casera 750g', 10, 12.50),
(3, 8, 'Arroz Costeño Extra 1kg', 30, 4.80),
(3, 11, 'Detergente Bolívar Matic 800g', 40, 9.50);

INSERT INTO guia_productos (codigo_guia, proveedor, fecha_emision, fecha_entrega, estado, id_solicitud, id_proveedor) VALUES
('G-1045', 'Gloria S.A.', '2026-07-06', '2026-07-06', 'Entregada', 1, 4),
('G-1046', 'Alicorp S.A.A.', '2026-07-07', '2026-07-07', 'Entregada', 2, NULL),
('G-1047', 'Costeño S.A.C.', '2026-07-08', '2026-07-08', 'Entregada', 3, NULL),
('G-1048', 'Arca Continental', '2026-07-08', '2026-07-08', 'Entregada', NULL, NULL),
('G-1049', 'Procter & Gamble', '2026-07-09', '2026-07-09', 'Pendiente', NULL, NULL);

INSERT INTO informe_recepcion (codigo_informe, id_guia, fecha_recepcion, operador, observaciones, estado) VALUES
('INF-2001', 1, '2026-07-06 09:00:00', 'Operador Almacén (Turno Mañana)', 'Recepción sin novedades. Todo conforme a la guía.', 'Pendiente'),
('INF-2002', 2, '2026-07-07 10:30:00', 'Operador Almacén (Turno Tarde)', 'Cajas de mayonesa aplastadas en el transporte y faltaron botellas de aceite.', 'Pendiente'),
('INF-2003', 3, '2026-07-08 08:15:00', 'Operador Almacén (Turno Mañana)', 'Llegó el pedido de arroz completo y en buen estado.', 'Conforme'),
('INF-2004', 4, '2026-07-08 14:20:00', 'Operador Almacén (Turno Tarde)', 'Varias botellas de Inca Kola reventadas en el fondo del camión.', 'Con Incidencias'),
('INF-2005', 5, '2026-07-09 09:45:00', 'Operador Almacén (Turno Mañana)', 'Faltaron bolsas de detergente según la guía.', 'Pendiente');

INSERT INTO detalle_informe (id_informe, id_producto, producto_esperado, cantidad_esperada, cantidad_recibida, estado_producto) VALUES
(1, 1, 'Leche Gloria Evaporada Entera 400g', 100, 100, 'Buen estado'),
(1, 2, 'Leche Gloria Evaporada Entera 170g', 50, 50, 'Buen estado'),
(2, 3, 'Mayonesa Alacena Doypack 500g', 50, 50, 'Buen estado'),
(2, 4, 'Mayonesa Alacena Doypack 100g', 150, 130, 'Faltante'), 
(2, 9, 'Aceite Primor Premium 1L', 120, 118, 'Dañado'),
(3, 7, 'Arroz Costeño Extra 5kg', 45, 45, 'Buen estado'), 
(3, 8, 'Arroz Costeño Extra 1kg', 100, 100, 'Buen estado'),
(4, 12, 'Inca Kola Descartable 3L', 85, 80, 'Buen estado'), 
(4, 12, 'Inca Kola Descartable 3L', 5, 5, 'Dañado'),
(5, 10, 'Detergente Bolívar Matic 2.6kg', 40, 40, 'Buen estado'), 
(5, 11, 'Detergente Bolívar Matic 800g', 40, 20, 'Faltante');

INSERT INTO reclamos (id_informe, motivo, descripcion_incidencia, estado, solucion_proveedor) VALUES
(2, 'Daños y Faltantes', 'Botellas de Inca Kola reventadas y faltante de mayonesa', 'Pendiente', NULL),
(5, 'Faltante en entrega', 'Faltaron bolsas de detergente según guía', 'En atención', 'Proveedor se compromete a enviar faltante mañana');
